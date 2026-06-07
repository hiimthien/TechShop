<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'name'           => $this->name,
            'slug'           => $this->slug,
            'brand'          => $this->whenLoaded('brand', fn() => $this->brand->name),
            'category'       => $this->whenLoaded('category', fn() => $this->category->name),
            'thumbnail'      => $this->whenLoaded('images', fn() => optional($this->images->first())->image_url),
            'starting_price' => $this->variants_min_price ?? $this->whenLoaded('variants', fn() => $this->variants->min('price')),

            // Chỉ xuất hiện ở trang detail (show)
            'description'    => $this->when($this->relationLoaded('variants'), $this->description),
            'variants'       => ProductVariantResource::collection($this->whenLoaded('variants')),
            'images'         => $this->whenLoaded('images', fn() => $this->images->map(fn($img) => $img->image_url)),
        ];
    }
}