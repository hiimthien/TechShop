<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Mail\OrderSuccessMail;
use App\Models\Order;
use App\Services\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendOrderConfirmationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * Exponential backoff delays in seconds between retries.
     */
    public array $backoff = [5, 15, 30];

    public function __construct(
        public int $orderId
    ) {}

    /**
     * Execute the job: send email and dispatch notifications asynchronously.
     */
    public function handle(): void
    {
        $order = Order::with(['user', 'items.variant.product', 'address', 'coupon'])->find($this->orderId);

        if (!$order) {
            Log::warning("[SendOrderConfirmationJob] Order #{$this->orderId} not found.");
            return;
        }

        $orderCode = '#TS-' . str_pad((string) $order->id, 6, '0', STR_PAD_LEFT);

        // 1. Gửi email xác nhận đặt hàng (bất đồng bộ qua queue)
        try {
            if ($order->user?->email) {
                Mail::to($order->user->email)->send(new OrderSuccessMail($order));
                Log::info("[SendOrderConfirmationJob] Sent confirmation email for order {$orderCode} to {$order->user->email}");
            }
        } catch (\Throwable $e) {
            Log::error("[SendOrderConfirmationJob] Failed sending email for {$orderCode}: " . $e->getMessage());
            // Throw exception so Laravel Queue retries this job according to $tries & $backoff
            throw $e;
        }

        // 2. Báo thông báo in-app cho Quản trị viên (Admin)
        try {
            NotificationService::sendToAdmins('order_created', [
                'title'        => 'Đơn hàng mới ' . $orderCode,
                'message'      => 'Khách hàng ' . ($order->user?->name ?: 'Khách') . ' vừa đặt đơn trị giá ' . number_format((float) $order->total_price) . 'đ',
                'category'     => 'order',
                'link'         => '/admin/orders',
                'icon'         => 'shopping_bag',
                'reference_id' => $order->id,
            ]);
        } catch (\Throwable $e) {
            Log::warning("[SendOrderConfirmationJob] Error notifying admins for {$orderCode}: " . $e->getMessage());
        }

        // 3. Báo thông báo in-app cho Khách hàng
        try {
            if ($order->user) {
                NotificationService::sendToUser($order->user, 'order_created', [
                    'title'        => 'Đặt hàng thành công ' . $orderCode,
                    'message'      => 'Đơn hàng trị giá ' . number_format((float) $order->total_price) . 'đ của bạn đã được tiếp nhận và đang chờ duyệt.',
                    'category'     => 'order',
                    'link'         => '/account',
                    'icon'         => 'check_circle',
                    'reference_id' => $order->id,
                ]);
            }
        } catch (\Throwable $e) {
            Log::warning("[SendOrderConfirmationJob] Error notifying customer for {$orderCode}: " . $e->getMessage());
        }
    }
}

