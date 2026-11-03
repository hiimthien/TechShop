<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_categories(): void
    {
        Category::create([
            'name' => 'Điện thoại',
            'slug' => 'dien-thoai',
            'description' => 'Điện thoại thông minh',
        ]);

        $response = $this->getJson('/api/categories');

        $response->assertStatus(200)
            ->assertJsonFragment([
                'name' => 'Điện thoại',
                'slug' => 'dien-thoai',
            ]);
    }

    public function test_can_get_mega_menu(): void
    {
        $category = Category::create([
            'name' => 'Laptop',
            'slug' => 'laptop',
        ]);

        $brand = Brand::create([
            'name' => 'Apple',
            'slug' => 'apple',
        ]);

        $product = Product::create([
            'name' => 'MacBook Pro 14',
            'slug' => 'macbook-pro-14',
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'starting_price' => 30000000,
            'is_active' => true,
        ]);

        $response = $this->getJson('/api/mega-menu');

        $response->assertStatus(200)
            ->assertJsonPath('status', true);
    }

    public function test_can_filter_products_by_slug_and_aliases(): void
    {
        $category = Category::create([
            'name' => 'Điện thoại',
            'slug' => 'dien-thoai',
        ]);

        $brand = Brand::create([
            'name' => 'Apple',
            'slug' => 'apple',
        ]);

        Product::create([
            'name' => 'iPhone 15 Pro',
            'slug' => 'iphone-15-pro',
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'starting_price' => 25000000,
            'is_active' => true,
        ]);

        // 1. Query with exact slug
        $res1 = $this->getJson('/api/products?category=dien-thoai');
        $res1->assertStatus(200);
        $this->assertCount(1, $res1->json('data'));

        // 2. Query with alias 'smartphones'
        $res2 = $this->getJson('/api/products?category=smartphones');
        $res2->assertStatus(200);
        $this->assertCount(1, $res2->json('data'));
        $this->assertEquals('iPhone 15 Pro', $res2->json('data.0.name'));
    }
}

