<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeOption;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductService
{
    public function __construct(
        protected ProductRepositoryInterface $productRepository
    ) {}

    /**
     * Create a new product along with variants, options, and gallery images.
     */
    public function createProduct(array $data): Product
    {
        return DB::transaction(function () use ($data) {
            $product = $this->productRepository->create([
                'name' => $data['name'],
                'slug' => Str::slug($data['name']) . '-' . Str::random(5),
                'brand_id' => $data['brand_id'],
                'category_id' => $data['category_id'],
                'is_active' => $data['is_active'] ?? true,
                'description' => $data['description'] ?? null,
            ]);

            $this->syncVariants($product, $data);
            $this->syncImages($product, $data);

            return $product->load(['brand', 'category', 'images', 'variants.options']);
        });
    }

    /**
     * Update an existing product, updating its variants, options, and gallery images.
     */
    public function updateProduct(Product $product, array $data): Product
    {
        return DB::transaction(function () use ($product, $data) {
            $updated = $this->productRepository->update($product, [
                'name' => $data['name'],
                'brand_id' => $data['brand_id'],
                'category_id' => $data['category_id'],
                'is_active' => $data['is_active'] ?? $product->is_active,
                'description' => $data['description'] ?? $product->description,
            ]);

            $this->syncVariants($updated, $data);
            $this->syncImages($updated, $data);

            return $updated->load(['brand', 'category', 'images', 'variants.options']);
        });
    }

    /**
     * Delete a product and its associated assets safely.
     */
    public function deleteProduct(Product $product): bool
    {
        return DB::transaction(function () use ($product) {
            return $this->productRepository->delete($product);
        });
    }

    /**
     * Helper to sync variants and attribute options.
     */
    protected function syncVariants(Product $product, array $data): void
    {
        if (!empty($data['variants']) && is_array($data['variants'])) {
            $product->variants()->delete();
            $attribute = ProductAttribute::firstOrCreate(
                ['product_id' => $product->id, 'name' => 'Phiên bản']
            );

            foreach ($data['variants'] as $v) {
                $sku = !empty($v['sku'])
                    ? $v['sku']
                    : strtoupper(Str::slug($product->name, '-')) . '-' . rand(1000, 9999);

                $variant = ProductVariant::create([
                    'product_id' => $product->id,
                    'sku' => $sku,
                    'price' => $v['price'] ?? 0,
                    'stock' => $v['stock'] ?? 10,
                    'image_url' => $v['image_url'] ?? null,
                ]);

                if (!empty($v['sku_name'])) {
                    $option = ProductAttributeOption::firstOrCreate([
                        'product_attribute_id' => $attribute->id,
                        'value' => trim($v['sku_name']),
                    ]);
                    $variant->options()->syncWithoutDetaching([$option->id]);
                }
            }
        } elseif (isset($data['price']) || isset($data['stock'])) {
            $variant = $product->variants()->first();
            if ($variant) {
                if (isset($data['price'])) $variant->price = $data['price'];
                if (isset($data['stock'])) $variant->stock = $data['stock'];
                if (isset($data['image_url'])) $variant->image_url = $data['image_url'];
                $variant->save();
            } else {
                $sku = !empty($data['sku'])
                    ? $data['sku']
                    : strtoupper(Str::slug($product->name, '-')) . '-' . rand(100, 999);

                ProductVariant::create([
                    'product_id' => $product->id,
                    'sku' => $sku,
                    'price' => $data['price'] ?? 1000000,
                    'stock' => $data['stock'] ?? 50,
                    'image_url' => $data['image_url'] ?? null,
                ]);
            }
        }
    }

    /**
     * Helper to sync gallery images.
     */
    protected function syncImages(Product $product, array $data): void
    {
        if (isset($data['images']) && is_array($data['images'])) {
            $product->images()->delete();
            foreach ($data['images'] as $idx => $imgUrl) {
                if (!empty($imgUrl)) {
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_url' => $imgUrl,
                        'is_primary' => $idx === 0,
                    ]);
                }
            }
        } elseif (!empty($data['image_url'])) {
            $img = $product->images()->first();
            if ($img) {
                $img->image_url = $data['image_url'];
                $img->save();
            } else {
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_url' => $data['image_url'],
                    'is_primary' => true,
                ]);
            }
        }
    }
}
