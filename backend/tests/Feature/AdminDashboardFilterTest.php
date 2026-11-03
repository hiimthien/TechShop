<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardFilterTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_filter_dashboard_metrics_by_period(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create();

        $address = Address::create([
            'user_id' => $user->id,
            'name' => 'Customer A',
            'phone' => '0987654321',
            'address' => '123 Le Loi',
            'city' => 'TP.HCM',
            'is_default' => true,
        ]);

        // Order created today
        Order::create([
            'user_id' => $user->id,
            'address_id' => $address->id,
            'total_price' => 5000000,
            'status' => 'delivered',
            'created_at' => now(),
        ]);

        // Order created 2 months ago
        $oldOrder = new Order([
            'user_id' => $user->id,
            'address_id' => $address->id,
            'total_price' => 10000000,
            'status' => 'delivered',
        ]);
        $oldOrder->timestamps = false;
        $oldOrder->created_at = now()->subMonths(2);
        $oldOrder->updated_at = now()->subMonths(2);
        $oldOrder->save();

        // 1. Query dashboard with 7days period
        $res = $this->actingAs($admin)->getJson('/api/admin/dashboard?period=7days');

        $res->assertStatus(200)
            ->assertJsonPath('status', true)
            ->assertJsonPath('data.summary.total_revenue', 5000000)
            ->assertJsonPath('data.summary.total_orders', 1)
            ->assertJsonPath('data.summary.all_time_orders', 2);
    }
}
