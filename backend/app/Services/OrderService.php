<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function createFromCart(Cart $cart, array $data): Order
    {
        return DB::transaction(function () use ($cart, $data) {
            $cart->load('items.variant');

            if ($cart->items->isEmpty()) {
                throw new \Exception('Giỏ hàng trống');
            }

            foreach ($cart->items as $item) {
                if ($item->variant->stock < $item->quantity) {
                    throw new \Exception("Sản phẩm {$item->variant->sku} không đủ tồn kho");
                }
            }

            $totalPrice = $cart->items->sum(fn($item) => $item->variant->price * $item->quantity);
            $order = Order::create([
                'user_id' => $cart->user_id,
                'shipping_fee' => $data['shipping_fee'],
                'address_id' => $data['address_id'],
                'total_price' => $totalPrice,
                'status'       => 'pending',
            ]);

            foreach ($cart->items as $item) {
                OrderItem::create([
                    'order_id'   => $order->id,
                    'variant_id' => $item->variant_id,
                    'quantity'   => $item->quantity,
                    'price'      => $item->variant->price,
                ]);
            }

            foreach ($cart->items as $item) {
                $item->variant->decrement('stock', $item->quantity);
            }

            $cart->delete();

            return $order;
        });
    }
}
