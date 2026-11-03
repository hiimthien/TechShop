<?php

namespace Tests\Feature;

use App\Models\Coupon;
use App\Models\User;
use App\Services\CouponService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CouponApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_validate_and_calculate_percentage_coupon(): void
    {
        $user = User::factory()->create();

        Coupon::create([
            'code' => 'DISCOUNT10',
            'type' => 'percentage',
            'value' => 10,
            'max_discount' => 500000,
            'min_order_value' => 1000000,
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->postJson('/api/coupons/check', [
            'code' => 'discount10',
            'order_total' => 2000000,
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('status', true)
            ->assertJsonPath('data.coupon.code', 'DISCOUNT10')
            ->assertJsonPath('data.discount_amount', 200000);
    }

    public function test_caps_at_max_discount(): void
    {
        $user = User::factory()->create();

        Coupon::create([
            'code' => 'VIP50',
            'type' => 'percentage',
            'value' => 50,
            'max_discount' => 500000,
            'min_order_value' => 1000000,
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->postJson('/api/coupons/check', [
            'code' => 'VIP50',
            'order_total' => 10000000, // 50% = 5,000,000, but capped at 500,000
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.discount_amount', 500000);
    }

    public function test_rejects_expired_coupon(): void
    {
        $user = User::factory()->create();

        Coupon::create([
            'code' => 'EXPIRED',
            'type' => 'fixed',
            'value' => 100000,
            'expires_at' => now()->subDay()->toDateString(),
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->postJson('/api/coupons/check', [
            'code' => 'EXPIRED',
            'order_total' => 500000,
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('status', false);
    }
}
