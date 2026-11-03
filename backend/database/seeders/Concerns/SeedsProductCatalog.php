<?php

declare(strict_types=1);

namespace Database\Seeders\Concerns;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeOption;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

trait SeedsProductCatalog
{
    /**
     * Nạp danh mục sản phẩm và các biến thể vào database một cách tối ưu và an toàn.
     *
     * @param array<int, array{
     *     name: string,
     *     category_slug: string,
     *     brand_slug: string,
     *     description: string,
     *     variants: array<int, array{
     *         name: string,
     *         sku: string,
     *         price: int|float,
     *         stock: int,
     *         image_url: string
     *     }>
     * }> $catalog
     */
    protected function seedProducts(array $catalog): void
    {
        $categories = Category::all()->keyBy('slug');
        $brands = Brand::all()->keyBy('slug');

        if ($categories->isEmpty() || $brands->isEmpty()) {
            if (isset($this->command)) {
                $this->command->warn('Vui lòng chạy CategorySeeder và BrandSeeder trước.');
            }
            return;
        }

        // Chia theo từng batch 50 sản phẩm để tối ưu bộ nhớ và tốc độ commit
        $chunks = array_chunk($catalog, 50);

        foreach ($chunks as $chunk) {
            DB::transaction(function () use ($chunk, $categories, $brands) {
                foreach ($chunk as $item) {
                    $category = $categories[$item['category_slug']] ?? null;
                    $brand = $brands[$item['brand_slug']] ?? null;

                    if (!$category || !$brand) {
                        continue;
                    }

                    $slug = Str::slug($item['name']);

                    // 1. Tạo hoặc cập nhật Product chính
                    $product = Product::updateOrCreate(
                        ['slug' => $slug],
                        [
                            'name' => $item['name'],
                            'brand_id' => $brand->id,
                            'category_id' => $category->id,
                            'description' => $item['description'],
                            'is_active' => true,
                        ]
                    );

                    // 2. Tạo thuộc tính phân loại
                    $attribute = ProductAttribute::firstOrCreate(
                        ['product_id' => $product->id, 'name' => 'Phiên bản & Màu sắc']
                    );

                    // 3. Xóa variants và images cũ để cập nhật mới sạch sẽ
                    $product->variants()->delete();
                    $product->images()->delete();

                    // 4. Tạo từng biến thể (variants) & hình ảnh tương ứng
                    foreach ($item['variants'] as $idx => $v) {
                        $sku = !empty($v['sku']) ? $v['sku'] : Str::upper(Str::slug($product->slug . '-' . $v['name']));
                        if (strlen($sku) > 190) {
                            $sku = substr($sku, 0, 180) . '-' . ($idx + 1);
                        }

                        $variant = ProductVariant::create([
                            'product_id' => $product->id,
                            'sku' => $sku,
                            'price' => $v['price'],
                            'stock' => $v['stock'],
                            'image_url' => $v['image_url'],
                        ]);

                        $option = ProductAttributeOption::firstOrCreate([
                            'product_attribute_id' => $attribute->id,
                            'value' => $v['name'],
                        ]);

                        $variant->options()->syncWithoutDetaching([$option->id]);

                        if (!empty($v['image_url'])) {
                            ProductImage::create([
                                'product_id' => $product->id,
                                'option_id' => $option->id,
                                'image_url' => $v['image_url'],
                                'is_primary' => $idx === 0,
                            ]);
                        }
                    }
                }
            });
        }
    }
}
