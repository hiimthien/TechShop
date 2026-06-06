<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Chuột', 'slug' => 'chuot'],
            ['name' => 'Bàn phím', 'slug' => 'ban-phim'],
            ['name' => 'Tai nghe', 'slug' => 'tai-nghe'],
            ['name' => 'Màn hình', 'slug' => 'man-hinh'],
            ['name' => 'Laptop', 'slug' => 'laptop'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
