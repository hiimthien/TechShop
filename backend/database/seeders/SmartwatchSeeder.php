<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Support\SeedImage;
use Database\Seeders\Concerns\SeedsProductCatalog;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SmartwatchSeeder extends Seeder
{
    use SeedsProductCatalog;

    public function run(): void
    {
        $catalog = [
            // ==================== APPLE WATCH ====================
            [
                'name' => 'Đồng Hồ Apple Watch Ultra 2 GPS + Cellular 49mm',
                'category_slug' => 'dong-ho-thong-minh',
                'brand_slug' => 'apple',
                'description' => 'Đỉnh cao đồng hồ thể thao và thám hiểm chuyên nghiệp. Khung viền Titan 49mm chống ăn mòn, mặt kính sapphire chống trầy, màn hình độ sáng kỷ lục 3000 nits, GPS băng tần kép L1 và L5 siêu chuẩn xác, khả năng lặn sâu 40m và thời lượng pin lên đến 72 giờ ở chế độ tiết kiệm pin.',
                'variants' => [
                    [
                        'name' => 'Titan Tự Nhiên - Dây Alpine Xanh Rêu',
                        'sku' => 'AW-ULTRA2-ALPINE',
                        'price' => 21990000,
                        'stock' => 25,
                        'image_url' => SeedImage::for('dong-ho-apple-watch-ultra-2-gps-cellular-49mm', 'titan-tu-nhien-day-alpine-xanh-reu'),
                    ],
                    [
                        'name' => 'Titan Tự Nhiên - Dây Trail Cam Xám',
                        'sku' => 'AW-ULTRA2-TRAIL',
                        'price' => 21990000,
                        'stock' => 20,
                        'image_url' => SeedImage::for('dong-ho-apple-watch-ultra-2-gps-cellular-49mm', 'titan-tu-nhien-day-trail-cam-xam'),
                    ],
                ],
            ],
            [
                'name' => 'Đồng Hồ Apple Watch Series 9 GPS 45mm',
                'category_slug' => 'dong-ho-thong-minh',
                'brand_slug' => 'apple',
                'description' => 'Trang bị chip Apple S9 SiP mạnh mẽ cùng cử chỉ chạm hai lần Double Tap kỳ diệu không cần chạm vào màn hình. Màn hình Always-On độ sáng 2000 nits, theo dõi nồng độ oxy trong máu SpO2, đo điện tâm đồ ECG và phát hiện va chạm xe khẩn cấp.',
                'variants' => [
                    [
                        'name' => 'Viền Nhôm Đen Midnight - Dây Thể Thao M/L',
                        'sku' => 'AW-S9-45-MID',
                        'price' => 10990000,
                        'stock' => 35,
                        'image_url' => SeedImage::for('dong-ho-apple-watch-series-9-gps-45mm', 'vien-nhom-den-midnight-day-the-thao-ml'),
                    ],
                    [
                        'name' => 'Viền Nhôm Bạc Starlight - Dây Thể Thao S/M',
                        'sku' => 'AW-S9-45-STA',
                        'price' => 10990000,
                        'stock' => 30,
                        'image_url' => SeedImage::for('dong-ho-apple-watch-series-9-gps-45mm', 'vien-nhom-bac-starlight-day-the-thao-sm'),
                    ],
                ],
            ],

            // ==================== SAMSUNG ====================
            [
                'name' => 'Đồng Hồ Samsung Galaxy Watch Ultra 47mm LTE',
                'category_slug' => 'dong-ho-thong-minh',
                'brand_slug' => 'samsung',
                'description' => 'Chiếc đồng hồ thể thao mạo hiểm bền bỉ nhất của Samsung với khung viền Titan đệm tròn, khả năng chịu áp suất nước 10 ATM và chuẩn độ bền quân đội MIL-STD-810H. Nút vật lý Quick Button tùy biến thao tác nhanh và tính năng còi báo động khẩn cấp 86 decibel.',
                'variants' => [
                    [
                        'name' => 'Xám Titan Dây Cam Thể Thao',
                        'sku' => 'GW-ULTRA-GRAY-ORG',
                        'price' => 16990000,
                        'stock' => 20,
                        'image_url' => SeedImage::for('dong-ho-samsung-galaxy-watch-ultra-47mm-lte', 'xam-titan-day-cam-the-thao'),
                    ],
                    [
                        'name' => 'Trắng Titan Dây Trắng',
                        'sku' => 'GW-ULTRA-WHITE',
                        'price' => 16990000,
                        'stock' => 15,
                        'image_url' => SeedImage::for('dong-ho-samsung-galaxy-watch-ultra-47mm-lte', 'trang-titan-day-trang'),
                    ],
                ],
            ],
            [
                'name' => 'Đồng Hồ Samsung Galaxy Watch 6 Classic LTE 43mm',
                'category_slug' => 'dong-ho-thong-minh',
                'brand_slug' => 'samsung',
                'description' => 'Huyền thoại viền xoay vật lý trứ danh tái xuất với viền bezel thanh mảnh hơn 15%. Mặt kính Sapphire sang trọng, tính năng phân tích thành phần cơ thể BIA 7 chỉ số và huấn luyện viên giấc ngủ cá nhân hóa chuyên sâu.',
                'variants' => [
                    [
                        'name' => 'Đen Classic Sang Trọng',
                        'sku' => 'GW6-CLA-43-BLACK',
                        'price' => 7490000,
                        'stock' => 30,
                        'image_url' => SeedImage::for('dong-ho-samsung-galaxy-watch-6-classic-lte-43mm', 'den-classic-sang-trong'),
                    ],
                ],
            ],

            // ==================== GARMIN ====================
            [
                'name' => 'Đồng Hồ Thể Thao Đỉnh Cao Garmin Fenix 7 Pro Sapphire Solar',
                'category_slug' => 'dong-ho-thong-minh',
                'brand_slug' => 'garmin',
                'description' => 'Trang bị thấu kính sạc năng lượng mặt trời Power Sapphire kéo dài thời lượng pin lên đến 37 ngày. Tích hợp đèn pin LED siêu sáng, bản đồ địa hình Topo đa lục địa chi tiết và các chỉ số huấn luyện chuyên sâu như điểm sức bền Endurance Score và điểm leo dốc Hill Score.',
                'variants' => [
                    [
                        'name' => 'Titan Carbon DLC Đen Xám',
                        'sku' => 'GARMIN-FENIX7P-SOLAR',
                        'price' => 22490000,
                        'stock' => 15,
                        'image_url' => SeedImage::for('dong-ho-the-thao-dinh-cao-garmin-fenix-7-pro-sapphire-solar', 'titan-carbon-dlc-den-xam'),
                    ],
                ],
            ],
            [
                'name' => 'Đồng Hồ Chạy Bộ Chuyên Nghiệp Garmin Forerunner 965 AMOLED',
                'category_slug' => 'dong-ho-thong-minh',
                'brand_slug' => 'garmin',
                'description' => 'Màn hình cảm ứng rực rỡ AMOLED 1.4 inch cùng viền bezel titan siêu nhẹ. Định vị đa băng tần SatIQ chính xác từng mét đường chạy, báo cáo buổi sáng Morning Report và đề xuất bài tập thích ứng hàng ngày theo thể trạng người dùng.',
                'variants' => [
                    [
                        'name' => 'Vàng Chanh Năng Động (Amp Yellow)',
                        'sku' => 'GARMIN-FR965-YELLOW',
                        'price' => 16490000,
                        'stock' => 25,
                        'image_url' => SeedImage::for('dong-ho-chay-bo-chuyen-nghiep-garmin-forerunner-965-amoled', 'vang-chanh-nang-dong-amp-yellow'),
                    ],
                    [
                        'name' => 'Đen Carbon (Carbon Black)',
                        'sku' => 'GARMIN-FR965-BLACK',
                        'price' => 16490000,
                        'stock' => 20,
                        'image_url' => SeedImage::for('dong-ho-chay-bo-chuyen-nghiep-garmin-forerunner-965-amoled', 'den-carbon-carbon-black'),
                    ],
                ],
            ],
        ];

        $this->seedProducts($catalog);
    }
}
