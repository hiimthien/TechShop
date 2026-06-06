<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $product = Product::create([
            'name'        => 'Chuột Logitech G102',
            'slug'        => 'chuot-logitech-g102',
            'category_id' => 1,
            'brand_id'    => 1,
            'description' => 'Chuột gaming Logitech G102 chính hãng',
            'is_active'   => true,
        ]);

        $colorAttr = ProductAttribute::create([
            'product_id' => $product->id,
            'name'       => 'Màu sắc',
        ]);

        $black = $colorAttr->options()->create(['value' => 'Đen']);
        $white = $colorAttr->options()->create(['value' => 'Trắng']);

        $variant1 = ProductVariant::create([
            'product_id' => $product->id,
            'sku'        => 'G102-BLACK',
            'price'      => 490000,
            'stock'      => 10,
        ]);
        $variant1->options()->attach($black->id);

        $variant2 = ProductVariant::create([
            'product_id' => $product->id,
            'sku'        => 'G102-WHITE',
            'price'      => 490000,
            'stock'      => 8,
        ]);
        $variant2->options()->attach($white->id);
    }
}
