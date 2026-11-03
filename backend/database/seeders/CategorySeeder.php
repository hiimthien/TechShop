<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Điện thoại',
                'slug' => 'dien-thoai',
                'description' => 'Điện thoại thông minh chính hãng Apple, Samsung, Xiaomi, OPPO giá tốt nhất.',
            ],
            [
                'name' => 'Đồng hồ thông minh',
                'slug' => 'dong-ho-thong-minh',
                'description' => 'Smartwatch theo dõi sức khỏe và thể thao đỉnh cao: Apple Watch, Galaxy Watch, Garmin.',
            ],
            [
                'name' => 'Tai nghe & Âm thanh',
                'slug' => 'tai-nghe-am-thanh',
                'description' => 'Tai nghe không dây Bluetooth, tai nghe chụp tai chống ồn chủ động Sony, Marshall, AirPods.',
            ],
            [
                'name' => 'Laptop',
                'slug' => 'laptop',
                'description' => 'Laptop gaming đồ họa, laptop mỏng nhẹ doanh nhân MacBook, Asus ROG, Dell XPS.',
            ],
            [
                'name' => 'Màn hình',
                'slug' => 'man-hinh',
                'description' => 'Màn hình máy tính gaming, đồ họa sắc nét 2K, 4K, 144Hz, 165Hz.',
            ],
            [
                'name' => 'Bàn phím cơ',
                'slug' => 'ban-phim',
                'description' => 'Bàn phím cơ gaming không dây, switch cơ học cao cấp gõ êm mượt.',
            ],
            [
                'name' => 'Chuột máy tính',
                'slug' => 'chuot',
                'description' => 'Chuột gaming độ nhạy cao, chuột công thái học văn phòng Bluetooth.',
            ],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(['slug' => $category['slug']], $category);
        }
    }
}
