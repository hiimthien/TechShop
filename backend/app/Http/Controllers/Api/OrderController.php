<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Cart;
use App\Models\Order;
use App\Services\NotificationService;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function __construct(
        protected OrderService $orderService
    ) {}

    public function index(): JsonResponse
    {
        $orders = Order::where('user_id', Auth::id())
            ->with(['items.variant.product.images', 'address', 'coupon'])
            ->latest()
            ->get();

        return $this->successResponse(OrderResource::collection($orders));
    }

    public function show(int $id): JsonResponse
    {
        $order = Order::where('id', $id)
            ->where('user_id', Auth::id())
            ->with(['items.variant.product.images', 'address', 'coupon'])
            ->firstOrFail();

        return $this->successResponse(new OrderResource($order));
    }

    public function cancel(int $id): JsonResponse
    {
        $order = Order::where('id', $id)
            ->where('user_id', Auth::id())
            ->with('items.variant')
            ->firstOrFail();

        if ($order->status !== 'pending') {
            return $this->errorResponse(
                'Chỉ có thể hủy đơn hàng khi đơn hàng đang ở trạng thái chờ duyệt.',
                422
            );
        }

        DB::transaction(function () use ($order) {
            $order->update(['status' => 'cancelled']);

            // Atomic inventory restock on cancellation
            foreach ($order->items as $item) {
                $item->variant?->increment('stock', $item->quantity);
            }
        });

        // Notify Admins about the cancellation
        try {
            $orderCode = '#TS-' . str_pad((string) $order->id, 6, '0', STR_PAD_LEFT);
            NotificationService::sendToAdmins('order_cancelled', [
                'title'        => 'Đơn hàng đã hủy ' . $orderCode,
                'message'      => 'Khách hàng ' . ($order->user?->name ?: 'Khách') . ' vừa hủy đơn hàng ' . $orderCode,
                'category'     => 'order',
                'link'         => '/admin/orders',
                'icon'         => 'cancel',
                'reference_id' => $order->id,
            ]);
        } catch (\Throwable $e) {
            Log::error('Lỗi khi gửi thông báo hủy đơn: ' . $e->getMessage());
        }

        return $this->successResponse(null, 'Đã hủy đơn hàng thành công.');
    }

    public function store(StoreOrderRequest $request): JsonResponse
    {
        $cart = Cart::where('user_id', Auth::id())->first();

        if (!$cart || $cart->items()->doesntExist()) {
            return $this->errorResponse('Giỏ hàng của bạn đang trống.', 422);
        }

        try {
            $order = $this->orderService->createFromCart(
                $cart,
                $request->validated()
            );

            return response()->json([
                'status'   => true,
                'message'  => 'Đơn hàng đã tạo thành công',
                'order_id' => $order->id,
            ], 201);
        } catch (\Throwable $e) {
            return $this->errorResponse($e->getMessage(), 422);
        }
    }
}
