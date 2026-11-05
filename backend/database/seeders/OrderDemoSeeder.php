<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class OrderDemoSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('order_items')->truncate();
        DB::table('orders')->truncate();
        Schema::enableForeignKeyConstraints();

        $mainCustomer = User::where('email', 'customer@techshop.com')->first();
        $otherCustomers = User::where('role', 'customer')
            ->where('email', '!=', 'customer@techshop.com')
            ->get();

        if (!$mainCustomer || $otherCustomers->isEmpty()) {
            $this->command?->error("Vui lòng chạy UserSeeder trước khi chạy OrderDemoSeeder.");
            return;
        }

        $variants = ProductVariant::with(['product.category'])->get();
        if ($variants->isEmpty()) {
            $this->command?->error("Vui lòng nạp ProductSeeder trước khi chạy OrderDemoSeeder.");
            return;
        }

        $coupons = Coupon::all()->keyBy('code');

        // 1. Phân bổ số lượng đơn cho từng khách hàng (Tổng cộng 100 đơn hàng)
        // customer@techshop.com có đúng 5 đơn với 5 trạng thái khác nhau
        $customerOrdersCount = [
            $mainCustomer->id => 5,
        ];

        // 14 khách hàng còn lại chia đều 95 đơn (mỗi khách từ 5 đến 9 đơn)
        $distribution = [9, 8, 8, 8, 7, 7, 7, 7, 7, 6, 6, 5, 5, 5]; // Sum = 95
        foreach ($otherCustomers as $idx => $cust) {
            $customerOrdersCount[$cust->id] = $distribution[$idx] ?? 6;
        }

        // 2. Tạo 100 mốc thời gian đặt hàng rải trong 90 ngày (mật độ cao hơn ở 14 ngày gần nhất)
        $orderTimestamps = [];
        $now = Carbon::now();

        // 8 đơn trong 48 giờ gần nhất (4 pending, 4 processing) cho video demo
        for ($i = 0; $i < 8; $i++) {
            $hoursAgo = rand(1, 46);
            $orderTimestamps[] = $now->copy()->subHours($hoursAgo)->subMinutes(rand(5, 55));
        }

        // 47 đơn trong 14 ngày gần nhất (từ ngày 2 đến ngày 14)
        for ($i = 0; $i < 47; $i++) {
            $daysAgo = rand(2, 14);
            $hour = rand(8, 23);
            if (rand(1, 10) <= 6) {
                $hour = rand(19, 22); // Giờ cao điểm buổi tối
            }
            $orderTimestamps[] = $now->copy()->subDays($daysAgo)->setHour($hour)->setMinute(rand(0, 59))->setSecond(rand(0, 59));
        }

        // 45 đơn từ ngày 15 đến ngày 90
        for ($i = 0; $i < 45; $i++) {
            $daysAgo = rand(15, 90);
            $hour = rand(8, 23);
            $orderTimestamps[] = $now->copy()->subDays($daysAgo)->setHour($hour)->setMinute(rand(0, 59))->setSecond(rand(0, 59));
        }

        // Sắp xếp tăng dần theo thời gian tạo (từ cũ nhất đến mới nhất)
        usort($orderTimestamps, fn($a, $b) => $a->timestamp <=> $b->timestamp);

        // 3. Phân bổ trạng thái chính xác theo tỷ lệ yêu cầu:
        // delivered: 55 (55%), shipped: 12 (12%), processing: 12 (12%), pending: 9 (9%), cancelled: 12 (12%)
        // Đơn cũ (index 0 - 64): 54 delivered, 11 cancelled
        // Đơn trung gian (index 65 - 91): 1 delivered, 12 shipped, 8 processing, 5 pending, 1 cancelled
        // 8 đơn trong 48h (index 92 - 99): 4 pending, 4 processing
        $statuses = [];
        // Cũ nhất: 54 delivered xen kẽ 11 cancelled
        $oldStatuses = array_merge(array_fill(0, 54, 'delivered'), array_fill(0, 11, 'cancelled'));
        shuffle($oldStatuses);
        foreach ($oldStatuses as $st) $statuses[] = $st;

        // Trung gian: 12 shipped, 8 processing, 5 pending, 1 delivered, 1 cancelled
        $midStatuses = array_merge(
            array_fill(0, 12, 'shipped'),
            array_fill(0, 8, 'processing'),
            array_fill(0, 5, 'pending'),
            ['delivered', 'cancelled']
        );
        shuffle($midStatuses);
        foreach ($midStatuses as $st) $statuses[] = $st;

        // 8 đơn cuối cùng (trong 48h): 4 pending, 4 processing
        $recentStatuses = array_merge(array_fill(0, 4, 'pending'), array_fill(0, 4, 'processing'));
        shuffle($recentStatuses);
        foreach ($recentStatuses as $st) $statuses[] = $st;

        // 4. Tạo phân bổ User cho 100 đơn:
        // customer@techshop.com có đúng 5 đơn với 5 trạng thái khác nhau:
        // 1 delivered, 1 shipped, 1 processing, 1 pending, 1 cancelled
        $mainCustomerOrders = [
            'delivered' => false,
            'shipped' => false,
            'processing' => false,
            'pending' => false,
            'cancelled' => false,
        ];

        $userList = [];
        foreach ($customerOrdersCount as $userId => $count) {
            if ($userId === $mainCustomer->id) continue;
            for ($k = 0; $k < $count; $k++) {
                $userList[] = $userId;
            }
        }
        shuffle($userList);

        $ordersData = [];
        $usedSerialNumbers = [];
        $couponUsageCount = [
            'TECHSHOP10' => 0,
            'TECHSHOP50K' => 0,
            'GAMINGVIP' => 0,
            'FREESHIP' => 0,
            'SUMMERSALE' => 0,
            'HETLUOT' => 15,
        ];
        $variantSoldQuantities = [];

        $userListIdx = 0;

        for ($idx = 0; $idx < 100; $idx++) {
            $createdAt = $orderTimestamps[$idx];
            $status = $statuses[$idx];

            // Nếu mainCustomer chưa đủ 5 trạng thái và trạng thái hiện tại khớp với trạng thái cần cho mainCustomer
            $userId = null;
            foreach ($mainCustomerOrders as $reqStatus => $done) {
                if (!$done && $status === $reqStatus) {
                    $userId = $mainCustomer->id;
                    $mainCustomerOrders[$reqStatus] = true;
                    break;
                }
            }
            if ($userId === null) {
                $userId = $userList[$userListIdx++] ?? $otherCustomers->random()->id;
            }

            $user = User::find($userId);
            $userAddress = Address::where('user_id', $userId)->where('is_default', true)->first()
                ?: Address::where('user_id', $userId)->first();

            if (!$userAddress) {
                $userAddress = Address::firstOrCreate(
                    ['user_id' => $userId, 'name' => $user->name],
                    [
                        'phone' => $user->phone ?? '0987654321',
                        'address' => 'Số 123 Đường Nguyễn Trãi, Phường Bến Thành, Quận 1',
                        'city' => 'TP. Hồ Chí Minh',
                        'is_default' => true,
                    ]
                );
            }

            // Số lượng items trong đơn (65% 1 item, 25% 2 items, 7% 3 items, 3% 4 items)
            $itemCountRoll = rand(1, 100);
            $numItems = $itemCountRoll <= 65 ? 1 : ($itemCountRoll <= 90 ? 2 : ($itemCountRoll <= 97 ? 3 : 4));

            $selectedVariants = $variants->random(min($numItems, $variants->count()));
            $orderItemsList = [];
            $itemsSubtotal = 0.0;

            foreach ($selectedVariants as $variant) {
                $catSlug = $variant->product?->category?->slug ?? '';
                $isAccessory = in_array($catSlug, ['chuot', 'ban-phim', 'tai-nghe-am-thanh']);
                $qty = $isAccessory ? rand(1, 2) : 1;

                $price = (float) $variant->price;
                $lineTotal = $price * $qty;
                $itemsSubtotal += $lineTotal;

                // Serial number duy nhất cho điện thoại, laptop, đồng hồ
                $serialNumber = null;
                if (in_array($catSlug, ['dien-thoai', 'laptop', 'dong-ho-thong-minh'])) {
                    do {
                        $serial = 'SN' . strtoupper(Str::random(10));
                    } while (isset($usedSerialNumbers[$serial]));
                    $usedSerialNumbers[$serial] = true;
                    $serialNumber = $serial;
                }

                $orderItemsList[] = [
                    'variant_id' => $variant->id,
                    'quantity' => $qty,
                    'price' => $price,
                    'serial_number' => $serialNumber,
                ];

                // Ghi nhận số lượng đã bán nếu đơn không bị hủy
                if ($status !== 'cancelled') {
                    $variantSoldQuantities[$variant->id] = ($variantSoldQuantities[$variant->id] ?? 0) + $qty;
                }
            }

            // Shipping fee & Coupon
            $shippingFee = $itemsSubtotal >= 500000 ? 0.0 : 30000.0;
            $couponId = null;
            $discountAmount = 0.0;

            // Gắn coupon cho khoảng 25% đơn hàng
            if ($idx % 4 === 0) {
                $eligibleCoupons = [];
                if ($itemsSubtotal >= 500000) $eligibleCoupons[] = 'TECHSHOP10';
                if ($itemsSubtotal >= 300000) $eligibleCoupons[] = 'TECHSHOP50K';
                if ($itemsSubtotal >= 2000000) $eligibleCoupons[] = 'GAMINGVIP';
                if ($itemsSubtotal >= 400000) $eligibleCoupons[] = 'FREESHIP';
                if ($createdAt->lt($now->copy()->subDays(10)) && $itemsSubtotal >= 200000) {
                    $eligibleCoupons[] = 'SUMMERSALE';
                }

                if (!empty($eligibleCoupons)) {
                    $chosenCode = $eligibleCoupons[array_rand($eligibleCoupons)];
                    $cp = $coupons[$chosenCode] ?? null;

                    if ($cp) {
                        $couponId = $cp->id;
                        if ($cp->type === 'percentage') {
                            $calc = ($itemsSubtotal * (float)$cp->value) / 100;
                            $discountAmount = $cp->max_discount ? min((float)$cp->max_discount, $calc) : $calc;
                        } else {
                            $discountAmount = min((float)$cp->value, $itemsSubtotal);
                        }
                        $discountAmount = round($discountAmount, 2);

                        if ($status !== 'cancelled') {
                            $couponUsageCount[$chosenCode] = ($couponUsageCount[$chosenCode] ?? 0) + 1;
                        }
                    }
                }
            }

            // Tổng tiền chuẩn xác tuyệt đối: total = max(0, subtotal - discount) + ship
            $totalPrice = max(0.0, $itemsSubtotal - $discountAmount) + $shippingFee;

            // Payment method & status nhất quán
            $paymentMethodRoll = rand(1, 100);
            $paymentMethod = $paymentMethodRoll <= 45 ? 'cod' : ($paymentMethodRoll <= 85 ? 'vnpay' : 'bank_transfer');

            $paymentTransactionId = null;
            $paymentStatus = 'unpaid';

            if ($status === 'delivered') {
                $paymentStatus = 'paid';
                if ($paymentMethod === 'vnpay') {
                    $paymentTransactionId = '14' . rand(100000000000, 999999999999);
                } elseif ($paymentMethod === 'bank_transfer') {
                    $paymentTransactionId = 'VCB.' . strtoupper(Str::random(8));
                }
            } elseif (in_array($status, ['processing', 'shipped'])) {
                if ($paymentMethod === 'cod') {
                    $paymentStatus = 'unpaid';
                } else {
                    $paymentStatus = 'paid';
                    $paymentTransactionId = $paymentMethod === 'vnpay'
                        ? '14' . rand(100000000000, 999999999999)
                        : 'MB.' . strtoupper(Str::random(8));
                }
            } elseif ($status === 'pending') {
                if ($paymentMethod === 'vnpay') {
                    $paymentStatus = rand(1, 10) <= 6 ? 'pending' : 'failed';
                } elseif ($paymentMethod === 'bank_transfer') {
                    $paymentStatus = 'pending';
                } else {
                    $paymentStatus = 'unpaid';
                }
            } elseif ($status === 'cancelled') {
                if (in_array($paymentMethod, ['vnpay', 'bank_transfer'])) {
                    $paymentStatus = 'refunded';
                    $paymentTransactionId = 'REF.' . strtoupper(Str::random(8));
                } else {
                    $paymentStatus = 'unpaid';
                }
            }

            // GHN code & Updated at
            $ghnCode = null;
            $expectedDeliveryAt = null;
            if (in_array($status, ['shipped', 'delivered'])) {
                $ghnCode = 'GHN' . strtoupper(Str::random(6));
                $expectedDeliveryAt = $createdAt->copy()->addDays(rand(2, 4))->toDateString();
            }

            $updatedAt = match ($status) {
                'delivered' => $createdAt->copy()->addDays(rand(2, 4))->addHours(rand(1, 8)),
                'shipped' => $createdAt->copy()->addDays(rand(1, 2))->addHours(rand(2, 10)),
                'processing' => $createdAt->copy()->addHours(rand(2, 8)),
                default => $createdAt->copy()->addMinutes(rand(15, 90)),
            };
            if ($updatedAt->gt($now)) {
                $updatedAt = $now->copy();
            }

            // Lưu Order
            $order = Order::create([
                'user_id' => $userId,
                'address_id' => $userAddress->id,
                'total_price' => $totalPrice,
                'shipping_fee' => $shippingFee,
                'discount_amount' => $discountAmount,
                'coupon_id' => $couponId,
                'status' => $status,
                'payment_method' => $paymentMethod,
                'payment_status' => $paymentStatus,
                'payment_transaction_id' => $paymentTransactionId,
                'ghn_order_code' => $ghnCode,
                'expected_delivery_at' => $expectedDeliveryAt,
                'created_at' => $createdAt,
                'updated_at' => $updatedAt,
            ]);

            foreach ($orderItemsList as $it) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'variant_id' => $it['variant_id'],
                    'quantity' => $it['quantity'],
                    'price' => $it['price'],
                    'serial_number' => $it['serial_number'],
                    'created_at' => $createdAt,
                    'updated_at' => $updatedAt,
                ]);
            }
        }

        // Cập nhật chính xác used_count cho Coupon theo số đơn thực tế không bị hủy
        foreach ($couponUsageCount as $code => $count) {
            Coupon::where('code', $code)->update(['used_count' => $count]);
        }

        // Cập nhật tồn kho thực tế = Tồn ban đầu (20-100) - Số lượng đã bán
        foreach ($variants as $v) {
            $sold = $variantSoldQuantities[$v->id] ?? 0;
            $baseStock = max(20, $sold + rand(10, 50));
            $finalStock = max(0, $baseStock - $sold);
            $v->update(['stock' => $finalStock]);
        }

        // Điều chỉnh riêng một số biến thể để phục vụ kịch bản demo:
        // - 2 biến thể tồn = 0 (hết hàng): 1 iPhone và 1 Chuột Razer
        $zeroVariant1 = ProductVariant::whereHas('product', fn($q) => $q->where('slug', 'like', '%iphone-16-pro-max%'))->first();
        if ($zeroVariant1) $zeroVariant1->update(['stock' => 0]);

        $zeroVariant2 = ProductVariant::where('sku', 'like', '%RAZER%')->first();
        if ($zeroVariant2) $zeroVariant2->update(['stock' => 0]);

        // - 4 biến thể tồn 1-3 (sắp hết hàng): 1 tai nghe, 1 bàn phím, 1 laptop, 1 Samsung S24
        $lowVariant1 = ProductVariant::where('sku', 'like', '%MARSHALL%')->first();
        if ($lowVariant1) $lowVariant1->update(['stock' => 1]);

        $lowVariant2 = ProductVariant::where('sku', 'like', '%KEYCHRON%')->first();
        if ($lowVariant2) $lowVariant2->update(['stock' => 2]);

        $lowVariant3 = ProductVariant::where('sku', 'like', '%DELL-XPS%')->first();
        if ($lowVariant3) $lowVariant3->update(['stock' => 3]);

        $lowVariant4 = ProductVariant::whereHas('product', fn($q) => $q->where('slug', 'like', '%s24-ultra%'))->first();
        if ($lowVariant4) $lowVariant4->update(['stock' => 2]);

        $this->command?->info("✓ Đã seed thành công 100 đơn hàng demo chuẩn logic & khớp giá tiền 100%.");
    }
}
