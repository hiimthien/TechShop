<?php

namespace Tests\Feature;

use App\Jobs\AutoCancelUnpaidOrderJob;
use App\Jobs\SendOrderConfirmationJob;
use App\Models\Address;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use App\Services\CouponService;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class OrderCheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_creates_order_and_decrements_stock_and_dispatches_jobs(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        $address = Address::create([
            'user_id' => $user->id,
            'name' => 'Nguyen Van A',
            'phone' => '0987654321',
            'address' => '123 Le Loi',
            'city' => 'TP.HCM',
            'is_default' => true,
        ]);

        $category = Category::create(['name' => 'Laptop', 'slug' => 'laptop']);
        $brand = \App\Models\Brand::create(['name' => 'Apple', 'slug' => 'apple']);
        $product = Product::create([
            'name' => 'MacBook Pro',
            'slug' => 'macbook-pro',
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'starting_price' => 30000000,
            'is_active' => true,
        ]);

        $variant = ProductVariant::create([
            'product_id' => $product->id,
            'sku' => 'MBP-14-M3',
            'price' => 30000000,
            'stock' => 5,
        ]);

        $cart = Cart::create(['user_id' => $user->id]);
        CartItem::create([
            'cart_id' => $cart->id,
            'variant_id' => $variant->id,
            'quantity' => 2,
        ]);

        $orderService = app(OrderService::class);
        $order = $orderService->createFromCart($cart, [
            'address_id' => $address->id,
            'shipping_fee' => 50000,
            'payment_method' => 'cod',
        ]);

        // 1. Verify Order created
        $this->assertNotNull($order->id);
        $this->assertEquals(60050000, $order->total_price);
        $this->assertEquals('pending', $order->status);

        // 2. Verify Stock decremented
        $variant->refresh();
        $this->assertEquals(3, $variant->stock);

        // 3. Verify Jobs dispatched
        Queue::assertPushed(SendOrderConfirmationJob::class);
        Queue::assertPushed(AutoCancelUnpaidOrderJob::class);
    }

    public function test_fails_when_product_out_of_stock(): void
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

        $category = Category::create(['name' => 'Laptop', 'slug' => 'laptop']);
        $brand = \App\Models\Brand::create(['name' => 'Dell', 'slug' => 'dell']);
        $product = Product::create([
            'name' => 'Dell XPS',
            'slug' => 'dell-xps',
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'starting_price' => 25000000,
            'is_active' => true,
        ]);

        $variant = ProductVariant::create([
            'product_id' => $product->id,
            'sku' => 'DELL-XPS-15',
            'price' => 25000000,
            'stock' => 1, // Only 1 in stock
        ]);

        $cart = Cart::create(['user_id' => $user->id]);
        CartItem::create([
            'cart_id' => $cart->id,
            'variant_id' => $variant->id,
            'quantity' => 2, // Requesting 2
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('không đủ số lượng tồn kho');

        $orderService = app(OrderService::class);
        $orderService->createFromCart($cart, [
            'address_id' => $address->id,
            'shipping_fee' => 0,
            'payment_method' => 'cod',
        ]);
    }
}
