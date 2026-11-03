<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductVariant;
use Illuminate\Validation\ValidationException;

class CartService
{
    /**
     * Get or create a cart instance for the given user.
     */
    public function getOrCreateCart(int $userId): Cart
    {
        return Cart::firstOrCreate(['user_id' => $userId]);
    }

    /**
     * Retrieve full structured cart contents for presentation.
     */
    public function getCartContents(int $userId): array
    {
        $cart = $this->getOrCreateCart($userId);
        $cart->load('items.variant.product');

        $items = $cart->items->map(fn(CartItem $item) => [
            'id'       => $item->id,
            'quantity' => $item->quantity,
            'variant'  => [
                'id'    => $item->variant?->id,
                'sku'   => $item->variant?->sku,
                'price' => $item->variant?->price,
                'stock' => $item->variant?->stock,
            ],
            'product'  => [
                'id'        => $item->variant?->product?->id,
                'name'      => $item->variant?->product?->name,
                'slug'      => $item->variant?->product?->slug,
                'thumbnail' => $item->variant?->image_url ?: $item->variant?->product?->thumbnail,
            ],
            'subtotal' => $item->quantity * ($item->variant?->price ?? 0),
        ]);

        return [
            'items' => $items,
            'total' => $items->sum('subtotal'),
        ];
    }

    /**
     * Add a variant item to the cart with stock verification.
     */
    public function addItem(int $userId, int $variantId, int $quantity): CartItem
    {
        $variant = ProductVariant::findOrFail($variantId);

        if ($variant->stock < $quantity) {
            throw ValidationException::withMessages([
                'quantity' => 'Số lượng tồn kho không đủ (chỉ còn ' . $variant->stock . ' sản phẩm).',
            ]);
        }

        $cart = $this->getOrCreateCart($userId);
        $item = $cart->items()->where('variant_id', $variantId)->first();

        if ($item) {
            $newQty = $item->quantity + $quantity;

            if ($variant->stock < $newQty) {
                throw ValidationException::withMessages([
                    'quantity' => 'Tổng số lượng vượt quá tồn kho hiện có (' . $variant->stock . ').',
                ]);
            }

            $item->update(['quantity' => $newQty]);
            return $item;
        }

        return $cart->items()->create([
            'variant_id' => $variantId,
            'quantity'   => $quantity,
        ]);
    }

    /**
     * Update quantity of a specific cart item with ownership & stock checks.
     */
    public function updateItemQuantity(int $userId, int $itemId, int $quantity): CartItem
    {
        $item = CartItem::with(['cart', 'variant'])->findOrFail($itemId);

        if ($item->cart->user_id !== $userId) {
            abort(403, 'Bạn không có quyền chỉnh sửa mục này.');
        }

        if ($item->variant && $item->variant->stock < $quantity) {
            throw ValidationException::withMessages([
                'quantity' => 'Số lượng tồn kho không đủ (chỉ còn ' . $item->variant->stock . ' sản phẩm).',
            ]);
        }

        $item->update(['quantity' => $quantity]);
        return $item;
    }

    /**
     * Remove an item from the cart.
     */
    public function removeItem(int $userId, int $itemId): void
    {
        $item = CartItem::with('cart')->findOrFail($itemId);

        if ($item->cart->user_id !== $userId) {
            abort(403, 'Bạn không có quyền xóa mục này.');
        }

        $item->delete();
    }
}

