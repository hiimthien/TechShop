<?php

declare(strict_types=1);

namespace App\Services;

use App\Jobs\AutoCancelUnpaidOrderJob;
use App\Jobs\SendOrderConfirmationJob;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderService
{
    public function __construct(
        protected CouponService $couponService
    ) {}

    /**
     * Create an order from a user's cart safely with atomic locks, coupon validation, and background jobs.
     */
    public function createFromCart(Cart $cart, array $data): Order
    {
        $lockKey = 'checkout_user_lock_' . $cart->user_id;

        // Redis / Cache Atomic Lock: Chống click đúp và race condition trong vòng 10 giây
        $lock = Cache::lock($lockKey, 10);

        try {
            return $lock->block(4, function () use ($cart, $data) {
                $order = DB::transaction(function () use ($cart, $data) {
                    $cart->load('items.variant');

                    if ($cart->items->isEmpty()) {
                        throw new \Exception('Giỏ hàng của bạn đang trống.');
                    }

                    // Kiểm tra tồn kho từng sản phẩm trước khi tạo đơn
                    foreach ($cart->items as $item) {
                        $variant = $item->variant;
                        if (!$variant || $variant->stock < $item->quantity) {
                            $skuName = $variant ? $variant->sku : "ID {$item->variant_id}";
                            throw new \Exception("Sản phẩm ({$skuName}) không đủ số lượng tồn kho.");
                        }
                    }

                    $subtotal = (float) $cart->items->sum(fn($item) => $item->variant->price * $item->quantity);
                    $shippingFee = (float) ($data['shipping_fee'] ?? 0);
                    $couponId = null;
                    $discountAmount = 0.0;

                    // Xác thực mã giảm giá (Coupon) an toàn ở tầng Server nếu có
                    if (!empty($data['coupon_code'])) {
                        $couponResult = $this->couponService->validateAndCalculate(
                            (string) $data['coupon_code'],
                            $subtotal
                        );
                        $coupon = $couponResult['coupon'];
                        $discountAmount = (float) $couponResult['discount_amount'];
                        $couponId = $coupon->id;

                        // Tăng số lần sử dụng coupon nguyên tử
                        $coupon->increment('used_count');
                    }

                    // Tính tổng tiền sau khi trừ coupon
                    $totalPrice = max(0, $subtotal - $discountAmount) + $shippingFee;
                    $paymentMethod = $data['payment_method'] ?? 'cod';
                    $paymentStatus = $paymentMethod === 'cod' ? 'unpaid' : 'pending';

                    $order = Order::create([
                        'user_id'         => $cart->user_id,
                        'shipping_fee'    => $shippingFee,
                        'address_id'      => $data['address_id'],
                        'total_price'     => $totalPrice,
                        'discount_amount' => $discountAmount,
                        'coupon_id'       => $couponId,
                        'status'          => 'pending',
                        'payment_method'  => $paymentMethod,
                        'payment_status'  => $paymentStatus,
                    ]);

                    foreach ($cart->items as $item) {
                        OrderItem::create([
                            'order_id'   => $order->id,
                            'variant_id' => $item->variant_id,
                            'quantity'   => $item->quantity,
                            'price'      => $item->variant->price,
                        ]);
                    }

                    // Trừ tồn kho nguyên tử (atomic conditional decrement) chống race condition đa người dùng
                    foreach ($cart->items as $item) {
                        $affected = DB::table('product_variants')
                            ->where('id', $item->variant_id)
                            ->where('stock', '>=', $item->quantity)
                            ->decrement('stock', $item->quantity);

                        if ($affected === 0) {
                            $skuName = $item->variant ? $item->variant->sku : "ID {$item->variant_id}";
                            throw new \Exception("Sản phẩm ({$skuName}) vừa hết hàng do có đơn hàng khác thanh toán trước.");
                        }
                    }

                    // Xóa giỏ hàng sau khi tạo đơn thành công
                    $cart->delete();

                    return $order;
                });

                // Đẩy tác vụ gửi email & thông báo vào Queue
                SendOrderConfirmationJob::dispatch($order->id);

                // Đẩy Delayed Job: Tự động hủy đơn sau 30 phút nếu chưa được duyệt hoặc chưa thanh toán
                AutoCancelUnpaidOrderJob::dispatch($order->id)->delay(now()->addMinutes(30));

                return $order;
            });
        } catch (LockTimeoutException $e) {
            Log::warning("[OrderService] Lock timeout for user {$cart->user_id}: " . $e->getMessage());
            throw new \Exception('Hệ thống đang xử lý yêu cầu đặt hàng của bạn, vui lòng không nhấn liên tục.');
        }
    }
}
