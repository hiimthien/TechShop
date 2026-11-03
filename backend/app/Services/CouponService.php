<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Coupon;
use Exception;

class CouponService
{
    /**
     * Validate a coupon code server-side and calculate the exact discount amount.
     *
     * @param string $code
     * @param float $orderTotal (subtotal of cart items before shipping)
     * @return array{coupon: Coupon, discount_amount: float}
     * @throws Exception
     */
    public function validateAndCalculate(string $code, float $orderTotal): array
    {
        $normalizedCode = strtoupper(trim($code));

        $coupon = Coupon::where('code', $normalizedCode)
            ->where('is_active', true)
            ->first();

        if (!$coupon) {
            throw new Exception('Mã khuyến mãi không tồn tại hoặc đã bị vô hiệu hóa.');
        }

        if ($coupon->expires_at && now()->toDateString() > $coupon->expires_at) {
            throw new Exception('Mã khuyến mãi đã hết hạn sử dụng.');
        }

        if ($coupon->usage_limit && $coupon->used_count >= $coupon->usage_limit) {
            throw new Exception('Mã khuyến mãi đã hết lượt sử dụng.');
        }

        if ($coupon->min_order_value && $orderTotal < (float) $coupon->min_order_value) {
            throw new Exception(
                'Đơn hàng tối thiểu ' . number_format((float) $coupon->min_order_value, 0, ',', '.') . 'đ mới có thể áp dụng mã này.'
            );
        }

        $discount = 0.0;
        if ($coupon->type === 'percentage') {
            $discount = ($orderTotal * (float) $coupon->value) / 100;
            if ($coupon->max_discount && $discount > (float) $coupon->max_discount) {
                $discount = (float) $coupon->max_discount;
            }
        } else {
            $discount = min((float) $coupon->value, $orderTotal);
        }

        return [
            'coupon' => $coupon,
            'discount_amount' => round($discount, 2),
        ];
    }
}

