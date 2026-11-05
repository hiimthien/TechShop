<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\WarrantyClaim;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class WarrantyClaimSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('warranty_claims')->truncate();
        Schema::enableForeignKeyConstraints();

        // Lấy các order_items thực tế đã delivered và có serial_number
        $deliveredItemsWithSerial = OrderItem::whereNotNull('serial_number')
            ->whereHas('order', fn($q) => $q->where('status', 'delivered'))
            ->with(['order.user', 'order.address', 'variant.product'])
            ->limit(7)
            ->get();

        if ($deliveredItemsWithSerial->isEmpty()) {
            $this->command?->warn("Không tìm thấy order_items có serial để seed warranty claims.");
            return;
        }

        $now = Carbon::now();

        $scenarios = [
            [
                'status' => 'pending',
                'issue' => 'Màn hình thỉnh thoảng xuất hiện 1 vệt sọc xanh nhấp nháy khi mở độ sáng tối đa, máy nguyên vẹn không rơi vỡ.',
                'admin_note' => null,
                'resolved_at' => null,
                'days_ago' => 2,
            ],
            [
                'status' => 'received',
                'issue' => 'Cổng cắm sạc Type-C tiếp xúc lỏng, chập chờn khi cắm dây sạc nhanh zin theo máy.',
                'admin_note' => 'Đã tiếp nhận máy tại chi nhánh Bến Nghé Quận 1. Đang chuẩn bị chuyển giao phòng kỹ thuật.',
                'resolved_at' => null,
                'days_ago' => 4,
            ],
            [
                'status' => 'inspecting',
                'issue' => 'Quạt tản nhiệt bên trái phát ra tiếng kêu rè rè khi máy tải tác vụ đồ họa nặng hoặc render video.',
                'admin_note' => 'Kỹ thuật viên đang tháo máy kiểm tra ổ trục quạt tản nhiệt và tra lại keo gốm tản nhiệt.',
                'resolved_at' => null,
                'days_ago' => 6,
            ],
            [
                'status' => 'inspecting',
                'issue' => 'Cảm biến nhịp tim và đo nồng độ SpO2 ở mặt lưng đồng hồ thỉnh thoảng báo lỗi không nhận diện da.',
                'admin_note' => 'Đã chạy test firmware chẩn đoán từ hãng. Đang chờ module cảm biến thay thế từ kho linh kiện.',
                'resolved_at' => null,
                'days_ago' => 8,
            ],
            [
                'status' => 'completed',
                'issue' => 'Loa ngoài nghe thoại rè khi mở âm lượng trên 80%.',
                'admin_note' => 'Đã thay thế cụm loa chính hãng mới. Đã kiểm tra âm thanh đạt tiêu chuẩn kiểm định của hãng và bàn giao khách.',
                'resolved_at' => $now->copy()->subDays(1),
                'days_ago' => 12,
            ],
            [
                'status' => 'completed',
                'issue' => 'Pin sụt nhanh bất thường sau khi cập nhật hệ điều hành mới, còn khoảng 60% thì tự sập nguồn.',
                'admin_note' => 'Đã thay cell pin chính hãng mới theo chính sách bảo hành 12 tháng. Dung lượng đạt 100% pin tiêu chuẩn.',
                'resolved_at' => $now->copy()->subDays(3),
                'days_ago' => 18,
            ],
            [
                'status' => 'rejected',
                'issue' => 'Máy bị liệt cảm ứng góc trên bên phải, viền máy có vết cấn móp sâu.',
                'admin_note' => 'Từ chối bảo hành miễn phí do thiết bị có dấu hiệu va đập mạnh làm nứt kính và chấn động màn hình (ngoài phạm vi bảo hành lỗi nhà sản xuất). Đã báo giá thay màn hình trợ giá 20% cho khách.',
                'resolved_at' => $now->copy()->subDays(5),
                'days_ago' => 15,
            ],
        ];

        $claimIndex = 1;
        foreach ($deliveredItemsWithSerial as $idx => $item) {
            $scenario = $scenarios[$idx] ?? $scenarios[0];
            $order = $item->order;
            $user = $order?->user;
            $product = $item->variant?->product;

            $claimDate = $now->copy()->subDays($scenario['days_ago'])->setHour(rand(9, 17))->setMinute(rand(0, 59));
            $purchaseDate = $order ? $order->created_at->toDateString() : $claimDate->copy()->subDays(30)->toDateString();

            WarrantyClaim::create([
                'claim_code' => 'BH-' . strtoupper(Str::random(6)),
                'user_id' => $user?->id,
                'order_id' => $order?->id,
                'customer_name' => $user ? $user->name : 'Khách hàng TechShop',
                'customer_phone' => $user ? $user->phone : ($order?->address?->phone ?? '0987654321'),
                'customer_email' => $user ? $user->email : 'customer@techshop.com',
                'product_name' => $product ? $product->name : 'Thiết bị TechShop',
                'serial_number' => $item->serial_number,
                'purchase_date' => $purchaseDate,
                'issue_description' => $scenario['issue'],
                'status' => $scenario['status'],
                'admin_note' => $scenario['admin_note'],
                'resolved_at' => $scenario['resolved_at'],
                'created_at' => $claimDate,
                'updated_at' => $scenario['resolved_at'] ?? $claimDate,
            ]);

            $claimIndex++;
        }

        $this->command?->info("✓ Đã seed " . ($claimIndex - 1) . " phiếu bảo hành gắn CHÍNH XÁC với serial_number và đơn hàng delivered.");
    }
}

