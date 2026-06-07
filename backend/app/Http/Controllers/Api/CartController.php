<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductVariant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    private function getOrCreateCart(): Cart
    {
        return Cart::firstOrCreate(['user_id' => Auth::id()]);
    }

    public function index(): JsonResponse
    {
        $cart = $this->getOrCreateCart();

        $cart->load('items.variant.product');

        $items = $cart->items->map(fn($item) => [
            'id'       => $item->id,
            'quantity' => $item->quantity,
            'variant'  => [
                'id'    => $item->variant->id,
                'sku'   => $item->variant->sku,
                'price' => $item->variant->price,
            ],
            'product'  => [
                'id'   => $item->variant->product->id,
                'name' => $item->variant->product->name,
                'slug' => $item->variant->product->slug,
            ],
            'subtotal' => $item->quantity * $item->variant->price,
        ]);

        return response()->json([
            'items' => $items,
            'total' => $items->sum('subtotal'),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'variant_id' => 'required|exists:product_variants,id',
            'quantity'   => 'required|integer|min:1',
        ]);

        $variant = ProductVariant::findOrFail($request->variant_id);

        if ($variant->stock < $request->quantity) {
            return response()->json(['message' => 'Số lượng tồn kho không đủ.'], 422);
        }

        $cart = $this->getOrCreateCart();

        $item = $cart->items()->where('variant_id', $request->variant_id)->first();

        if ($item) {
            $newQty = $item->quantity + $request->quantity;

            if ($variant->stock < $newQty) {
                return response()->json(['message' => 'Số lượng tồn kho không đủ.'], 422);
            }

            $item->update(['quantity' => $newQty]);
        } else {
            $item = $cart->items()->create([
                'variant_id' => $request->variant_id,
                'quantity'   => $request->quantity,
            ]);
        }

        return response()->json(['message' => 'Đã thêm vào giỏ hàng.', 'item_id' => $item->id], 201);
    }

    public function update(Request $request, CartItem $item): JsonResponse
    {
        if ($item->cart->user_id !== Auth::id()) {
            return response()->json(['message' => 'Không có quyền.'], 403);
        }

        $request->validate(['quantity' => 'required|integer|min:1']);

        if ($item->variant->stock < $request->quantity) {
            return response()->json(['message' => 'Số lượng tồn kho không đủ.'], 422);
        }

        $item->update(['quantity' => $request->quantity]);

        return response()->json(['message' => 'Đã cập nhật.']);
    }

    public function destroy(CartItem $item): JsonResponse
    {
        if ($item->cart->user_id !== Auth::id()) {
            return response()->json(['message' => 'Không có quyền.'], 403);
        }

        $item->delete();

        return response()->json(['message' => 'Đã xóa khỏi giỏ hàng.']);
    }
}
