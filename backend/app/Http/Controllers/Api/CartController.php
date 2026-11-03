<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddToCartRequest;
use App\Models\CartItem;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function __construct(
        protected CartService $cartService
    ) {}

    public function index(): JsonResponse
    {
        $cartData = $this->cartService->getCartContents(Auth::id());
        return response()->json($cartData);
    }

    public function store(AddToCartRequest $request): JsonResponse
    {
        $item = $this->cartService->addItem(
            Auth::id(),
            (int) $request->variant_id,
            (int) $request->quantity
        );

        return $this->successResponse(
            ['item_id' => $item->id],
            'Đã thêm sản phẩm vào giỏ hàng thành công.',
            201
        );
    }

    public function update(Request $request, CartItem $item): JsonResponse
    {
        $request->validate(['quantity' => 'required|integer|min:1']);

        $updated = $this->cartService->updateItemQuantity(
            Auth::id(),
            $item->id,
            (int) $request->quantity
        );

        return $this->successResponse(
            ['item_id' => $updated->id, 'quantity' => $updated->quantity],
            'Đã cập nhật số lượng sản phẩm trong giỏ hàng.'
        );
    }

    public function destroy(CartItem $item): JsonResponse
    {
        $this->cartService->removeItem(Auth::id(), $item->id);

        return $this->successResponse(
            null,
            'Đã xóa sản phẩm khỏi giỏ hàng.'
        );
    }
}
