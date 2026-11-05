<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CategorySeeder::class,
            BrandSeeder::class,
            UserSeeder::class,
            ProductSeeder::class,
            CouponSeeder::class,
            OrderDemoSeeder::class,
            ProductInteractionSeeder::class,
            WarrantyClaimSeeder::class,
        ]);
    }
}
