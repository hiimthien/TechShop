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
            'id'             => $this->id,
            'shipping_fee'      => $this->shipping_fee,
            'total_price' => $this->total_price,
            'status'     => $this->status,
            'created_at' => $this->created_at,
            'items' => $this->whenLoaded('items', fn() => $this->items->map(fn($item) => [
                'id'       => $item->id,
                'quantity' => $item->quantity,
                'price'    => $item->price,
                'variant'  => [
                    'id'  => $item->variant->id,
                    'sku' => $item->variant->sku,
                ],
            ])),
        ];
    }
}