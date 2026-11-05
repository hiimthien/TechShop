<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AdminDummySeeder extends Seeder
{
    /**
     * Proxy seeder để duy trì tính tương thích ngược khi gọi class AdminDummySeeder.
     */
    public function run(): void
    {
        $this->call([
            CouponSeeder::class,
            OrderDemoSeeder::class,
            ProductInteractionSeeder::class,
            WarrantyClaimSeeder::class,
        ]);
    }
}
