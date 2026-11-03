<?php

declare(strict_types=1);

namespace Database\Seeders;

use Database\Seeders\Concerns\SeedsProductCatalog;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class LaptopSeeder extends Seeder
{
    use SeedsProductCatalog;

    public function run(): void
    {
        $catalog = [
            // ==================== APPLE MACBOOK ====================
            [
                'name' => 'MacBook Pro 16 inch Chip Apple M3 Max',
                'category_slug' => 'laptop',
                'brand_slug' => 'apple',
                'description' => 'Cỗ máy trạm di động mạnh nhất lịch sử Apple với vi xử lý M3 Max 16 nhân CPU, 40 nhân GPU hỗ trợ phần cứng Ray Tracing. Màn hình Liquid Retina XDR 16.2 inch độ sáng tối đa 1600 nits đỉnh cao, pin 22 giờ liên tục và màu Đen Không Gian (Space Black) chống bám vân tay đỉnh cao.',
                'variants' => [
                    [
                        'name' => 'Đen Không Gian - 36GB RAM / 1TB SSD',
                        'sku' => 'MBP16-M3MAX-36-1TB-BLK',
                        'price' => 89990000,
                        'stock' => 15,
                        'image_url' => 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=800&auto=format&fit=crop&q=85',
                    ],
                    [
                        'name' => 'Bạc Silver - 48GB RAM / 1TB SSD',
                        'sku' => 'MBP16-M3MAX-48-1TB-SLV',
                        'price' => 102990000,
                        'stock' => 10,
                        'image_url' => 'https://images.unsplash.com/photo-1541807084-5c52b6b3adef?w=800&auto=format&fit=crop&q=85',
                    ],
                ],
            ],
            [
                'name' => 'MacBook Pro 14 inch Chip Apple M3 Pro',
                'category_slug' => 'laptop',
                'brand_slug' => 'apple',
                'description' => 'Sự cân bằng hoàn hảo giữa hiệu năng đồ họa chuyên nghiệp và tính cơ động. Màn hình Promotion 120Hz Mini-LED tuyệt mỹ, hệ thống tản nhiệt quạt êm ái, đầy đủ cổng kết nối HDMI, thẻ nhớ SDXC và MagSafe 3 tiện lợi.',
                'variants' => [
                    [
                        'name' => 'Đen Không Gian - 18GB RAM / 512GB SSD',
                        'sku' => 'MBP14-M3PRO-18-512-BLK',
                        'price' => 49990000,
                        'stock' => 25,
                        'image_url' => 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=800&auto=format&fit=crop&q=85',
                    ],
                    [
                        'name' => 'Bạc Silver - 18GB RAM / 1TB SSD',
                        'sku' => 'MBP14-M3PRO-18-1TB-SLV',
                        'price' => 54990000,
                        'stock' => 20,
                        'image_url' => 'https://images.unsplash.com/photo-1541807084-5c52b6b3adef?w=800&auto=format&fit=crop&q=85',
                    ],
                ],
            ],
            [
                'name' => 'MacBook Air 15 inch Chip Apple M3',
                'category_slug' => 'laptop',
                'brand_slug' => 'apple',
                'description' => 'Chiếc laptop 15 inch mỏng nhất thế giới với độ dày chỉ 11.5mm. Màn hình Liquid Retina rộng rãi, chip M3 mạnh hơn 60% so với M1, thiết kế không quạt hoạt động hoàn toàn yên tĩnh và thời lượng pin 18 giờ ấn tượng.',
                'variants' => [
                    [
                        'name' => 'Xanh Đen Midnight - 16GB RAM / 512GB SSD',
                        'sku' => 'MBA15-M3-16-512-MID',
                        'price' => 37990000,
                        'stock' => 30,
                        'image_url' => 'https://images.unsplash.com/photo-1611186871348-b1ce696e52c9?w=800&auto=format&fit=crop&q=85',
                    ],
                    [
                        'name' => 'Ánh Sao Starlight - 8GB RAM / 256GB SSD',
                        'sku' => 'MBA15-M3-8-256-STA',
                        'price' => 31990000,
                        'stock' => 35,
                        'image_url' => 'https://images.unsplash.com/photo-1541807084-5c52b6b3adef?w=800&auto=format&fit=crop&q=85',
                    ],
                ],
            ],
            [
                'name' => 'MacBook Air 13 inch Chip Apple M2',
                'category_slug' => 'laptop',
                'brand_slug' => 'apple',
                'description' => 'Thiết kế vuông vức thời thượng với trọng lượng chỉ 1.24kg. Camera FaceTime HD 1080p sắc nét, âm thanh 4 loa hỗ trợ Dolby Atmos sống động và cổng sạc MagSafe 3 an toàn chống giật rơi.',
                'variants' => [
                    [
                        'name' => 'Xanh Đen Midnight - 8GB RAM / 256GB SSD',
                        'sku' => 'MBA13-M2-8-256-MID',
                        'price' => 24490000,
                        'stock' => 45,
                        'image_url' => 'https://images.unsplash.com/photo-1611186871348-b1ce696e52c9?w=800&auto=format&fit=crop&q=85',
                    ],
                    [
                        'name' => 'Bạc Silver - 16GB RAM / 256GB SSD',
                        'sku' => 'MBA13-M2-16-256-SLV',
                        'price' => 27490000,
                        'stock' => 35,
                        'image_url' => 'https://images.unsplash.com/photo-1541807084-5c52b6b3adef?w=800&auto=format&fit=crop&q=85',
                    ],
                ],
            ],

            // ==================== DELL ====================
            [
                'name' => 'Laptop Dell XPS 15 9530 OLED',
                'category_slug' => 'laptop',
                'brand_slug' => 'dell',
                'description' => 'Tuyệt tác máy trạm mỏng nhẹ bằng nhôm CNC nguyên khối và chiếu nghỉ tay sợi carbon. Màn hình cảm ứng OLED 3.5K viền siêu mỏng InfinityEdge 4 cạnh, vi xử lý Intel Core i7-13700H kết hợp card đồ họa rời NVIDIA GeForce RTX 4060 8GB mạnh mẽ.',
                'variants' => [
                    [
                        'name' => 'Bạch Kim Platinum - 32GB RAM / 1TB SSD / RTX 4060',
                        'sku' => 'DELL-XPS15-32-1TB',
                        'price' => 59990000,
                        'stock' => 15,
                        'image_url' => 'https://images.unsplash.com/photo-1593642632823-8f785ba67e45?w=800&auto=format&fit=crop&q=85',
                    ],
                    [
                        'name' => 'Bạch Kim Platinum - 16GB RAM / 512GB SSD / RTX 4050',
                        'sku' => 'DELL-XPS15-16-512',
                        'price' => 49990000,
                        'stock' => 20,
                        'image_url' => 'https://images.unsplash.com/photo-1593642632823-8f785ba67e45?w=800&auto=format&fit=crop&q=85',
                    ],
                ],
            ],
            [
                'name' => 'Laptop Dell Inspiron 16 5630',
                'category_slug' => 'laptop',
                'brand_slug' => 'dell',
                'description' => 'Laptop văn phòng và học tập hàng đầu với màn hình 16 inch tỉ lệ vàng 16:10 sắc nét 2.5K. Vỏ nhôm thanh lịch, vi xử lý Intel Core i7-1360P 12 nhân 16 luồng, bàn phím gõ êm ái và cảm biến vân tay 1 chạm.',
                'variants' => [
                    [
                        'name' => 'Bạc Silver - 16GB RAM / 512GB SSD',
                        'sku' => 'DELL-INS16-16-512',
                        'price' => 21990000,
                        'stock' => 30,
                        'image_url' => 'https://images.unsplash.com/photo-1588872657578-7efd1f1555ed?w=800&auto=format&fit=crop&q=85',
                    ],
                ],
            ],

            // ==================== ASUS ====================
            [
                'name' => 'Laptop Gaming ASUS ROG Zephyrus G16 OLED',
                'category_slug' => 'laptop',
                'brand_slug' => 'asus',
                'description' => 'Laptop gaming cao cấp thiết kế sang trọng mỏng chỉ 1.49cm. Dải đèn Slash Lighting độc bản ở mặt A, màn hình ROG Nebula OLED 2.5K 240Hz 0.2ms chuẩn màu 100% DCI-P3, vi xử lý Intel Core Ultra 9 185H và card RTX 4070 8GB.',
                'variants' => [
                    [
                        'name' => 'Trắng Platinum White - 32GB RAM / 1TB SSD / RTX 4070',
                        'sku' => 'ROG-G16-32-1TB-WHT',
                        'price' => 64990000,
                        'stock' => 12,
                        'image_url' => 'https://images.unsplash.com/photo-1603302576837-37561b2e2302?w=800&auto=format&fit=crop&q=85',
                    ],
                    [
                        'name' => 'Xám Eclipse Gray - 16GB RAM / 1TB SSD / RTX 4060',
                        'sku' => 'ROG-G16-16-1TB-GRY',
                        'price' => 52990000,
                        'stock' => 18,
                        'image_url' => 'https://images.unsplash.com/photo-1603302576837-37561b2e2302?w=800&auto=format&fit=crop&q=85',
                    ],
                ],
            ],
            [
                'name' => 'Laptop ASUS Zenbook 14 OLED',
                'category_slug' => 'laptop',
                'brand_slug' => 'asus',
                'description' => 'Chuẩn mực laptop doanh nhân mỏng nhẹ chỉ 1.2kg. Màn hình Lumina OLED 3K 120Hz rực rỡ, chip Intel Core Ultra 7 tích hợp NPU xử lý trí tuệ nhân tạo AI offline, pin dung lượng lớn 75Wh dùng trọn vẹn cả ngày làm việc.',
                'variants' => [
                    [
                        'name' => 'Xanh Ponder Blue - 16GB RAM / 1TB SSD',
                        'sku' => 'ZENBOOK-14-16-1TB',
                        'price' => 28990000,
                        'stock' => 25,
                        'image_url' => 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?w=800&auto=format&fit=crop&q=85',
                    ],
                ],
            ],

            // ==================== LENOVO ====================
            [
                'name' => 'Laptop Lenovo ThinkPad X1 Carbon Gen 12',
                'category_slug' => 'laptop',
                'brand_slug' => 'lenovo',
                'description' => 'Biểu tượng laptop doanh nhân đẳng cấp toàn cầu. Trọng lượng siêu nhẹ chỉ 1.09kg nhờ sợi carbon gia cường, bàn phím gõ êm nhất thế giới tích hợp nút TrackPoint đỏ huyền thoại, chuẩn độ bền quân sự MIL-STD-810H và chip Intel Core Ultra 7 mạnh mẽ.',
                'variants' => [
                    [
                        'name' => 'Đen Deep Black - 32GB RAM / 1TB SSD OLED 2.8K',
                        'sku' => 'THINKPAD-X1-32-1TB',
                        'price' => 54990000,
                        'stock' => 15,
                        'image_url' => 'https://images.unsplash.com/photo-1544731612-de292439cc4f?w=800&auto=format&fit=crop&q=85',
                    ],
                ],
            ],
            [
                'name' => 'Laptop Gaming Lenovo Legion Pro 5i Gen 9',
                'category_slug' => 'laptop',
                'brand_slug' => 'lenovo',
                'description' => 'Quái thú gaming eSports với màn hình WQXGA 240Hz tốc độ cao chuẩn màu 100% sRGB. Hệ thống tản nhiệt Legion ColdFront 5.0 buồng hơi kép, chip Intel Core i9-14900HX 24 nhân 32 luồng và card đồ họa NVIDIA RTX 4070 140W cực khủng.',
                'variants' => [
                    [
                        'name' => 'Xám Onyx Grey - 32GB RAM / 1TB SSD / RTX 4070',
                        'sku' => 'LEGION-PRO5-32-1TB',
                        'price' => 48990000,
                        'stock' => 20,
                        'image_url' => 'https://images.unsplash.com/photo-1525547719571-a2d4ac8945e2?w=800&auto=format&fit=crop&q=85',
                    ],
                ],
            ],

            // ==================== MSI ====================
            [
                'name' => 'Laptop Gaming MSI Raider GE78 HX',
                'category_slug' => 'laptop',
                'brand_slug' => 'msi',
                'description' => 'Cỗ máy chiến game tối thượng biểu tượng Rồng Đỏ MSI. Dải đèn Mystic Light Matrix RGB rực rỡ mặt trước, màn hình QHD+ 240Hz 17 inch siêu lớn, cấu hình cực hạn Intel Core i9-14900HX và card đồ họa NVIDIA GeForce RTX 4080 12GB.',
                'variants' => [
                    [
                        'name' => 'Đen Core Black - 32GB RAM / 2TB SSD / RTX 4080',
                        'sku' => 'MSI-RAIDER-32-2TB',
                        'price' => 79990000,
                        'stock' => 10,
                        'image_url' => 'https://images.unsplash.com/photo-1587614382346-4ec70e388b28?w=800&auto=format&fit=crop&q=85',
                    ],
                ],
            ],
            [
                'name' => 'Laptop MSI Modern 14 C13M',
                'category_slug' => 'laptop',
                'brand_slug' => 'msi',
                'description' => 'Dòng laptop văn phòng trẻ trung, thanh lịch với vỏ nhôm mỏng 16.9mm và trọng lượng nhẹ 1.4kg. Bản lề 180 độ mở phẳng tiện chia sẻ màn hình, vi xử lý Intel Core i5-1335U thế hệ mới tiết kiệm điện năng và bàn phím đèn LED trắng làm việc ban đêm.',
                'variants' => [
                    [
                        'name' => 'Đen Cổ Điển Classic Black - 16GB RAM / 512GB SSD',
                        'sku' => 'MSI-MODERN14-16-512',
                        'price' => 13490000,
                        'stock' => 40,
                        'image_url' => 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?w=800&auto=format&fit=crop&q=85',
                    ],
                    [
                        'name' => 'Bạc Urban Silver - 16GB RAM / 512GB SSD',
                        'sku' => 'MSI-MODERN14-16-512-SLV',
                        'price' => 13490000,
                        'stock' => 35,
                        'image_url' => 'https://images.unsplash.com/photo-1541807084-5c52b6b3adef?w=800&auto=format&fit=crop&q=85',
                    ],
                ],
            ],
        ];

        $this->seedProducts($catalog);
    }
}
