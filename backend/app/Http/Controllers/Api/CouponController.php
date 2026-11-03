<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    /**
     * Lấy danh sách voucher / coupon khả dụng cho khách hàng
     */
    public function index(): JsonResponse
    {
        $coupons = Coupon::where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>=', now()->toDateString());
            })
            ->get();

        // Tự động tạo một số mã khuyến mãi mặc định nếu chưa có
        if ($coupons->isEmpty()) {
            $defaultCoupons = [
                [
                    'code' => 'TECHSHOP50',
                    'type' => 'fixed',
                    'value' => 50000,
                    'min_order_value' => 500000,
                    'max_discount' => 50000,
                    'usage_limit' => 1000,
                    'used_count' => 84,
                    'expires_at' => now()->addDays(30)->toDateString(),
                    'is_active' => true,
                ],
                [
                    'code' => 'TECHSHOP100',
                    'type' => 'fixed',
                    'value' => 100000,
                    'min_order_value' => 2000000,
                    'max_discount' => 100000,
                    'usage_limit' => 500,
                    'used_count' => 142,
                    'expires_at' => now()->addDays(45)->toDateString(),
                    'is_active' => true,
                ],
                [
                    'code' => 'FREESHIP30',
                    'type' => 'fixed',
                    'value' => 30000,
                    'min_order_value' => 300000,
                    'max_discount' => 30000,
                    'usage_limit' => 2000,
                    'used_count' => 530,
                    'expires_at' => now()->addDays(60)->toDateString(),
                    'is_active' => true,
                ],
                [
                    'code' => 'VIPMEMBER10',
                    'type' => 'percentage',
                    'value' => 10,
                    'min_order_value' => 5000000,
                    'max_discount' => 500000,
                    'usage_limit' => 300,
                    'used_count' => 27,
                    'expires_at' => now()->addDays(60)->toDateString(),
                    'is_active' => true,
                ],
            ];

            foreach ($defaultCoupons as $item) {
                Coupon::create($item);
            }

            $coupons = Coupon::where('is_active', true)->get();
        }

        return response()->json([
            'status' => true,
            'total' => $coupons->count(),
            'data' => $coupons,
        ]);
    }

    /**
     * Kiểm tra tính hợp lệ của mã voucher
     */
    public function check(Request $request, \App\Services\CouponService $couponService): JsonResponse
    {
        $request->validate([
            'code' => 'required|string',
            'order_total' => 'required|numeric|min:0',
        ]);

        try {
            $result = $couponService->validateAndCalculate(
                (string) $request->input('code'),
                (float) $request->input('order_total')
            );

            return response()->json([
                'status' => true,
                'message' => 'Áp dụng mã giảm giá thành công!',
                'data' => [
                    'coupon' => $result['coupon'],
                    'discount_amount' => $result['discount_amount'],
                    'final_total' => max(0, (float) $request->input('order_total') - $result['discount_amount']),
                ],
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}

