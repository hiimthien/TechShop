<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class VnpayPaymentTest extends TestCase
{
    use RefreshDatabase;

    protected string $hashSecret = 'SECRETKEYDEMOVNPAYFORTECHSHOP123';

    protected function setUp(): void
    {
        parent::setUp();
        Config::set('services.vnpay.hash_secret', $this->hashSecret);
    }

    protected function createTestOrder(User $user, array $attributes = []): Order
    {
        $address = Address::create([
            'user_id'    => $user->id,
            'name'       => 'Nguyen Van A',
            'phone'      => '0987654321',
            'address'    => '123 Le Loi',
            'city'       => 'TP.HCM',
            'is_default' => true,
        ]);

        return Order::create(array_merge([
            'user_id'        => $user->id,
            'address_id'     => $address->id,
            'total_price'    => 15000000,
            'status'         => 'pending',
            'payment_method' => 'vnpay',
            'payment_status' => 'pending',
        ], $attributes));
    }

    protected function generateVnpayParams(array $params): array
    {
        ksort($params);
        $i = 0;
        $hashData = '';
        foreach ($params as $key => $value) {
            if ($i == 1) {
                $hashData .= '&' . urlencode($key) . '=' . urlencode((string) $value);
            } else {
                $hashData .= urlencode($key) . '=' . urlencode((string) $value);
                $i = 1;
            }
        }
        $params['vnp_SecureHash'] = hash_hmac('sha512', $hashData, $this->hashSecret);
        return $params;
    }

    public function test_can_generate_vnpay_url(): void
    {
        $user = User::factory()->create();
        $order = $this->createTestOrder($user);

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
        $order = $this->createTestOrder($user, ['payment_status' => 'paid']);

        $response = $this->actingAs($user)->postJson('/api/payment/vnpay/create-url', [
            'order_id' => $order->id,
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('status', false);
    }

    public function test_cannot_generate_vnpay_url_for_cancelled_order(): void
    {
        $user = User::factory()->create();
        $order = $this->createTestOrder($user, ['status' => 'cancelled']);

        $response = $this->actingAs($user)->postJson('/api/payment/vnpay/create-url', [
            'order_id' => $order->id,
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('status', false);
    }

    public function test_handle_vnpay_return_success(): void
    {
        $user = User::factory()->create();
        $order = $this->createTestOrder($user, ['total_price' => 15000000]);

        $params = $this->generateVnpayParams([
            'vnp_Amount'        => '1500000000',
            'vnp_BankCode'      => 'NCB',
            'vnp_CardType'      => 'ATM',
            'vnp_OrderInfo'     => 'Thanh toan don hang TechShop',
            'vnp_PayDate'       => date('YmdHis'),
            'vnp_ResponseCode'  => '00',
            'vnp_TmnCode'       => 'DEMOVNPAY',
            'vnp_TransactionNo' => '14123456',
            'vnp_TxnRef'        => (string) $order->id,
        ]);

        $response = $this->getJson('/api/payment/vnpay/return?' . http_build_query($params));

        $response->assertStatus(200)
            ->assertJsonPath('status', true)
            ->assertJsonPath('data.order_id', $order->id)
            ->assertJsonPath('data.transaction_no', '14123456');

        $order->refresh();
        $this->assertEquals('paid', $order->payment_status);
        $this->assertEquals('processing', $order->status);
        $this->assertEquals('14123456', $order->payment_transaction_id);
    }

    public function test_handle_vnpay_return_customer_cancelled(): void
    {
        $user = User::factory()->create();
        $order = $this->createTestOrder($user, ['total_price' => 15000000]);

        $params = $this->generateVnpayParams([
            'vnp_Amount'        => '1500000000',
            'vnp_ResponseCode'  => '24', // 24 = Customer cancelled
            'vnp_TmnCode'       => 'DEMOVNPAY',
            'vnp_TxnRef'        => (string) $order->id,
        ]);

        $response = $this->getJson('/api/payment/vnpay/return?' . http_build_query($params));

        $response->assertStatus(400)
            ->assertJsonPath('status', false)
            ->assertJsonPath('data.response_code', '24');

        $order->refresh();
        $this->assertEquals('failed', $order->payment_status);
    }

    public function test_handle_vnpay_ipn_success_and_idempotent(): void
    {
        $user = User::factory()->create();
        $order = $this->createTestOrder($user, ['total_price' => 15000000]);

        $params = $this->generateVnpayParams([
            'vnp_Amount'        => '1500000000',
            'vnp_ResponseCode'  => '00',
            'vnp_TmnCode'       => 'DEMOVNPAY',
            'vnp_TransactionNo' => '99887766',
            'vnp_TxnRef'        => (string) $order->id,
        ]);

        // Lần đầu IPN tới
        $response1 = $this->getJson('/api/payment/vnpay/ipn?' . http_build_query($params));
        $response1->assertStatus(200)->assertJsonPath('RspCode', '00');

        $order->refresh();
        $this->assertEquals('paid', $order->payment_status);
        $this->assertEquals('processing', $order->status);
        $this->assertEquals('99887766', $order->payment_transaction_id);

        // Lần 2 IPN lặp lại (Idempotency) -> RspCode 02 (Order already confirmed)
        $response2 = $this->getJson('/api/payment/vnpay/ipn?' . http_build_query($params));
        $response2->assertStatus(200)->assertJsonPath('RspCode', '02');
    }

    public function test_ipn_rejects_invalid_checksum(): void
    {
        $response = $this->getJson('/api/payment/vnpay/ipn?vnp_TxnRef=1&vnp_SecureHash=invalid_hash');

        $response->assertStatus(200)
            ->assertJsonPath('RspCode', '97'); // 97 = Invalid checksum in VNPAY spec
    }
}
