<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_code' => '#' . str_pad((string)$this->id, 6, '0', STR_PAD_LEFT),
            'shipping_fee' => (float) $this->shipping_fee,
            'discount_amount' => (float) ($this->discount_amount ?? 0),
            'total_price' => (float) $this->total_price,
            'final_total' => (float) $this->total_price,
            'status' => $this->status,
            'payment_method' => $this->payment_method ?? 'cod',
            'payment_status' => $this->payment_status ?? 'unpaid',
            'payment_transaction_id' => $this->payment_transaction_id,
            'created_at' => $this->created_at?->format('d/m/Y H:i') ?? '',
            'created_at_raw' => $this->created_at?->toISOString(),
            'address' => $this->whenLoaded('address', fn() => [
                'id' => $this->address->id,
                'name' => $this->address->name,
                'phone' => $this->address->phone,
                'address' => $this->address->address,
                'city' => $this->address->city,
            ]),
            'coupon' => $this->whenLoaded('coupon', fn() => [
                'id' => $this->coupon->id,
                'code' => $this->coupon->code,
                'type' => $this->coupon->type,
                'value' => (float) $this->coupon->value,
            ]),
            'items' => $this->whenLoaded('items', fn() => $this->items->map(fn($item) => [
                'id' => $item->id,
                'serial_number' => $item->serial_number,
                'quantity' => $item->quantity,
                'price' => (float) $item->price,
                'line_total' => (float) ($item->price * $item->quantity),
                'variant' => [
                    'id' => $item->variant?->id,
                    'sku' => $item->variant?->sku,
                ],
                'product' => [
                    'id' => $item->variant?->product?->id,
                    'name' => $item->variant?->product?->name ?? 'Sản phẩm TechShop',
                    'slug' => $item->variant?->product?->slug,
                    'image' => $item->variant?->product?->images?->where('is_primary', true)->first()?->image_url
                        ?? $item->variant?->product?->images?->first()?->image_url
                        ?? 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=300',
                ],
            ])),
        ];
    }
}
