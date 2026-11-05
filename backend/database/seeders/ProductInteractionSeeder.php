<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Product;
use App\Models\ProductQuestion;
use App\Models\ProductReview;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ProductInteractionSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('product_reviews')->truncate();
        DB::table('product_questions')->truncate();
        Schema::enableForeignKeyConstraints();

        $now = Carbon::now();

        // 1. Kho nội dung Review theo từng ngành hàng
        $reviewPools = [
            'dien-thoai' => [
                5 => [
                    'Máy mua đập hộp rất ưng ý, đóng gói kỹ càng, giao hàng hỏa tốc. Nhân viên TechShop tư vấn tận tình, camera chụp đêm xuất sắc!',
                    'Màu máy ở ngoài đẹp hơn trên ảnh, viền mỏng sang trọng, cầm đầm tay. Màn hình 120Hz ProMotion siêu mượt mà!',
                    'Hàng chính hãng VN/A nguyên seal kích hoạt chuẩn chỉ. Pin trâu dùng onscreen liên tục cả ngày không hết.',
                    'Chip xử lý siêu mượt, mở nhiều app đa nhiệm không hề giật lag. Loa kép to rõ và âm thanh sống động.',
                ],
                4 => [
                    'Sản phẩm dùng tốt, camera quay video chống rung cực đỉnh. Chỉ có điều hộp máy hơi xước nhẹ bên ngoài do vận chuyển nhưng seal nguyên vẹn.',
                    'Máy đẹp, hiệu năng khỏe, sạc nhanh. Giá tại TechShop đang tốt nhất phân khúc.',
                ],
                3 => [
                    'Máy dùng ổn định, tuy nhiên chơi game nặng liên tục khoảng 45 phút thì mặt lưng hơi ấm lên một chút.',
                ],
                2 => [
                    'Máy dùng tốt nhưng shipper giao hàng trễ hơn hẹn 1 buổi khiến mình phải dời lịch công việc.',
                ]
            ],
            'laptop' => [
                5 => [
                    'Hiệu năng render video 4K và đồ họa 3D siêu mượt. Màn hình chuẩn màu phục vụ thiết kế cực kỳ chuẩn xác.',
                    'Khung nhôm nguyên khối CNC sắc sảo, bàn phím gõ nảy đầm tay, touchpad rê mượt như lụa. Pin dùng tác vụ văn phòng được gần 10 tiếng!',
                    'Cấu hình quái vật, chiến mượt mà mọi tựa game AAA ở thiết lập Ultra Setting. Tản nhiệt thông minh rất êm.',
                ],
                4 => [
                    'Máy khởi động vài giây là lên, màn hình tần số quét cao xem phim rất đã mắt. Hơi nặng một chút khi mang balo đi làm.',
                ],
                3 => [
                    'Cấu hình đúng mô tả, tuy nhiên quạt tản nhiệt khi tải nặng 100% CPU hơi ồn một chút trong phòng kín.',
                ]
            ],
            'chuot' => [
                5 => [
                    'Form chuột công thái học cầm ôm sát lòng bàn tay, rê cực kỳ chính xác. Switch bấm nảy giòn, con lăn mượt.',
                    'Kết nối không dây 2.4GHz siêu nhạy không có độ trễ, pin dùng cả tháng mới phải sạc lại. Rất đáng đồng tiền.',
                ],
                4 => [
                    'Chuột nhẹ, feet lướt êm ái trên lót chuột. Phần mềm tùy chỉnh DPI và macro trực quan dễ dùng.',
                ]
            ],
            'ban-phim' => [
                5 => [
                    'Switch gõ êm mượt, âm thanh đanh gọn không bị vang kim loại. Keycap PBT dày dặn chống bám mồ hôi cực tốt.',
                    'Bàn phím build cứng cáp, gasket mount gõ rất êm tay không mỏi khi làm việc 8 tiếng. Led RGB sáng đều.',
                ],
                4 => [
                    'Kết nối Bluetooth chuyển đổi nhanh giữa Mac và Windows rất tiện lợi. Đóng gói rất cẩn thận.',
                ]
            ],
            'man-hinh' => [
                5 => [
                    'Tần số quét 165Hz chơi game bắn súng FPS cực mượt, không hề có hiện tượng bóng ma (ghosting). Màu sắc rực rỡ.',
                    'Độ phân giải 2K/4K siêu nét, viền màn hình siêu mỏng, chân đế nâng hạ xoay 90 độ tiện lợi cho coder và designer.',
                ],
                4 => [
                    'Màn hình sáng đẹp, không có điểm chết pixel nào. TechShop hỗ trợ kiểm tra màn tại chỗ rất an tâm.',
                ]
            ],
            'tai-nghe-am-thanh' => [
                5 => [
                    'Công nghệ chống ồn chủ động ANC đỉnh chóp, đeo vào là cách ly hoàn toàn với tiếng ồn xe cộ ngoài phố. Âm bass sâu chắc.',
                    'Chất âm Marshall mộc mạc đặc trưng, nghe acoustic và rock cực phê. Thiết kế cổ điển retro sang trọng.',
                ],
                4 => [
                    'Đệm tai giả da cao cấp đeo 3-4 tiếng liên tục không bị cấn tai hay nóng bức. Micro đàm thoại lọc gió tốt.',
                ],
                1 => [
                    'Chất âm khá tốt nhưng tai nghe kích thước to hơn mình tưởng tượng, đeo tập thể thao dễ bị tuột.',
                ]
            ],
            'dong-ho-thong-minh' => [
                5 => [
                    'GPS bắt sóng siêu nhanh và đo quãng đường chạy bộ chính xác tuyệt đối. Cảm biến đo nhịp tim và giấc ngủ chi tiết.',
                    'Mặt kính Sapphire chống trầy xước va đập cực tốt, hiển thị ngoài trời nắng gắt vẫn rõ ràng. Pin trâu.',
                ],
                4 => [
                    'Nhận thông báo cuộc gọi, tin nhắn Zalo tiếng Việt đầy đủ không lỗi font. Dây đeo silicon mềm mại.',
                ]
            ],
        ];

        // 2. Tạo Verified Reviews từ các đơn hàng ĐÃ GIAO THÀNH CÔNG (Delivered Orders)
        $deliveredOrders = Order::where('status', 'delivered')
            ->with(['user', 'items.variant.product.category'])
            ->get();

        $reviewCount = 0;
        $reviewedProductUserPairs = [];

        foreach ($deliveredOrders as $order) {
            $user = $order->user;
            if (!$user) continue;

            foreach ($order->items as $item) {
                $product = $item->variant?->product;
                if (!$product) continue;

                $pairKey = "{$user->id}_{$product->id}";
                if (isset($reviewedProductUserPairs[$pairKey])) continue;
                $reviewedProductUserPairs[$pairKey] = true;

                $catSlug = $product->category?->slug ?? 'dien-thoai';
                $pool = $reviewPools[$catSlug] ?? $reviewPools['dien-thoai'];

                // Phân bổ sao: 70% 5 sao, 20% 4 sao, 7% 3 sao, 3% 1-2 sao
                $roll = rand(1, 100);
                $rating = $roll <= 70 ? 5 : ($roll <= 90 ? 4 : ($roll <= 97 ? 3 : (rand(1, 2) === 1 ? 2 : 1)));

                $contentsList = $pool[$rating] ?? ($pool[5] ?? ['Sản phẩm rất tốt, chính hãng 100%!']);
                $content = $contentsList[array_rand($contentsList)];

                // Ngày review phải SAU ngày giao hàng
                $reviewDate = $order->updated_at->copy()->addDays(rand(1, 5))->addHours(rand(1, 10));
                if ($reviewDate->gt($now)) {
                    $reviewDate = $now->copy()->subHours(rand(2, 24));
                }

                // 40% có phản hồi từ Admin
                $hasReply = (rand(1, 100) <= 40);
                $adminReply = null;
                $repliedAt = null;
                if ($hasReply) {
                    $replies = [
                        'Dạ TechShop chân thành cảm ơn quý khách đã tin tưởng và đánh giá tích cực. Chúc bạn có những trải nghiệm tuyệt vời cùng sản phẩm mới ạ!',
                        'Dạ TechShop cảm ơn bạn rất nhiều! Trong quá trình sử dụng nếu cần hỗ trợ kỹ thuật hoặc phụ kiện, bạn cứ nhắn lại hotline bên em nhé!',
                        'Dạ TechShop rất vui khi bạn hài lòng về sản phẩm và dịch vụ. Chúc bạn một ngày làm việc tràn đầy năng lượng ạ!',
                    ];
                    $adminReply = $replies[array_rand($replies)];
                    $repliedAt = $reviewDate->copy()->addHours(rand(3, 24));
                    if ($repliedAt->gt($now)) $repliedAt = $now->copy();
                }

                ProductReview::create([
                    'product_id' => $product->id,
                    'user_id' => $user->id,
                    'customer_name' => $user->name,
                    'customer_phone' => $user->phone ?? '098****214',
                    'rating' => $rating,
                    'content' => $content,
                    'admin_reply' => $adminReply,
                    'replied_at' => $repliedAt,
                    'is_verified_purchase' => true,
                    'is_active' => true,
                    'created_at' => $reviewDate,
                    'updated_at' => $repliedAt ?? $reviewDate,
                ]);

                $reviewCount++;
                if ($reviewCount >= 36) break 2;
            }
        }

        // Thêm 4 đánh giá từ khách vãng lai (user_id = NULL => is_verified_purchase = false)
        $guestProducts = Product::inRandomOrder()->limit(4)->get();
        $guestReviews = [
            ['name' => 'Nguyễn Thị Hồng', 'phone' => '093****889', 'rating' => 5, 'content' => 'Mình mua tặng sinh nhật em trai, máy rất đẹp và nguyên seal xịn sò.'],
            ['name' => 'Trần Văn Mạnh', 'phone' => '091****445', 'rating' => 4, 'content' => 'Trải nghiệm mượt mà, giá bán lẻ rất cạnh tranh so với các chuỗi lớn khác.'],
            ['name' => 'Phạm Quốc Huy', 'phone' => '097****332', 'rating' => 5, 'content' => 'Nhân viên tư vấn nhiệt tình, đóng gói bọc xốp bóng khí nhiều lớp rất cẩn thận.'],
            ['name' => 'Lê Thanh Mai', 'phone' => '090****776', 'rating' => 4, 'content' => 'Thiết kế đẹp tinh tế, màu sắc ở ngoài chân thật đúng như mong đợi.'],
        ];

        foreach ($guestProducts as $gIdx => $gProd) {
            $gData = $guestReviews[$gIdx];
            $gDate = $now->copy()->subDays(rand(3, 25))->setHour(rand(9, 21));

            ProductReview::create([
                'product_id' => $gProd->id,
                'user_id' => null,
                'customer_name' => $gData['name'],
                'customer_phone' => $gData['phone'],
                'rating' => $gData['rating'],
                'content' => $gData['content'],
                'admin_reply' => 'Dạ TechShop cảm ơn bạn đã dành thời gian đánh giá sản phẩm ạ!',
                'replied_at' => $gDate->copy()->addHours(6),
                'is_verified_purchase' => false,
                'is_active' => true,
                'created_at' => $gDate,
                'updated_at' => $gDate,
            ]);
            $reviewCount++;
        }

        // 3. Tạo 12 câu hỏi đáp Q&A (trong đó 8-9 câu đã trả lời, 3-4 câu chưa trả lời)
        $qaProducts = Product::inRandomOrder()->limit(12)->get();
        $qaTemplates = [
            // Các câu đã trả lời
            [
                'name' => 'Vũ Hải Đăng',
                'q' => 'Sản phẩm này trong hộp có tặng kèm củ sạc nhanh và ốp lưng không shop?',
                'a' => 'Dạ chào bạn, hộp sản phẩm chuẩn của nhà sản xuất bao gồm máy và cáp sạc Type-C. Khi mua máy tại TechShop bạn sẽ được tặng kèm voucher giảm giá 30% khi mua kèm củ sạc chính hãng và ốp lưng cao cấp ạ!',
            ],
            [
                'name' => 'Phạm Kiều Oanh',
                'q' => 'Em muốn mua trả góp 0% thì cần những giấy tờ gì và xét duyệt trong bao lâu ạ?',
                'a' => 'Dạ chào bạn, bạn chỉ cần có Căn cước công dân (CCCD) gắn chip là có thể làm thủ tục duyệt hồ sơ online trong 5 - 10 phút, hoặc trả góp 0% qua thẻ tín dụng hơn 25 ngân hàng liên kết cực kỳ tiện lợi ạ!',
            ],
            [
                'name' => 'Bùi Gia Huy',
                'q' => 'Máy mua tại TechShop có được bảo hành chính hãng tại trung tâm ủy quyền Apple / Samsung không?',
                'a' => 'Dạ hoàn toàn chính xác ạ! 100% sản phẩm tại TechShop là hàng chính hãng phân phối chính thức tại Việt Nam (mã VN/A với Apple), được bảo hành tại tất cả các Trung tâm bảo hành ủy quyền chính hãng trên toàn quốc ạ.',
            ],
            [
                'name' => 'Đỗ Minh Trí',
                'q' => 'Đặt giao hàng về Cần Thơ thì mất bao lâu nhận được máy ạ?',
                'a' => 'Dạ khu vực các tỉnh miền Tây như Cần Thơ thì TechShop giao qua đường bay bưu cục hỏa tốc chỉ mất khoảng 24 - 36 giờ là bạn nhận được hàng tận tay ạ.',
            ],
            [
                'name' => 'Nguyễn Thanh Hằng',
                'q' => 'Sản phẩm này có sẵn máy trải nghiệm tại cửa hàng số 142 Hai Bà Trưng, Quận 1 không shop?',
                'a' => 'Dạ chi nhánh Hai Bà Trưng Quận 1 luôn có sẵn bàn trải nghiệm máy demo đầy đủ màu sắc để bạn đến test thử thực tế trước khi mua ạ!',
            ],
            [
                'name' => 'Lê Hoàng Long',
                'q' => 'Bản RAM 16GB này sau có nâng cấp thêm thanh RAM nữa được không shop?',
                'a' => 'Dạ mẫu máy này trang bị RAM onboard hàn trực tiếp trên bo mạch để tối ưu băng thông và độ mỏng nhẹ, nên sẽ không nâng cấp thêm RAM được ạ. Bạn nên cân nhắc chọn luôn bản RAM 32GB nếu làm đồ họa nặng nhé!',
            ],
            [
                'name' => 'Trần Thị Thu Hà',
                'q' => 'Bàn phím này có kết nối được cùng lúc 3 thiết bị máy tính và iPad không ạ?',
                'a' => 'Dạ có ạ! Bàn phím hỗ trợ 3 profile Bluetooth riêng biệt (Fn + 1/2/3) cho phép bạn chuyển đổi qua lại giữa laptop, iPad và điện thoại chỉ trong 1 giây ạ.',
            ],
            [
                'name' => 'Nguyễn Minh Tuấn',
                'q' => 'Tai nghe này chống ồn có lọc được tiếng gió khi đi xe máy hoặc chạy bộ không?',
                'a' => 'Dạ tai nghe có chế độ Wind Noise Reduction chuyên dụng với màng lọc micro đa hướng, giúp giảm tiếng ồn gió rít rất hiệu quả khi bạn chạy bộ hoặc đi ngoài đường ạ.',
            ],

            // 4 câu CHƯA trả lời để Admin demo trả lời trên video
            [
                'name' => 'Đặng Văn Đức',
                'q' => 'Màu Titan Sa Mạc này hiện tại chi nhánh Hà Nội có sẵn hàng lấy ngay trong ngày được không shop?',
                'a' => null,
            ],
            [
                'name' => 'Cao Thanh Thảo',
                'q' => 'Shop cho mình hỏi học sinh sinh viên có được ưu đãi giảm thêm khi mua laptop này không?',
                'a' => null,
            ],
            [
                'name' => 'Dương Đình Toàn',
                'q' => 'Đồng hồ này có tính năng cảnh báo té ngã và gọi cứu hộ khẩn cấp tại Việt Nam không shop?',
                'a' => null,
            ],
            [
                'name' => 'Võ Hoàng Nam',
                'q' => 'Mình có chiếc iPad Air 4 cũ muốn thu cũ đổi mới lên dòng này thì quy trình thế nào ạ?',
                'a' => null,
            ],
        ];

        foreach ($qaTemplates as $qIdx => $qa) {
            $p = $qaProducts[$qIdx] ?? $qaProducts->first();
            $qDate = $now->copy()->subDays(rand(1, 20))->setHour(rand(8, 22));
            $aDate = $qa['a'] ? $qDate->copy()->addHours(rand(1, 8)) : null;

            ProductQuestion::create([
                'product_id' => $p->id,
                'customer_name' => $qa['name'],
                'question' => $qa['q'],
                'admin_answer' => $qa['a'],
                'answered_at' => $aDate,
                'created_at' => $qDate,
                'updated_at' => $aDate ?? $qDate,
            ]);
        }

        $this->command?->info("✓ Đã seed {$reviewCount} đánh giá chuẩn ngành hàng (kèm verified purchase thực tế) và 12 hỏi đáp Q&A.");
    }
}

