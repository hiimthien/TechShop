<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use App\Models\WarrantyClaim;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WarrantyClaimTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_can_lookup_warranty_by_claim_code(): void
    {
        $user = User::factory()->create();
        $address = Address::create([
            'user_id' => $user->id,
            'name' => 'Nguyen Van A',
            'phone' => '0987654321',
            'address' => '123 Le Loi',
            'city' => 'TP.HCM',
            'is_default' => true,
        ]);

        $order = Order::create([
            'user_id' => $user->id,
            'address_id' => $address->id,
            'total_price' => 1000000,
            'status' => 'completed',
        ]);

        $category = Category::create(['name' => 'Tai nghe', 'slug' => 'tai-nghe']);
        $brand = \App\Models\Brand::create(['name' => 'Sony', 'slug' => 'sony']);
        $product = Product::create([
            'name' => 'Sony WH-1000XM5',
            'slug' => 'sony-wh-1000xm5',
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'starting_price' => 6000000,
            'is_active' => true,
        ]);
        $variant = ProductVariant::create([
            'product_id' => $product->id,
            'sku' => 'SONY-XM5-BLK',
            'price' => 6000000,
            'stock' => 10,
        ]);

        $item = OrderItem::create([
            'order_id' => $order->id,
            'variant_id' => $variant->id,
            'quantity' => 1,
            'price' => 6000000,
            'serial_number' => 'SN-SONY-998877',
        ]);

        $claim = WarrantyClaim::create([
            'user_id' => $user->id,
            'claim_code' => 'WAR-TEST-12345',
            'customer_name' => 'Nguyen Van A',
            'customer_phone' => '0987654321',
            'product_name' => 'Sony WH-1000XM5',
            'serial_number' => 'SN-SONY-998877',
            'issue_description' => 'Tai nghe bên trái không nghe thấy tiếng',
            'status' => 'pending',
        ]);

        // 1. Lookup by claim code
        $res1 = $this->getJson('/api/warranty-claims/lookup?query=WAR-TEST-12345');
        $res1->assertStatus(200)
            ->assertJsonPath('status', true);
        $this->assertNotEmpty($res1->json('data'));

        // 2. Lookup by phone
        $res2 = $this->getJson('/api/warranty-claims/lookup?query=0987654321');
        $res2->assertStatus(200)
            ->assertJsonPath('status', true);
        $this->assertNotEmpty($res2->json('data'));
    }
}
