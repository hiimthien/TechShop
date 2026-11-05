<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Coupon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class CouponSeeder extends Seeder
{
    public function run(): void
    {
        $coupons = [
            [
                'code' => 'TECHSHOP10',
                'type' => 'percentage',
                'value' => 10,
                'min_order_value' => 500000,
                'max_discount' => 200000,
                'usage_limit' => 50,
                'used_count' => 0, // Sẽ được OrderDemoSeeder cập nhật chính xác theo số đơn thực tế
                'expires_at' => Carbon::now()->addDays(30)->toDateString(),
                'is_active' => true,
            ],
            [
                'code' => 'TECHSHOP50K',
                'type' => 'fixed',
                'value' => 50000,
                'min_order_value' => 300000,
                'max_discount' => null,
                'usage_limit' => 60,
                'used_count' => 0,
                'expires_at' => Carbon::now()->addDays(45)->toDateString(),
                'is_active' => true,
            ],
            [
                'code' => 'GAMINGVIP',
                'type' => 'percentage',
                'value' => 15,
                'min_order_value' => 2000000,
                'max_discount' => 500000,
                'usage_limit' => 30,
                'used_count' => 0,
                'expires_at' => Carbon::now()->addDays(20)->toDateString(),
                'is_active' => true,
            ],
            [
                'code' => 'FREESHIP',
                'type' => 'fixed',
                'value' => 30000,
                'min_order_value' => 400000,
                'max_discount' => null,
                'usage_limit' => 80,
                'used_count' => 0,
                'expires_at' => Carbon::now()->addDays(60)->toDateString(),
                'is_active' => true,
            ],
            [
                'code' => 'SUMMERSALE',
                'type' => 'percentage',
                'value' => 5,
                'min_order_value' => 200000,
                'max_discount' => 100000,
                'usage_limit' => 40,
                'used_count' => 0,
                'expires_at' => Carbon::now()->subDays(10)->toDateString(),
                'is_active' => false,
            ],
            [
                'code' => 'HETLUOT',
                'type' => 'fixed',
                'value' => 20000,
                'min_order_value' => 200000,
                'max_discount' => null,
                'usage_limit' => 15,
                'used_count' => 15, // Đã hết lượt dùng để demo báo lỗi trên UI
                'expires_at' => Carbon::now()->addDays(30)->toDateString(),
                'is_active' => true,
            ],
        ];

        foreach ($coupons as $c) {
            Coupon::updateOrCreate(['code' => $c['code']], $c);
        }
    }
}

