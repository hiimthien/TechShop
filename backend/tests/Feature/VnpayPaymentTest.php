<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VnpayPaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_generate_vnpay_url(): void
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
            'total_price' => 15000000,
            'status' => 'pending',
            'payment_method' => 'vnpay',
            'payment_status' => 'pending',
        ]);

        $response = $this->actingAs($user)->postJson('/api/payment/vnpay/create-url', [
            'order_id' => $order->id,
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('status', true);

        $url = $response->json('data.payment_url');
        $this->assertNotEmpty($url);
        $this->assertStringContainsString('vnp_Amount=', $url);
        $this->assertStringContainsString('vnp_TxnRef=', $url);
    }

    public function test_cannot_generate_vnpay_url_for_already_paid_order(): void
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
            'total_price' => 15000000,
            'status' => 'pending',
            'payment_method' => 'vnpay',
            'payment_status' => 'paid',
        ]);

        $response = $this->actingAs($user)->postJson('/api/payment/vnpay/create-url', [
            'order_id' => $order->id,
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('status', false);
    }

    public function test_ipn_rejects_invalid_checksum(): void
    {
        $response = $this->getJson('/api/payment/vnpay/ipn?vnp_TxnRef=1&vnp_SecureHash=invalid_hash');

        $response->assertStatus(200)
            ->assertJsonPath('RspCode', '97'); // 97 = Invalid checksum in VNPAY spec
    }
}
