<?php

declare(strict_types=1);

namespace Database\Seeders;

use Database\Seeders\Concerns\SeedsProductCatalog;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SmartphoneSeeder extends Seeder
{
    use SeedsProductCatalog;

    public function run(): void
    {
        $catalog = [
            // ==================== APPLE ====================
            [
                'name' => 'iPhone 16 Pro Max',
                'category_slug' => 'dien-thoai',
                'brand_slug' => 'apple',
                'description' => 'iPhone 16 Pro Max sở hữu màn hình Super Retina XDR 6.9 inch lớn nhất từng có, khung viền Titan Cấp 5 siêu bền nhẹ. Trang bị chip Apple A18 Pro mạnh mẽ vượt trội với Ray Tracing phần cứng, cụm 3 camera 48MP Fusion cùng ống kính tiềm vọng Zoom quang học 5x, nút Camera Control cảm ứng lực thế hệ mới và thời lượng pin kỷ lục lên đến 33 giờ xem video.',
                'variants' => [
                    [
                        'name' => 'Titan Sa Mạc - 256GB',
                        'sku' => 'IP16PM-256-DESERT',
                        'price' => 34990000,
                        'stock' => 50,
                        'image_url' => 'https://images.unsplash.com/photo-1695048133142-1a20484d2569?w=800&auto=format&fit=crop&q=85',
                    ],
                    [
                        'name' => 'Titan Tự Nhiên - 512GB',
                        'sku' => 'IP16PM-512-NATURAL',
                        'price' => 40990000,
                        'stock' => 35,
                        'image_url' => 'https://images.unsplash.com/photo-1592750475338-74b7b21085ab?w=800&auto=format&fit=crop&q=85',
                    ],
                    [
                        'name' => 'Titan Đen - 1TB',
                        'sku' => 'IP16PM-1TB-BLACK',
                        'price' => 46990000,
                        'stock' => 20,
                        'image_url' => 'https://images.unsplash.com/photo-1510557880182-3d4d3cba35a5?w=800&auto=format&fit=crop&q=85',
                    ],
                    [
                        'name' => 'Titan Trắng - 256GB',
                        'sku' => 'IP16PM-256-WHITE',
                        'price' => 34990000,
                        'stock' => 40,
                        'image_url' => 'https://images.unsplash.com/photo-1574944985070-8f3ebc6b79d2?w=800&auto=format&fit=crop&q=85',
                    ],
                ],
            ],
            [
                'name' => 'iPhone 16 Pro',
                'category_slug' => 'dien-thoai',
                'brand_slug' => 'apple',
                'description' => 'iPhone 16 Pro mang đến hiệu năng chuyên nghiệp trong kích thước gọn gàng 6.3 inch viền siêu mỏng. Chip A18 Pro 3nm đỉnh cao, camera Tetraprism zoom 5x, quay video 4K Dolby Vision 120fps chuẩn Hollywood và nút tác vụ Action Button linh hoạt.',
                'variants' => [
                    [
                        'name' => 'Titan Sa Mạc - 128GB',
                        'sku' => 'IP16P-128-DESERT',
                        'price' => 28990000,
                        'stock' => 45,
                        'image_url' => 'https://images.unsplash.com/photo-1695048133142-1a20484d2569?w=800&auto=format&fit=crop&q=85',
                    ],
                    [
                        'name' => 'Titan Tự Nhiên - 256GB',
                        'sku' => 'IP16P-256-NATURAL',
                        'price' => 31990000,
                        'stock' => 30,
                        'image_url' => 'https://images.unsplash.com/photo-1592750475338-74b7b21085ab?w=800&auto=format&fit=crop&q=85',
                    ],
                    [
                        'name' => 'Titan Đen - 512GB',
                        'sku' => 'IP16P-512-BLACK',
                        'price' => 37990000,
                        'stock' => 25,
                        'image_url' => 'https://images.unsplash.com/photo-1510557880182-3d4d3cba35a5?w=800&auto=format&fit=crop&q=85',
                    ],
                ],
            ],
            [
                'name' => 'iPhone 16',
                'category_slug' => 'dien-thoai',
                'brand_slug' => 'apple',
                'description' => 'iPhone 16 tỏa sáng với thiết kế kính pha màu rực rỡ, cụm camera dọc hỗ trợ quay Spatial Video cho kính Apple Vision Pro. Chip Apple A18 cực mạnh, màn hình Dynamic Island và nút Camera Control điều khiển chụp ảnh tức thì.',
                'variants' => [
                    [
                        'name' => 'Xanh Mòng Két - 128GB',
                        'sku' => 'IP16-128-TEAL',
                        'price' => 22490000,
                        'stock' => 60,
                        'image_url' => 'https://images.unsplash.com/photo-1605236453806-6ff36851218e?w=800&auto=format&fit=crop&q=85',
                    ],
                    [
                        'name' => 'Hồng Pastel - 128GB',
                        'sku' => 'IP16-128-PINK',
                        'price' => 22490000,
                        'stock' => 55,
                        'image_url' => 'https://images.unsplash.com/photo-1565849904461-04a58ad377e0?w=800&auto=format&fit=crop&q=85',
                    ],
                    [
                        'name' => 'Đen Midnight - 256GB',
                        'sku' => 'IP16-256-BLACK',
                        'price' => 25490000,
                        'stock' => 40,
                        'image_url' => 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=800&auto=format&fit=crop&q=85',
                    ],
                ],
            ],
            [
                'name' => 'iPhone 15 Pro Max',
                'category_slug' => 'dien-thoai',
                'brand_slug' => 'apple',
                'description' => 'Flagship khai sinh khung Titan siêu nhẹ của Apple, vi xử lý A17 Pro 3nm đầu tiên chơi game đồ họa AAA điều hướng Ray Tracing, camera 5x zoom quang học và cổng kết nối USB-C chuẩn 3.0 truyền tải siêu tốc.',
                'variants' => [
                    [
                        'name' => 'Titan Tự Nhiên - 256GB',
                        'sku' => 'IP15PM-256-NATURAL',
                        'price' => 29490000,
                        'stock' => 30,
                        'image_url' => 'https://images.unsplash.com/photo-1592750475338-74b7b21085ab?w=800&auto=format&fit=crop&q=85',
                    ],
                    [
                        'name' => 'Titan Xanh - 512GB',
                        'sku' => 'IP15PM-512-BLUE',
                        'price' => 35490000,
                        'stock' => 20,
                        'image_url' => 'https://images.unsplash.com/photo-1565849904461-04a58ad377e0?w=800&auto=format&fit=crop&q=85',
                    ],
                ],
            ],
            [
                'name' => 'iPhone 13',
                'category_slug' => 'dien-thoai',
                'brand_slug' => 'apple',
                'description' => 'Chiếc iPhone quốc dân bán chạy nhất thế giới với thiết kế camera chéo đặc trưng, màn hình Super Retina XDR OLED, thời lượng pin cả ngày dài và chip Apple A15 Bionic bền bỉ nhiều năm.',
                'variants' => [
                    [
                        'name' => 'Đen Midnight - 128GB',
                        'sku' => 'IP13-128-BLACK',
                        'price' => 13490000,
                        'stock' => 70,
                        'image_url' => 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=800&auto=format&fit=crop&q=85',
                    ],
                    [
                        'name' => 'Trắng Starlight - 128GB',
                        'sku' => 'IP13-128-WHITE',
                        'price' => 13490000,
                        'stock' => 65,
                        'image_url' => 'https://images.unsplash.com/photo-1574944985070-8f3ebc6b79d2?w=800&auto=format&fit=crop&q=85',
                    ],
                ],
            ],

            // ==================== SAMSUNG ====================
            [
                'name' => 'Samsung Galaxy S24 Ultra 5G',
                'category_slug' => 'dien-thoai',
                'brand_slug' => 'samsung',
                'description' => 'Đỉnh cao công nghệ di động với quyền năng Galaxy AI: khoanh vùng tìm kiếm Circle to Search, dịch cuộc gọi trực tiếp hai chiều, tóm tắt ghi chú thông minh. Khung viền Titan phẳng sang trọng, màn hình chống lóa Corning Gorilla Armor đỉnh cao, bút S-Pen quyền năng và camera 200MP zoom quang học 100x Space Zoom.',
                'variants' => [
                    [
                        'name' => 'Xám Titan - 256GB',
                        'sku' => 'SS-S24U-256-GRAY',
                        'price' => 29990000,
                        'stock' => 45,
                        'image_url' => 'https://images.unsplash.com/photo-1610945265064-0e34e5519bbf?w=800&auto=format&fit=crop&q=85',
                    ],
                    [
                        'name' => 'Đen Titan - 512GB',
                        'sku' => 'SS-S24U-512-BLACK',
                        'price' => 34490000,
                        'stock' => 30,
                        'image_url' => 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=800&auto=format&fit=crop&q=85',
                    ],
                    [
                        'name' => 'Tím Titan - 256GB',
                        'sku' => 'SS-S24U-256-VIOLET',
                        'price' => 29990000,
                        'stock' => 35,
                        'image_url' => 'https://images.unsplash.com/photo-1598327105666-5b89351aff97?w=800&auto=format&fit=crop&q=85',
                    ],
                ],
            ],
            [
                'name' => 'Samsung Galaxy Z Fold 6 5G',
                'category_slug' => 'dien-thoai',
                'brand_slug' => 'samsung',
                'description' => 'Tuyệt tác màn hình gập mỏng nhẹ và vuông vức nhất phân khúc. Khung nhôm Armor Aluminum gia cường, kính siêu mỏng UTG, tính năng Galaxy AI phác họa thông minh Sketch to Image và dịch trực tiếp trên màn hình phụ.',
                'variants' => [
                    [
                        'name' => 'Xám Metal - 256GB',
                        'sku' => 'SS-ZFOLD6-256-GRAY',
                        'price' => 41990000,
                        'stock' => 25,
                        'image_url' => 'https://images.unsplash.com/photo-1556656793-08538906a9f8?w=800&auto=format&fit=crop&q=85',
                    ],
                    [
                        'name' => 'Xanh Navy - 512GB',
                        'sku' => 'SS-ZFOLD6-512-NAVY',
                        'price' => 45990000,
                        'stock' => 15,
                        'image_url' => 'https://images.unsplash.com/photo-1567581935884-3349723552ca?w=800&auto=format&fit=crop&q=85',
                    ],
                ],
            ],
            [
                'name' => 'Samsung Galaxy Z Flip 6 5G',
                'category_slug' => 'dien-thoai',
                'brand_slug' => 'samsung',
                'description' => 'Biểu tượng phong cách công nghệ gập vỏ sò nhỏ gọn. Màn hình phụ Flex Window đa năng, camera chính nâng cấp 50MP ProVisual Engine, thời lượng pin 4000mAh bền bỉ kèm tản nhiệt buồng hơi Vapor Chamber lần đầu tiên xuất hiện.',
                'variants' => [
                    [
                        'name' => 'Xanh Maya - 256GB',
                        'sku' => 'SS-ZFLIP6-256-BLUE',
                        'price' => 26990000,
                        'stock' => 30,
                        'image_url' => 'https://images.unsplash.com/photo-1556656793-08538906a9f8?w=800&auto=format&fit=crop&q=85',
                    ],
                    [
                        'name' => 'Vàng Mì Ý - 256GB',
                        'sku' => 'SS-ZFLIP6-256-YELLOW',
                        'price' => 26990000,
                        'stock' => 25,
                        'image_url' => 'https://images.unsplash.com/photo-1575695342320-d2d2d2f9b73f?w=800&auto=format&fit=crop&q=85',
                    ],
                ],
            ],
            [
                'name' => 'Samsung Galaxy A55 5G',
                'category_slug' => 'dien-thoai',
                'brand_slug' => 'samsung',
                'description' => 'Chiếc smartphone tầm trung dẫn đầu phân khúc với khung viền kim loại cao cấp, mặt lưng kính cường lực Gorilla Glass Victus+, chuẩn kháng nước bụi IP67 và màn hình Super AMOLED 120Hz mượt mà.',
                'variants' => [
                    [
                        'name' => 'Xanh Iceblue - 128GB',
                        'sku' => 'SS-A55-128-ICE',
                        'price' => 9690000,
                        'stock' => 50,
                        'image_url' => 'https://images.unsplash.com/photo-1565849904461-04a58ad377e0?w=800&auto=format&fit=crop&q=85',
                    ],
                    [
                        'name' => 'Tím Lilac - 256GB',
                        'sku' => 'SS-A55-256-LILAC',
                        'price' => 10490000,
                        'stock' => 45,
                        'image_url' => 'https://images.unsplash.com/photo-1598327105666-5b89351aff97?w=800&auto=format&fit=crop&q=85',
                    ],
                ],
            ],

            // ==================== XIAOMI ====================
            [
                'name' => 'Xiaomi 14 Ultra 5G Leica',
                'category_slug' => 'dien-thoai',
                'brand_slug' => 'xiaomi',
                'description' => 'Tuyệt tác nhiếp ảnh di động hợp tác huyền thoại Leica. Hệ thống 4 camera 50MP cảm biến 1 inch LYT-900 khẩu độ vô cấp f/1.63 - f/4.0, quay video 8K chuyên nghiệp, chip Snapdragon 8 Gen 3 và sạc siêu tốc 90W HyperCharge.',
                'variants' => [
                    [
                        'name' => 'Đen Da Thuần Chay - 512GB',
                        'sku' => 'MI-14U-512-BLACK',
                        'price' => 29990000,
                        'stock' => 25,
                        'image_url' => 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=800&auto=format&fit=crop&q=85',
                    ],
                    [
                        'name' => 'Trắng Gốm - 512GB',
                        'sku' => 'MI-14U-512-WHITE',
                        'price' => 29990000,
                        'stock' => 20,
                        'image_url' => 'https://images.unsplash.com/photo-1574944985070-8f3ebc6b79d2?w=800&auto=format&fit=crop&q=85',
                    ],
                ],
            ],
            [
                'name' => 'Xiaomi 14 5G',
                'category_slug' => 'dien-thoai',
                'brand_slug' => 'xiaomi',
                'description' => 'Flagship màn hình phẳng kích thước nhỏ gọn 6.36 inch viền siêu mỏng chỉ 1.61mm. Ống kính quang học Leica Summilux f/1.6, chip Snapdragon 8 Gen 3 cực đỉnh và sạc đầy 100% chỉ trong 31 phút.',
                'variants' => [
                    [
                        'name' => 'Xanh Ngọc Bích - 256GB',
                        'sku' => 'MI-14-256-GREEN',
                        'price' => 19990000,
                        'stock' => 40,
                        'image_url' => 'https://images.unsplash.com/photo-1605236453806-6ff36851218e?w=800&auto=format&fit=crop&q=85',
                    ],
                    [
                        'name' => 'Đen Nhám - 256GB',
                        'sku' => 'MI-14-256-BLACK',
                        'price' => 19990000,
                        'stock' => 35,
                        'image_url' => 'https://images.unsplash.com/photo-1510557880182-3d4d3cba35a5?w=800&auto=format&fit=crop&q=85',
                    ],
                ],
            ],
            [
                'name' => 'Xiaomi Redmi Note 13 Pro+ 5G',
                'category_slug' => 'dien-thoai',
                'brand_slug' => 'xiaomi',
                'description' => 'Đột phá phân khúc với camera 200MP siêu nét OIS, màn hình AMOLED cong 1.5K 120Hz rực rỡ, chuẩn kháng nước kháng bụi chuẩn flagship IP68 và công nghệ sạc thần tốc 120W nạp đầy pin trong 19 phút.',
                'variants' => [
                    [
                        'name' => 'Tím Cực Quang - 256GB',
                        'sku' => 'RN13P-256-PURPLE',
                        'price' => 9990000,
                        'stock' => 55,
                        'image_url' => 'https://images.unsplash.com/photo-1598327105666-5b89351aff97?w=800&auto=format&fit=crop&q=85',
                    ],
                    [
                        'name' => 'Đen Huyền Bí - 256GB',
                        'sku' => 'RN13P-256-BLACK',
                        'price' => 9990000,
                        'stock' => 50,
                        'image_url' => 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=800&auto=format&fit=crop&q=85',
                    ],
                ],
            ],

            // ==================== OPPO ====================
            [
                'name' => 'OPPO Find N3 Flip',
                'category_slug' => 'dien-thoai',
                'brand_slug' => 'oppo',
                'description' => 'Điện thoại gập đầu tiên trang bị bộ 3 camera chuyên nghiệp Hasselblad. Bản lề uốn tàng hình không nếp gấp, màn hình ngoài hiển thị dọc tương thích trực tiếp các ứng dụng Zalo, TikTok, YouTube tiện lợi.',
                'variants' => [
                    [
                        'name' => 'Vàng Thạch Anh - 256GB',
                        'sku' => 'OPPO-N3FLIP-GOLD',
                        'price' => 22990000,
                        'stock' => 25,
                        'image_url' => 'https://images.unsplash.com/photo-1575695342320-d2d2d2f9b73f?w=800&auto=format&fit=crop&q=85',
                    ],
                    [
                        'name' => 'Đen Hổ Phách - 256GB',
                        'sku' => 'OPPO-N3FLIP-BLACK',
                        'price' => 22990000,
                        'stock' => 20,
                        'image_url' => 'https://images.unsplash.com/photo-1510557880182-3d4d3cba35a5?w=800&auto=format&fit=crop&q=85',
                    ],
                ],
            ],
            [
                'name' => 'OPPO Reno 12 Pro 5G',
                'category_slug' => 'dien-thoai',
                'brand_slug' => 'oppo',
                'description' => 'Chuyên gia chân dung AI với thiết kế dòng chảy bạc tương lai. Tính năng AI xóa vật thể 2.0, tách nền thông minh, khung viền kháng lực bền bỉ và sạc nhanh 80W SuperVOOC bảo vệ pin tối đa.',
                'variants' => [
                    [
                        'name' => 'Bạc Vũ Trụ - 512GB',
                        'sku' => 'OPPO-R12P-SILVER',
                        'price' => 15990000,
                        'stock' => 40,
                        'image_url' => 'https://images.unsplash.com/photo-1598327105666-5b89351aff97?w=800&auto=format&fit=crop&q=85',
                    ],
                    [
                        'name' => 'Nâu Tinh Tú - 512GB',
                        'sku' => 'OPPO-R12P-BROWN',
                        'price' => 15990000,
                        'stock' => 35,
                        'image_url' => 'https://images.unsplash.com/photo-1567581935884-3349723552ca?w=800&auto=format&fit=crop&q=85',
                    ],
                ],
            ],
        ];

        $this->seedProducts($catalog);
    }
}
