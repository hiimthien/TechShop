<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CouponController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Coupon::query();

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where('code', 'like', "%{$search}%");
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $coupons = $query->latest()->paginate($request->input('per_page', 15));

        return response()->json([
            'status' => true,
            'data' => $coupons,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:coupons,code',
            'type' => 'required|in:fixed,percentage',
            'value' => 'required|numeric|min:0',
            'min_order_value' => 'nullable|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'expires_at' => 'nullable|date',
            'is_active' => 'boolean',
        ]);

        $validated['code'] = strtoupper($validated['code']);
        $coupon = Coupon::create($validated);

        // Gửi thông báo có Voucher mới đến khách hàng
        try {
            if ($coupon->is_active) {
                $discountText = $coupon->type === 'percentage'
                    ? "giảm {$coupon->value}%"
                    : 'giảm ' . number_format((float) $coupon->value) . 'đ';

                NotificationService::broadcastToCustomers('coupon_created', [
                    'title' => 'Voucher mới: ' . $coupon->code,
                    'message' => "TechShop tặng bạn voucher {$coupon->code} {$discountText}. Hãy áp dụng ngay khi thanh toán!",
                    'category' => 'coupon',
                    'link' => '/account',
                    'icon' => 'local_activity',
                    'reference_id' => $coupon->id,
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('Lỗi khi gửi thông báo voucher: ' . $e->getMessage());
        }

        return response()->json([
            'status' => true,
            'message' => 'Tạo mã giảm giá thành công',
            'data' => $coupon,
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $coupon = Coupon::withCount('orders')->findOrFail($id);

        return response()->json([
            'status' => true,
            'data' => $coupon,
        ]);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $coupon = Coupon::findOrFail($id);

        $validated = $request->validate([
            'code' => "required|string|max:50|unique:coupons,code,{$id}",
            'type' => 'required|in:fixed,percentage',
            'value' => 'required|numeric|min:0',
            'min_order_value' => 'nullable|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'expires_at' => 'nullable|date',
            'is_active' => 'boolean',
        ]);

        $validated['code'] = strtoupper($validated['code']);
        $coupon->update($validated);

        return response()->json([
            'status' => true,
            'message' => 'Cập nhật mã giảm giá thành công',
            'data' => $coupon,
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $coupon = Coupon::findOrFail($id);
        $coupon->delete();

        return response()->json([
            'status' => true,
            'message' => 'Đã xóa mã giảm giá',
        ]);
    }

    public function toggle(string $id): JsonResponse
    {
        $coupon = Coupon::findOrFail($id);
        $coupon->is_active = !$coupon->is_active;
        $coupon->save();

        return response()->json([
            'status' => true,
            'message' => $coupon->is_active ? 'Đã kích hoạt mã giảm giá' : 'Đã tạm dừng mã giảm giá',
            'data' => $coupon,
        ]);
    }
}

