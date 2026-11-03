<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Order;
use App\Services\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AutoCancelUnpaidOrderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;

    public function __construct(
        public int $orderId
    ) {}

    /**
     * Tự động hủy đơn hàng và hoàn lại tồn kho nếu đơn vẫn ở trạng thái 'pending' sau thời gian quy định.
     */
    public function handle(): void
    {
        $order = Order::with(['items.variant', 'user'])->find($this->orderId);

        if (!$order) {
            Log::info("[AutoCancelUnpaidOrderJob] Order #{$this->orderId} not found, skipping.");
            return;
        }

        // Chỉ tự động hủy nếu đơn vẫn ở trạng thái pending (chưa được duyệt hoặc chưa thanh toán)
        if ($order->status !== 'pending') {
            Log::info("[AutoCancelUnpaidOrderJob] Order #{$this->orderId} is currently '{$order->status}'. Auto-cancel skipped.");
            return;
        }

        $orderCode = '#TS-' . str_pad((string) $order->id, 6, '0', STR_PAD_LEFT);

        DB::transaction(function () use ($order, $orderCode) {
            $order->update(['status' => 'cancelled']);

            // Atomic inventory restock: hoàn lại tồn kho cho từng sản phẩm
            foreach ($order->items as $item) {
                if ($item->variant) {
                    $item->variant->increment('stock', $item->quantity);
                    Log::info("[AutoCancelUnpaidOrderJob] Restocked +{$item->quantity} for variant SKU: {$item->variant->sku}");
                }
            }
        });

        Log::info("[AutoCancelUnpaidOrderJob] Order {$orderCode} automatically cancelled due to expiration. Inventory restocked.");

        // Thông báo cho khách hàng
        try {
            if ($order->user) {
                NotificationService::sendToUser($order->user, 'order_auto_cancelled', [
                    'title'        => 'Đơn hàng tự động hủy ' . $orderCode,
                    'message'      => 'Đơn hàng ' . $orderCode . ' đã tự động hủy do quá hạn thanh toán/xác nhận. Tồn kho sản phẩm đã được hoàn lại.',
                    'category'     => 'order',
                    'link'         => '/account',
                    'icon'         => 'event_busy',
                    'reference_id' => $order->id,
                ]);
            }
        } catch (\Throwable $e) {
            Log::warning("[AutoCancelUnpaidOrderJob] Failed to notify user: " . $e->getMessage());
        }

        // Thông báo cho Quản trị viên
        try {
            NotificationService::sendToAdmins('order_auto_cancelled', [
                'title'        => 'Đơn quá hạn tự hủy ' . $orderCode,
                'message'      => 'Hệ thống đã tự động hủy đơn hàng ' . $orderCode . ' và hoàn lại tồn kho.',
                'category'     => 'order',
                'link'         => '/admin/orders',
                'icon'         => 'cancel',
                'reference_id' => $order->id,
            ]);
        } catch (\Throwable $e) {
            Log::warning("[AutoCancelUnpaidOrderJob] Failed to notify admin: " . $e->getMessage());
        }
    }
}

