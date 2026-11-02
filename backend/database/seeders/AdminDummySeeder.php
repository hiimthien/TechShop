<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductVariant;
use App\Models\User;
use App\Models\WarrantyClaim;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AdminDummySeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Coupons
        $coupons = [
            [
                'code' => 'TECHVINA10',
                'type' => 'percentage',
                'value' => 10,
                'min_order_value' => 500000,
                'max_discount' => 200000,
                'usage_limit' => 100,
                'used_count' => 18,
                'expires_at' => now()->addDays(30),
                'is_active' => true,
            ],
            [
                'code' => 'TECHVINA50K',
                'type' => 'fixed',
                'value' => 50000,
                'min_order_value' => 300000,
                'max_discount' => null,
                'usage_limit' => 200,
                'used_count' => 45,
                'expires_at' => now()->addDays(60),
                'is_active' => true,
            ],
            [
                'code' => 'GAMINGVIP',
                'type' => 'percentage',
                'value' => 15,
                'min_order_value' => 2000000,
                'max_discount' => 500000,
                'usage_limit' => 50,
                'used_count' => 12,
                'expires_at' => now()->addDays(15),
                'is_active' => true,
            ],
            [
                'code' => 'SUMMERSALE',
                'type' => 'percentage',
                'value' => 5,
                'min_order_value' => 100000,
                'max_discount' => 100000,
                'usage_limit' => 500,
                'used_count' => 120,
                'expires_at' => now()->subDays(5),
                'is_active' => false,
            ],
            [
                'code' => 'FREESHIP',
                'type' => 'fixed',
                'value' => 30000,
                'min_order_value' => 400000,
                'max_discount' => null,
                'usage_limit' => 300,
                'used_count' => 88,
                'expires_at' => now()->addDays(45),
                'is_active' => true,
            ],
        ];

        foreach ($coupons as $c) {
            Coupon::updateOrCreate(['code' => $c['code']], $c);
        }

        // 2. Ensure customer user exists
        $user = User::where('role', '!=', 'admin')->first();
        if (!$user) {
            $user = User::create([
                'name' => 'Nguyễn Văn Khách',
                'email' => 'customer@techshop.com',
                'password' => bcrypt('password'),
                'phone' => '0987654321',
                'role' => 'customer',
            ]);
        }

        // Ensure user address exists
        $address = Address::where('user_id', $user->id)->first();
        if (!$address) {
            $address = Address::create([
                'user_id' => $user->id,
                'name' => 'Nguyễn Văn Khách',
                'phone' => '0987654321',
                'address' => 'Số 123 Đường Cầu Giấy, Phường Dịch Vọng',
                'city' => 'Hà Nội',
                'is_default' => true,
            ]);
        }

        // 3. Seed sample orders across the last 4 months
        $variants = ProductVariant::with('product')->take(20)->get();
        if ($variants->isNotEmpty() && Order::count() < 10) {
            $statuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
            for ($i = 1; $i <= 20; $i++) {
                $status = $statuses[array_rand($statuses)];
                $randomVariant = $variants->random();
                $qty = rand(1, 3);
                $unitPrice = $randomVariant->price;
                $total = $unitPrice * $qty + 30000;
                $daysAgo = rand(0, 90);
                $createdAt = now()->subDays($daysAgo)->subHours(rand(1, 23));

                $order = Order::create([
                    'user_id' => $user->id,
                    'address_id' => $address->id,
                    'total_price' => $total,
                    'shipping_fee' => 30000,
                    'ghn_order_code' => 'GHN' . rand(10000000, 99999999),
                    'expected_delivery_at' => $createdAt->copy()->addDays(3),
                    'status' => $status,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);

                OrderItem::create([
                    'order_id' => $order->id,
                    'variant_id' => $randomVariant->id,
                    'quantity' => $qty,
                    'price' => $unitPrice,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);
            }
        }

        // 4. Seed sample warranty claims
        $warrantySamples = [
            [
                'customer_name' => 'Trần Văn Bình',
                'customer_phone' => '0912345678',
                'customer_email' => 'binhtran@gmail.com',
                'product_name' => 'Chuột Gaming Logitech G502 Hero',
                'serial_number' => 'LT-9821034',
                'issue_description' => 'Nút chuột trái bị hiện tượng double click khi bấm 1 lần.',
                'status' => 'pending',
                'admin_note' => null,
            ],
            [
                'customer_name' => 'Lê Thị Thu',
                'customer_phone' => '0934567890',
                'customer_email' => 'thule@gmail.com',
                'product_name' => 'Màn hình ASUS TUF Gaming 27 inch 165Hz',
                'serial_number' => 'AS-5544211',
                'issue_description' => 'Màn hình xuất hiện một sọc dọc màu xanh lá ở mép phải.',
                'status' => 'inspecting',
                'admin_note' => 'Đã nhận máy tại TTBH Cầu Giấy, đang kiểm tra tấm nền hiển thị.',
            ],
            [
                'customer_name' => 'Phạm Hoàng Nam',
                'customer_phone' => '0901223344',
                'customer_email' => 'namph@gmail.com',
                'product_name' => 'Bàn phím cơ Akko 3087 v2 Monet',
                'serial_number' => 'AK-1100223',
                'issue_description' => 'Phím Space và Shift phải không phản hồi khi gõ.',
                'status' => 'completed',
                'admin_note' => 'Đã thay switch mới, kiểm tra hoạt động ổn định và bàn giao cho khách.',
            ],
            [
                'customer_name' => 'Vũ Đình Toàn',
                'customer_phone' => '0988776655',
                'customer_email' => 'toanvd@gmail.com',
                'product_name' => 'Tai nghe Kingston HyperX Cloud II',
                'serial_number' => 'HX-7788990',
                'issue_description' => 'Bên tai phải bị mất tiếng sau khi rơi từ bàn xuống.',
                'status' => 'rejected',
                'admin_note' => 'Sản phẩm có dấu hiệu nứt vỡ do va đập vật lý ngoài điều kiện bảo hành của hãng.',
            ],
        ];

        foreach ($warrantySamples as $sample) {
            $claimCode = 'BH-' . strtoupper(Str::random(6));
            WarrantyClaim::create([
                ...$sample,
                'claim_code' => $claimCode,
                'purchase_date' => now()->subMonths(rand(1, 8)),
                'user_id' => $user->id,
            ]);
        }
    }
}
