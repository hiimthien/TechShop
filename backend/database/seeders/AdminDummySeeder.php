<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductQuestion;
use App\Models\ProductReview;
use App\Models\ProductVariant;
use App\Models\User;
use App\Models\WarrantyClaim;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class AdminDummySeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('warranty_claims')->truncate();
        DB::table('orders')->truncate();
        Schema::enableForeignKeyConstraints();

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
        $user = User::firstOrCreate(
            ['email' => 'customer@techshop.com'],
            [
                'name' => 'Nguyễn Văn Khách',
                'password' => bcrypt('password'),
                'phone' => '0987654321',
                'role' => 'customer',
            ]
        );

        // Ensure user address exists
        $address = Address::firstOrCreate(
            ['user_id' => $user->id, 'phone' => '0987654321'],
            [
                'name' => 'Nguyễn Văn Khách',
                'address' => 'Số 123 Đường Cầu Giấy, Phường Dịch Vọng',
                'city' => 'Hà Nội',
                'is_default' => true,
            ]
        );

        // 3. Seed 28 sample orders across 90 days
        $variants = ProductVariant::with('product')->get();
        if ($variants->isNotEmpty()) {
            $paymentMethods = ['vnpay', 'cod'];
            $statuses = [
                'delivered' => 16,
                'processing' => 4,
                'shipped' => 4,
                'pending' => 2,
                'cancelled' => 2,
            ];

            $orderIndex = 1;
            foreach ($statuses as $status => $count) {
                for ($k = 0; $k < $count; $k++) {
                    $randomVariant = $variants->random();
                    $qty = rand(1, 2);
                    $unitPrice = (float) $randomVariant->price;
                    $shippingFee = 30000;
                    $discountAmount = 0;
                    $couponId = null;

                    if ($orderIndex % 3 === 0) {
                        $coupon = Coupon::where('is_active', true)->inRandomOrder()->first();
                        if ($coupon) {
                            $couponId = $coupon->id;
                            $discountAmount = $coupon->type === 'fixed' ? $coupon->value : min(200000, ($unitPrice * $qty * $coupon->value) / 100);
                        }
                    }

                    $total = max(0, ($unitPrice * $qty) + $shippingFee - $discountAmount);
                    $method = $paymentMethods[array_rand($paymentMethods)];
                    
                    $paymentStatus = match ($status) {
                        'delivered', 'shipped', 'processing' => 'paid',
                        'cancelled' => 'refunded',
                        default => $method === 'vnpay' ? 'paid' : 'unpaid',
                    };

                    // Spread orders nicely across 90 days (including today, last 7 days, last 30 days)
                    $daysAgo = match ($orderIndex % 6) {
                        0 => 0, // Today
                        1 => rand(1, 3), // Last 3 days
                        2 => rand(4, 7), // Last 7 days
                        3 => rand(8, 20), // Last 20 days
                        4 => rand(21, 30), // Last month
                        default => rand(31, 85), // Earlier
                    };
                    $createdAt = now()->subDays($daysAgo)->subHours(rand(1, 20));

                    $order = Order::create([
                        'user_id' => $user->id,
                        'address_id' => $address->id,
                        'total_price' => $total,
                        'shipping_fee' => $shippingFee,
                        'discount_amount' => $discountAmount,
                        'coupon_id' => $couponId,
                        'payment_method' => $method,
                        'payment_status' => $paymentStatus,
                        'payment_transaction_id' => $paymentStatus === 'paid' && $method === 'vnpay' ? 'VNP' . rand(10000000, 99999999) : null,
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
                        'serial_number' => 'SN-' . strtoupper(Str::random(10)),
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt,
                    ]);

                    $orderIndex++;
                }
            }
        }

        // 4. Seed Product Reviews & Ratings
        $products = Product::take(10)->get();
        if ($products->isNotEmpty()) {
            $sampleReviews = [
                [
                    'rating' => 5,
                    'customer_name' => 'Nguyễn Minh Quân',
                    'customer_phone' => '0912***456',
                    'content' => 'Sản phẩm nguyên seal chính hãng, máy dùng cực kỳ mượt mà. Đóng gói cẩn thận 3 lớp bóng khí rất an tâm!',
                    'admin_reply' => 'TechShop chân thành cảm ơn bạn Quân! Chúc bạn có trải nghiệm tuyệt vời cùng sản phẩm nhé.',
                ],
                [
                    'rating' => 5,
                    'customer_name' => 'Trần Thị Mai',
                    'customer_phone' => '0983***789',
                    'content' => 'Giao hàng siêu nhanh, đặt sáng mà chiều nhận được luôn rồi. Màu sắc thực tế nhìn sang hơn trong ảnh nhiều.',
                    'admin_reply' => 'Dạ cảm ơn chị Mai đã tin tưởng ủng hộ hệ thống TechShop ạ!',
                ],
                [
                    'rating' => 4,
                    'customer_name' => 'Lê Hoàng Long',
                    'customer_phone' => '0904***112',
                    'content' => 'Hiệu năng rất mạnh mẽ, chơi game nặng nhiệt độ vẫn ổn định. Điểm trừ nhẹ là hộp hơi móp góc do bên vận chuyển nhưng máy nguyên vẹn.',
                    'admin_reply' => 'TechShop đã ghi nhận phản hồi để cải thiện hơn nữa khâu đóng gói vận chuyển. Cảm ơn anh Long!',
                ],
                [
                    'rating' => 5,
                    'customer_name' => 'Phạm Thu Trang',
                    'customer_phone' => '0977***334',
                    'content' => 'Bàn phím gõ rất đầm tay, âm thanh êm không làm phiền người xung quanh. Thiết kế tinh tế góc cạnh tuyệt đẹp.',
                    'admin_reply' => null,
                ],
            ];

            foreach ($products as $pIdx => $product) {
                $reviewTemplate = $sampleReviews[$pIdx % count($sampleReviews)];
                ProductReview::create([
                    'product_id' => $product->id,
                    'user_id' => $user->id,
                    'customer_name' => $reviewTemplate['customer_name'],
                    'customer_phone' => $reviewTemplate['customer_phone'],
                    'rating' => $reviewTemplate['rating'],
                    'content' => $reviewTemplate['content'],
                    'admin_reply' => $reviewTemplate['admin_reply'],
                    'replied_at' => $reviewTemplate['admin_reply'] ? now()->subDays(1) : null,
                    'is_verified_purchase' => true,
                    'is_active' => true,
                    'created_at' => now()->subDays(rand(2, 20)),
                ]);
            }
        }

        // 5. Seed Product Questions (Q&A)
        if ($products->isNotEmpty()) {
            $sampleQuestions = [
                [
                    'customer_name' => 'Hoàng Tuấn Anh',
                    'customer_phone' => '0932***555',
                    'question' => 'Sản phẩm này có hỗ trợ trả góp 0% qua thẻ tín dụng Visa/MasterCard không shop?',
                    'admin_answer' => 'Dạ có ạ! TechShop hỗ trợ trả góp 0% qua thẻ tín dụng và đối tác tài chính, thủ tục online duyệt chỉ 5 phút.',
                ],
                [
                    'customer_name' => 'Đỗ Bích Ngọc',
                    'customer_phone' => '0944***888',
                    'question' => 'Hiện tại chi nhánh Hà Nội còn sẵn hàng phiên bản này không ạ?',
                    'admin_answer' => 'Dạ chi nhánh Hà Nội đang có sẵn đầy đủ màu sắc ạ, bạn có thể ghé trực tiếp hoặc đặt giao nhanh trong 2h nhé.',
                ],
                [
                    'customer_name' => 'Vũ Quốc Bảo',
                    'customer_phone' => '0966***222',
                    'question' => 'Sản phẩm có được bảo hành chính hãng 12 tháng 1 đổi 1 trong 30 ngày đầu không?',
                    'admin_answer' => 'Dạ hoàn toàn chính xác ạ. TechShop cam kết 100% hàng chính hãng, đổi mới trong 30 ngày nếu phát sinh lỗi từ nhà sản xuất.',
                ],
            ];

            foreach ($products->take(6) as $qIdx => $product) {
                $qData = $sampleQuestions[$qIdx % count($sampleQuestions)];
                ProductQuestion::create([
                    'product_id' => $product->id,
                    'user_id' => $user->id,
                    'customer_name' => $qData['customer_name'],
                    'customer_phone' => $qData['customer_phone'],
                    'question' => $qData['question'],
                    'admin_answer' => $qData['admin_answer'],
                    'answered_at' => now()->subHours(rand(2, 24)),
                    'is_active' => true,
                    'created_at' => now()->subDays(rand(1, 10)),
                ]);
            }
        }

        // 6. Seed sample warranty claims
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
