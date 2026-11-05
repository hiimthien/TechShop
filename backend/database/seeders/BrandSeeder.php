<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Xóa các thương hiệu không có sản phẩm nếu còn tồn tại
        Brand::whereIn('slug', ['vivo', 'realme', 'honor', 'hp', 'acer', 'huawei', 'lg'])->delete();

        $brands = [
            // Smartphone & Tablet Giants
            [
                'name' => 'Apple',
                'slug' => 'apple',
                'logo_url' => 'https://upload.wikimedia.org/wikipedia/commons/f/fa/Apple_logo_black.svg',
                'description' => 'Hệ sinh thái công nghệ hàng đầu thế giới: iPhone, iPad, Mac, Apple Watch, AirPods.',
            ],
            [
                'name' => 'Samsung',
                'slug' => 'samsung',
                'logo_url' => 'https://upload.wikimedia.org/wikipedia/commons/2/24/Samsung_Logo.svg',
                'description' => 'Tập đoàn công nghệ dẫn đầu về điện thoại Galaxy AI, màn hình OLED và smartwatch.',
            ],
            [
                'name' => 'Xiaomi',
                'slug' => 'xiaomi',
                'logo_url' => 'https://upload.wikimedia.org/wikipedia/commons/a/ae/Xiaomi_logo_%282021-%29.svg',
                'description' => 'Điện thoại thông minh chụp ảnh Leica cao cấp và thiết bị thông minh giá cực tốt.',
            ],
            [
                'name' => 'OPPO',
                'slug' => 'oppo',
                'logo_url' => 'https://upload.wikimedia.org/wikipedia/commons/b/b8/OPPO_Logo.svg',
                'description' => 'Chuyên gia chân dung AI, smartphone màn hình gập cao cấp và sạc siêu nhanh SuperVOOC.',
            ],

            // Laptop & PC Giants
            [
                'name' => 'ASUS',
                'slug' => 'asus',
                'logo_url' => 'https://upload.wikimedia.org/wikipedia/commons/2/2e/ASUS_Logo.svg',
                'description' => 'Laptop gaming ROG đỉnh cao, dòng Zenbook cao cấp, bo mạch chủ và màn hình chuyên nghiệp.',
            ],
            [
                'name' => 'Dell',
                'slug' => 'dell',
                'logo_url' => 'https://upload.wikimedia.org/wikipedia/commons/4/48/Dell_Logo.svg',
                'description' => 'Thương hiệu laptop bền bỉ hàng đầu thế giới với các dòng XPS, Inspiron, Alienware.',
            ],
            [
                'name' => 'Lenovo',
                'slug' => 'lenovo',
                'logo_url' => 'https://upload.wikimedia.org/wikipedia/commons/b/b8/Lenovo_logo_2015.svg',
                'description' => 'Hãng laptop số 1 thế giới với bàn phím ThinkPad huyền thoại và dòng máy chơi game Legion.',
            ],
            [
                'name' => 'MSI',
                'slug' => 'msi',
                'logo_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/6/69/MSI_Logo_2019.svg/1200px-MSI_Logo_2019.svg.png',
                'description' => 'Biểu tượng laptop gaming Rồng Đỏ với hiệu năng tản nhiệt khủng và card đồ họa tối thượng.',
            ],

            // Smartwatch & Wearables
            [
                'name' => 'Garmin',
                'slug' => 'garmin',
                'logo_url' => 'https://upload.wikimedia.org/wikipedia/commons/1/1d/Garmin_logo_2020.svg',
                'description' => 'Thương hiệu đồng hồ thể thao định vị GPS chuyên nghiệp dành cho vận động viên và dã ngoại.',
            ],

            // Audio & Speakers
            [
                'name' => 'Sony',
                'slug' => 'sony',
                'logo_url' => 'https://upload.wikimedia.org/wikipedia/commons/c/ca/Sony_logo.svg',
                'description' => 'Đỉnh cao công nghệ âm thanh với tai nghe chống ồn số 1 thế giới và máy chơi game PlayStation.',
            ],
            [
                'name' => 'Marshall',
                'slug' => 'marshall',
                'logo_url' => 'https://upload.wikimedia.org/wikipedia/commons/e/e4/Marshall_Logo.svg',
                'description' => 'Biểu tượng âm thanh Rock n Roll cổ điển đến từ Vương Quốc Anh với chất âm mộc mạc mạnh mẽ.',
            ],
            [
                'name' => 'JBL',
                'slug' => 'jbl',
                'logo_url' => 'https://upload.wikimedia.org/wikipedia/commons/1/1e/JBL_logo.svg',
                'description' => 'Âm thanh JBL Original Pro Sound bùng nổ cho các bữa tiệc với dòng loa Flip, Charge, PartyBox.',
            ],
            [
                'name' => 'Bose',
                'slug' => 'bose',
                'logo_url' => 'https://upload.wikimedia.org/wikipedia/commons/8/85/Bose_logo.svg',
                'description' => 'Thương hiệu âm thanh cao cấp của Mỹ, tiên phong về công nghệ chống ồn QuietComfort đỉnh cao.',
            ],

            // Gaming Gear & Displays
            [
                'name' => 'Logitech',
                'slug' => 'logitech',
                'logo_url' => 'https://upload.wikimedia.org/wikipedia/commons/0/09/Logitech_logo.svg',
                'description' => 'Nhà sản xuất chuột, bàn phím gaming và thiết bị văn phòng công thái học số 1 thế giới.',
            ],
            [
                'name' => 'Razer',
                'slug' => 'razer',
                'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/4/40/Razer_snake_logo.svg',
                'description' => 'Thương hiệu Gaming Gear Tam Xà huyền thoại dành riêng cho các game thủ eSports chuyên nghiệp.',
            ],
            [
                'name' => 'Keychron',
                'slug' => 'keychron',
                'logo_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/c/cd/Keychron_Logo.svg/1200px-Keychron_Logo.svg.png',
                'description' => 'Hãng bàn phím cơ không dây tùy biến custom cao cấp tương thích hoàn hảo cả Mac và Windows.',
            ],
        ];

        foreach ($brands as $brand) {
            Brand::updateOrCreate(['slug' => $brand['slug']], $brand);
        }
    }
}
