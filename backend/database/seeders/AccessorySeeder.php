<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Support\SeedImage;
use Database\Seeders\Concerns\SeedsProductCatalog;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AccessorySeeder extends Seeder
{
    use SeedsProductCatalog;

    public function run(): void
    {
        $catalog = [
            // ==================== 1. CHUỘT MÁY TÍNH (category: chuot) ====================
            [
                'name' => 'Chuột Công Thái Học Không Dây Logitech MX Master 3S',
                'category_slug' => 'chuot',
                'brand_slug' => 'logitech',
                'description' => 'Chuột công thái học cao cấp số 1 thế giới dành cho lập trình viên và nhà sáng tạo nội dung. Cảm biến Darkfield 8000 DPI lướt trên mọi bề mặt kể cả mặt kính cường lực, con lăn điện từ MagSpeed cuộn 1000 dòng/giây siêu êm cùng nút bấm Quiet Clicks giảm 90% tiếng ồn.',
                'variants' => [
                    [
                        'name' => 'Đen Than Chì (Graphite)',
                        'sku' => 'LOGI-MXM3S-GRAPHITE',
                        'price' => 2490000,
                        'stock' => 50,
                        'image_url' => SeedImage::for('chuot-cong-thai-hoc-khong-day-logitech-mx-master-3s', 'den-than-chi-graphite'),
                    ],
                    [
                        'name' => 'Xám Bạc (Pale Gray)',
                        'sku' => 'LOGI-MXM3S-GRAY',
                        'price' => 2490000,
                        'stock' => 40,
                        'image_url' => SeedImage::for('chuot-cong-thai-hoc-khong-day-logitech-mx-master-3s', 'xam-bac-pale-gray'),
                    ],
                ],
            ],
            [
                'name' => 'Chuột Gaming Không Dây Siêu Nhẹ Logitech G Pro X Superlight 2',
                'category_slug' => 'chuot',
                'brand_slug' => 'logitech',
                'description' => 'Biểu tượng chiến thắng trên các đấu trường eSports quốc tế. Trọng lượng siêu nhẹ chỉ 60g, cảm biến HERO 2 độ chính xác hơn 44.000 DPI, switch lai quang-cơ LIGHTFORCE phản hồi tốc độ ánh sáng và tần số gửi tín hiệu không dây 4000Hz.',
                'variants' => [
                    [
                        'name' => 'Trắng Tuyết (White)',
                        'sku' => 'LOGI-GPX2-WHITE',
                        'price' => 3690000,
                        'stock' => 30,
                        'image_url' => SeedImage::for('chuot-gaming-khong-day-sieu-nhe-logitech-g-pro-x-superlight-2', 'trang-tuyet-white'),
                    ],
                    [
                        'name' => 'Đen Nhám (Black)',
                        'sku' => 'LOGI-GPX2-BLACK',
                        'price' => 3690000,
                        'stock' => 35,
                        'image_url' => SeedImage::for('chuot-gaming-khong-day-sieu-nhe-logitech-g-pro-x-superlight-2', 'den-nham-black'),
                    ],
                ],
            ],
            [
                'name' => 'Chuột Gaming Huyền Thoại Logitech G502 X PLUS RGB Lightspeed',
                'category_slug' => 'chuot',
                'brand_slug' => 'logitech',
                'description' => 'Hậu duệ nâng cấp toàn diện của mẫu chuột bán chạy nhất mọi thời đại. Dải đèn RGB LIGHTSYNC 8 vùng sáng rực rỡ, 13 nút điều khiển có thể lập trình macro, nút chuyển DPI có thể tháo rời đảo chiều và thời lượng pin 120 giờ liên tục.',
                'variants' => [
                    [
                        'name' => 'Đen Huyền Bí',
                        'sku' => 'LOGI-G502X-BLACK',
                        'price' => 3490000,
                        'stock' => 30,
                        'image_url' => SeedImage::for('chuot-gaming-huyen-thoai-logitech-g502-x-plus-rgb-lightspeed', 'den-huyen-bi'),
                    ],
                    [
                        'name' => 'Trắng Băng Tuyết',
                        'sku' => 'LOGI-G502X-WHITE',
                        'price' => 3490000,
                        'stock' => 25,
                        'image_url' => SeedImage::for('chuot-gaming-huyen-thoai-logitech-g502-x-plus-rgb-lightspeed', 'trang-bang-tuyet'),
                    ],
                ],
            ],
            [
                'name' => 'Chuột Gaming Không Dây Razer DeathAdder V3 Pro',
                'category_slug' => 'chuot',
                'brand_slug' => 'razer',
                'description' => 'Chuột gaming công thái học eSports hàng đầu với trọng lượng siêu nhẹ 63g. Cảm biến quang học Razer Focus Pro 30K DPI, switch chuột quang học Razer Gen 3 tuổi thọ 90 triệu lần bấm không bị double-click và tương thích Dongle không dây 8000Hz HyperPolling.',
                'variants' => [
                    [
                        'name' => 'Đen Classic Black',
                        'sku' => 'RAZER-DAV3P-BLACK',
                        'price' => 3590000,
                        'stock' => 35,
                        'image_url' => SeedImage::for('chuot-gaming-khong-day-razer-deathadder-v3-pro', 'den-classic-black'),
                    ],
                    [
                        'name' => 'Trắng Mercury White',
                        'sku' => 'RAZER-DAV3P-WHITE',
                        'price' => 3590000,
                        'stock' => 30,
                        'image_url' => SeedImage::for('chuot-gaming-khong-day-razer-deathadder-v3-pro', 'trang-mercury-white'),
                    ],
                ],
            ],

            // ==================== 2. BÀN PHÍM CƠ (category: ban-phim) ====================
            [
                'name' => 'Bàn Phím Cơ Không Dây Tùy Biến Keychron Q1 Pro Max QMK/VIA',
                'category_slug' => 'ban-phim',
                'brand_slug' => 'keychron',
                'description' => 'Đỉnh cao bàn phím cơ custom vỏ nhôm CNC nguyên khối với núm xoay điều khiển đa năng. Cấu trúc Gasket Mount êm ái, kết nối 3 chế độ không dây 2.4GHz / Bluetooth 5.1 / Type-C, mạch hotswap 5 pin và tương thích hoàn hảo cả hệ điều hành macOS và Windows.',
                'variants' => [
                    [
                        'name' => 'Vỏ Nhôm Đen Carbon - Red Switch (Gõ êm nhẹ)',
                        'sku' => 'KC-Q1P-RED-BLK',
                        'price' => 4590000,
                        'stock' => 20,
                        'image_url' => SeedImage::for('ban-phim-co-khong-day-tuy-bien-keychron-q1-pro-max-qmkvia', 'vo-nhom-den-carbon-red-switch-go-em-nhe'),
                    ],
                    [
                        'name' => 'Vỏ Nhôm Bạc Shell White - Brown Switch (Khấc nhẹ đầm tay)',
                        'sku' => 'KC-Q1P-BRN-SLV',
                        'price' => 4590000,
                        'stock' => 25,
                        'image_url' => SeedImage::for('ban-phim-co-khong-day-tuy-bien-keychron-q1-pro-max-qmkvia', 'vo-nhom-bac-shell-white-brown-switch-khac-nhe-dam-tay'),
                    ],
                ],
            ],
            [
                'name' => 'Bàn Phím Cơ Không Dây Keychron K2 Pro QMK/VIA',
                'category_slug' => 'ban-phim',
                'brand_slug' => 'keychron',
                'description' => 'Layout 75% nhỏ gọn kinh điển dành cho dân văn phòng và coder. Keycap PBT Double-shot OSA profile không phai màu, mạch xuôi South-Facing RGB lung linh và pin 4000mAh sử dụng liên tục hàng tháng.',
                'variants' => [
                    [
                        'name' => 'Khung Nhôm LED RGB - Red Switch',
                        'sku' => 'KC-K2PRO-RED',
                        'price' => 2290000,
                        'stock' => 45,
                        'image_url' => SeedImage::for('ban-phim-co-khong-day-keychron-k2-pro-qmkvia', 'khung-nhom-led-rgb-red-switch'),
                    ],
                    [
                        'name' => 'Khung Nhôm LED RGB - Brown Switch',
                        'sku' => 'KC-K2PRO-BROWN',
                        'price' => 2290000,
                        'stock' => 40,
                        'image_url' => SeedImage::for('ban-phim-co-khong-day-keychron-k2-pro-qmkvia', 'khung-nhom-led-rgb-brown-switch'),
                    ],
                ],
            ],
            [
                'name' => 'Bàn Phím Cơ Gaming Logitech G Pro X TKL Lightspeed',
                'category_slug' => 'ban-phim',
                'brand_slug' => 'logitech',
                'description' => 'Bàn phím cơ thi đấu không dây Tenkeyless được thiết kế cùng các game thủ hàng đầu thế giới. Công nghệ không dây Lightspeed độ trễ cực thấp, keycap PBT cao cấp, con lăn chỉnh âm lượng và túi đựng du lịch chống va đập đi kèm.',
                'variants' => [
                    [
                        'name' => 'Trắng Tinh Khiết - Tactile Brown Switch',
                        'sku' => 'LOGI-GPXTKL-WHT',
                        'price' => 4290000,
                        'stock' => 25,
                        'image_url' => SeedImage::for('ban-phim-co-gaming-logitech-g-pro-x-tkl-lightspeed', 'trang-tinh-khiet-tactile-brown-switch'),
                    ],
                    [
                        'name' => 'Đen Nhám - Linear Red Switch',
                        'sku' => 'LOGI-GPXTKL-BLK',
                        'price' => 4290000,
                        'stock' => 30,
                        'image_url' => SeedImage::for('ban-phim-co-gaming-logitech-g-pro-x-tkl-lightspeed', 'den-nham-linear-red-switch'),
                    ],
                ],
            ],
            [
                'name' => 'Bàn Phím Cơ Gaming Razer BlackWidow V4 Pro RGB',
                'category_slug' => 'ban-phim',
                'brand_slug' => 'razer',
                'description' => 'Cỗ máy điều khiển trung tâm góc máy chơi game. Núm xoay điều khiển Razer Command Dial đa năng, 8 phím macro chuyên dụng, đệm kê tay bọc da êm ái tích hợp dải LED gầm Razer Chroma RGB 3 phía cực kỳ rực rỡ.',
                'variants' => [
                    [
                        'name' => 'Đen Chroma - Green Switch (Clicky giòn tan)',
                        'sku' => 'RAZER-BWV4P-GRN',
                        'price' => 5490000,
                        'stock' => 15,
                        'image_url' => SeedImage::for('ban-phim-co-gaming-razer-blackwidow-v4-pro-rgb', 'den-chroma-green-switch-clicky-gion-tan'),
                    ],
                    [
                        'name' => 'Đen Chroma - Yellow Switch (Linear êm mượt)',
                        'sku' => 'RAZER-BWV4P-YEL',
                        'price' => 5490000,
                        'stock' => 20,
                        'image_url' => SeedImage::for('ban-phim-co-gaming-razer-blackwidow-v4-pro-rgb', 'den-chroma-yellow-switch-linear-em-muot'),
                    ],
                ],
            ],

            // ==================== 3. MÀN HÌNH MÁY TÍNH (category: man-hinh) ====================
            [
                'name' => 'Màn Hình Dell UltraSharp U2724D 27 inch 2K 120Hz IPS Black',
                'category_slug' => 'man-hinh',
                'brand_slug' => 'dell',
                'description' => 'Màn hình chuyên đồ họa chuẩn mực hàng đầu thế giới. Tấm nền IPS Black độ tương phản 2000:1 sâu thẳm, tần số quét mượt mà 120Hz, cảm biến ánh sáng môi trường tự động điều chỉnh và cổng kết nối Thunderbolt / USB-C truyền hình ảnh và sạc nhanh 90W.',
                'variants' => [
                    [
                        'name' => 'Bạch Kim Platinum - 27 inch 2K QHD 120Hz',
                        'sku' => 'DELL-U2724D-27-2K',
                        'price' => 11490000,
                        'stock' => 30,
                        'image_url' => SeedImage::for('man-hinh-dell-ultrasharp-u2724d-27-inch-2k-120hz-ips-black', 'bach-kim-platinum-27-inch-2k-qhd-120hz'),
                    ],
                ],
            ],
            [
                'name' => 'Màn Hình Cong Gaming Dell Alienware AW3423DWF QD-OLED',
                'category_slug' => 'man-hinh',
                'brand_slug' => 'dell',
                'description' => 'Tuyệt tác màn hình cong siêu rộng 34 inch tỉ lệ 21:9 chuẩn công nghệ Quantum Dot OLED. Độ tương phản vô cực, thời gian phản hồi siêu tốc 0.1ms, tần số quét 165Hz và chuẩn HDR TrueBlack 400 mang đến trải nghiệm điện ảnh choáng ngợp.',
                'variants' => [
                    [
                        'name' => 'Trắng Lunar Light - 34 inch WQHD 165Hz QD-OLED',
                        'sku' => 'DELL-AW3423DWF-34',
                        'price' => 26990000,
                        'stock' => 10,
                        'image_url' => SeedImage::for('man-hinh-cong-gaming-dell-alienware-aw3423dwf-qd-oled', 'trang-lunar-light-34-inch-wqhd-165hz-qd-oled'),
                    ],
                ],
            ],
            [
                'name' => 'Màn Hình Gaming ASUS ROG Swift OLED PG27AQDM 240Hz',
                'category_slug' => 'man-hinh',
                'brand_slug' => 'asus',
                'description' => 'Vũ khí tối thượng cho game bắn súng FPS eSports. Tấm nền 27 inch 2K OLED tần số quét 240Hz tốc độ phản hồi 0.03ms, hệ thống tản nhiệt buồng hơi tùy chỉnh chống hiện tượng burn-in và độ sáng vượt trội 1000 nits.',
                'variants' => [
                    [
                        'name' => 'Đen ROG Gaming - 27 inch 2K OLED 240Hz',
                        'sku' => 'ROG-PG27AQDM-27-240',
                        'price' => 24490000,
                        'stock' => 15,
                        'image_url' => SeedImage::for('man-hinh-gaming-asus-rog-swift-oled-pg27aqdm-240hz', 'den-rog-gaming-27-inch-2k-oled-240hz'),
                    ],
                ],
            ],
            [
                'name' => 'Màn Hình Đồ Họa Chuyên Nghiệp ASUS ProArt PA278CV 2K',
                'category_slug' => 'man-hinh',
                'brand_slug' => 'asus',
                'description' => 'Được cân chỉnh màu sắc chính xác tại nhà máy đạt chứng nhận Calman Verified với độ sai lệch màu Delta E < 2. Chuẩn màu 100% sRGB và 100% Rec. 709, chân đế công thái học xoay 90 độ dọc linh hoạt và cổng USB-C sạc 65W cho laptop.',
                'variants' => [
                    [
                        'name' => 'Bạc Đen ProArt - 27 inch 2K IPS 75Hz',
                        'sku' => 'ASUS-PA278CV-27-2K',
                        'price' => 8490000,
                        'stock' => 35,
                        'image_url' => SeedImage::for('man-hinh-do-hoa-chuyen-nghiep-asus-proart-pa278cv-2k', 'bac-den-proart-27-inch-2k-ips-75hz'),
                    ],
                ],
            ],
        ];

        $this->seedProducts($catalog);
    }
}
