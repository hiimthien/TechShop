<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Cart;
use App\Models\Order;
use App\Services\OrderService;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
    public function __construct(private OrderService $orderService) {}

    public function index(): JsonResponse
    {
        $orders = Order::where('user_id', Auth::id())->get();

        return response()->json([
            'status' => true,
            'data' => OrderResource::collection($orders)
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $order = Order::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $order->load('items.variant');

        return response()->json([
            'status' => true,
            'data' => new  OrderResource($order)
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'address_id' => 'required|exists:addresses,id',
            'shipping_fee'   => 'required|numeric|min:0',
        ]);

        $cart = Cart::where('user_id', Auth::id())->first();

        if (!$cart || $cart->items()->doesntExist()) {
            return response()->json(['message' => 'Giỏ hàng trống.'], 422);
        }

        $order = $this->orderService->createFromCart($cart, $request->only('address_id', 'shipping_fee'));

        return response()->json([
            'status' => true,
            'message' => 'Đơn hàng đã tạo thành công'
        ]);
    }
}
