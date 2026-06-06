<?php

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
        $brands = [
            ['name' => 'Logitech', 'slug' => 'logitech'],
            ['name' => 'Apple', 'slug' => 'apple'],
            ['name' => 'Dell', 'slug' => 'dell'],
            ['name' => 'Samsung', 'slug' => 'samsung'],
            ['name' => 'Razer', 'slug' => 'razer'],
            ['name' => 'Asus', 'slug' => 'asus'],
        ];

        foreach ($brands as $brand) {
            Brand::create($brand);
        }
    }
}