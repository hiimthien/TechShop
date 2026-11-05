<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Support\SeedImage;
use Database\Seeders\Concerns\SeedsProductCatalog;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AudioSeeder extends Seeder
{
    use SeedsProductCatalog;

    public function run(): void
    {
        $catalog = [
            // ==================== APPLE ====================
            [
                'name' => 'Tai Nghe Apple AirPods Pro Gen 2 (MagSafe USB-C)',
                'category_slug' => 'tai-nghe-am-thanh',
                'brand_slug' => 'apple',
                'description' => 'Tai nghe không dây chống ồn chủ động tốt nhất hệ sinh thái Apple. Chip xử lý Apple H2 khử tiếng ồn gấp 2 lần, chế độ Xuyên Âm Thích Ứng (Adaptive Audio) tự động điều tiết theo môi trường, âm thanh không gian Spatial Audio cá nhân hóa và hộp sạc chuẩn USB-C có loa phát tín hiệu tìm kiếm.',
                'variants' => [
                    [
                        'name' => 'Trắng Tinh Khiết - Hộp Sạc MagSafe USB-C',
                        'sku' => 'APP2-USBC-WHITE',
                        'price' => 5690000,
                        'stock' => 80,
                        'image_url' => SeedImage::for('tai-nghe-apple-airpods-pro-gen-2-magsafe-usb-c', 'trang-tinh-khiet-hop-sac-magsafe-usb-c'),
                    ],
                ],
            ],
            [
                'name' => 'Tai Nghe Chụp Tai Apple AirPods Max',
                'category_slug' => 'tai-nghe-am-thanh',
                'brand_slug' => 'apple',
                'description' => 'Đỉnh cao tai nghe over-ear cao cấp với khung vòm bằng lưới thoáng khí và chụp tai bằng nhôm anodized nguyên khối. Driver dynamic do Apple thiết kế mang đến âm thanh độ trung thực cao cùng tính năng Spatial Audio theo dõi chuyển động đầu sống động như rạp phim.',
                'variants' => [
                    [
                        'name' => 'Xám Không Gian (Space Gray)',
                        'sku' => 'APMAX-GRAY',
                        'price' => 12990000,
                        'stock' => 20,
                        'image_url' => SeedImage::for('tai-nghe-chup-tai-apple-airpods-max', 'xam-khong-gian-space-gray'),
                    ],
                    [
                        'name' => 'Bạc Ánh Kim (Silver)',
                        'sku' => 'APMAX-SILVER',
                        'price' => 12990000,
                        'stock' => 15,
                        'image_url' => SeedImage::for('tai-nghe-chup-tai-apple-airpods-max', 'bac-anh-kim-silver'),
                    ],
                ],
            ],

            // ==================== SONY ====================
            [
                'name' => 'Tai Nghe Chống Ồn Không Dây Sony WH-1000XM5',
                'category_slug' => 'tai-nghe-am-thanh',
                'brand_slug' => 'sony',
                'description' => 'Tai nghe chống ồn số 1 thế giới với bộ đôi vi xử lý V1 và HD QN1 kết hợp 8 micro thu âm chuẩn xác. Chất âm Hi-Res Audio không dây chứng nhận LDAC, công nghệ đàm thoại AI lọc gió thông minh và thời lượng pin 30 giờ có sạc nhanh 3 phút nghe 3 giờ.',
                'variants' => [
                    [
                        'name' => 'Đen Huyền Bí (Black)',
                        'sku' => 'SONY-WHXM5-BLACK',
                        'price' => 7990000,
                        'stock' => 45,
                        'image_url' => SeedImage::for('tai-nghe-chong-on-khong-day-sony-wh-1000xm5', 'den-huyen-bi-black'),
                    ],
                    [
                        'name' => 'Bạc Bạch Kim (Silver)',
                        'sku' => 'SONY-WHXM5-SILVER',
                        'price' => 7990000,
                        'stock' => 35,
                        'image_url' => SeedImage::for('tai-nghe-chong-on-khong-day-sony-wh-1000xm5', 'bac-bach-kim-silver'),
                    ],
                ],
            ],
            [
                'name' => 'Tai Nghe True Wireless Sony WF-1000XM5',
                'category_slug' => 'tai-nghe-am-thanh',
                'brand_slug' => 'sony',
                'description' => 'Chống ồn vượt trội trong kích thước siêu nhỏ nhẹ hơn 25% thế hệ trước. Màng loa Dynamic Driver X tái tạo dải âm rộng và âm trầm sâu lắng, cảm biến truyền âm qua xương thu giọng nói rõ nét ngay cả ở nơi đông người ồn ào.',
                'variants' => [
                    [
                        'name' => 'Đen Nhám (Black)',
                        'sku' => 'SONY-WFXM5-BLACK',
                        'price' => 5990000,
                        'stock' => 40,
                        'image_url' => SeedImage::for('tai-nghe-true-wireless-sony-wf-1000xm5', 'den-nham-black'),
                    ],
                ],
            ],

            // ==================== MARSHALL ====================
            [
                'name' => 'Tai Nghe On-Ear Không Dây Marshall Major IV Bluetooth',
                'category_slug' => 'tai-nghe-am-thanh',
                'brand_slug' => 'marshall',
                'description' => 'Biểu tượng phong cách âm nhạc Rock n Roll với thời lượng pin kỷ lục hơn 80 giờ nghe nhạc liên tục. Hỗ trợ sạc không dây chuẩn Qi, núm điều hướng đa hướng Control Knob bằng đồng thau sang trọng và chất âm chữ ký Marshall bùng nổ.',
                'variants' => [
                    [
                        'name' => 'Đen Da Cổ Điển (Classic Black)',
                        'sku' => 'MARSHALL-M4-BLACK',
                        'price' => 3490000,
                        'stock' => 50,
                        'image_url' => SeedImage::for('tai-nghe-on-ear-khong-day-marshall-major-iv-bluetooth', 'den-da-co-dien-classic-black'),
                    ],
                    [
                        'name' => 'Nâu Vintage (Vintage Brown)',
                        'sku' => 'MARSHALL-M4-BROWN',
                        'price' => 3490000,
                        'stock' => 30,
                        'image_url' => SeedImage::for('tai-nghe-on-ear-khong-day-marshall-major-iv-bluetooth', 'nau-vintage-vintage-brown'),
                    ],
                ],
            ],
            [
                'name' => 'Loa Bluetooth Cắm Điện Marshall Stanmore III',
                'category_slug' => 'tai-nghe-am-thanh',
                'brand_slug' => 'marshall',
                'description' => 'Chiếc loa trang trí phòng khách và không gian cà phê sang trọng nhất. Không gian âm thanh đa hướng rộng hơn thế hệ trước, công nghệ Dynamic Loudness tự động cân bằng âm sắc, kết nối Bluetooth 5.2 và cổng RCA / 3.5mm chất lượng cao.',
                'variants' => [
                    [
                        'name' => 'Đen Viền Vàng Đồng (Black Brass)',
                        'sku' => 'STANMORE3-BLACK',
                        'price' => 9490000,
                        'stock' => 20,
                        'image_url' => SeedImage::for('loa-bluetooth-cam-dien-marshall-stanmore-iii', 'den-vien-vang-dong-black-brass'),
                    ],
                    [
                        'name' => 'Kem Cổ Điển (Cream White)',
                        'sku' => 'STANMORE3-CREAM',
                        'price' => 9490000,
                        'stock' => 15,
                        'image_url' => SeedImage::for('loa-bluetooth-cam-dien-marshall-stanmore-iii', 'kem-co-dien-cream-white'),
                    ],
                ],
            ],

            // ==================== BOSE & JBL ====================
            [
                'name' => 'Tai Nghe Chống Ồn Bose QuietComfort Ultra Headphones',
                'category_slug' => 'tai-nghe-am-thanh',
                'brand_slug' => 'bose',
                'description' => 'Trải nghiệm chống ồn đẳng cấp nhất của thương hiệu Bose danh tiếng từ Mỹ. Công nghệ âm thanh không gian Bose Immersive Audio biến mọi bài hát thành sân khấu sống động, đệm tai bọc da protein siêu êm đeo cả ngày không cấn mỏi tai.',
                'variants' => [
                    [
                        'name' => 'Đen Nhám (Black)',
                        'sku' => 'BOSE-QCU-BLACK',
                        'price' => 9990000,
                        'stock' => 25,
                        'image_url' => SeedImage::for('tai-nghe-chong-on-bose-quietcomfort-ultra-headphones', 'den-nham-black'),
                    ],
                    [
                        'name' => 'Trắng Khói (White Smoke)',
                        'sku' => 'BOSE-QCU-WHITE',
                        'price' => 9990000,
                        'stock' => 20,
                        'image_url' => SeedImage::for('tai-nghe-chong-on-bose-quietcomfort-ultra-headphones', 'trang-khoi-white-smoke'),
                    ],
                ],
            ],
            [
                'name' => 'Loa Di Động Chống Nước JBL Charge 5',
                'category_slug' => 'tai-nghe-am-thanh',
                'brand_slug' => 'jbl',
                'description' => 'Âm thanh bùng nổ JBL Original Pro Sound với củ loa trầm riêng biệt và màng rung thụ động kép. Kháng nước và bụi chuẩn quân sự IP67, pin khủng 20 giờ có tính năng sạc dự phòng cho điện thoại qua cổng USB tiện lợi.',
                'variants' => [
                    [
                        'name' => 'Đen Thể Thao (Black)',
                        'sku' => 'JBL-CHARGE5-BLACK',
                        'price' => 3690000,
                        'stock' => 40,
                        'image_url' => SeedImage::for('loa-di-dong-chong-nuoc-jbl-charge-5', 'den-the-thao-black'),
                    ],
                ],
            ],
        ];

        $this->seedProducts($catalog);
    }
}
