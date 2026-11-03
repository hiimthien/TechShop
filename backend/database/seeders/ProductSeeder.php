<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\CartItem;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeOption;
use App\Models\ProductImage;
use App\Models\ProductQuestion;
use App\Models\ProductReview;
use App\Models\ProductVariant;
use App\Models\ProductVariantOption;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ProductSeeder extends Seeder
{
    /**
     * Dọn dẹp catalog cũ và nạp bộ sản phẩm công nghệ chuẩn mực, ảnh sắc nét chân thực.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        // 1. Dọn dẹp các bảng phụ thuộc sản phẩm
        DB::table('product_images')->truncate();
        DB::table('product_variant_options')->truncate();
        DB::table('product_attribute_options')->truncate();
        DB::table('product_attributes')->truncate();
        DB::table('cart_items')->truncate();
        DB::table('order_items')->truncate();
        DB::table('product_reviews')->truncate();
        DB::table('product_questions')->truncate();
        DB::table('product_variants')->truncate();
        DB::table('products')->truncate();

        Schema::enableForeignKeyConstraints();

        // 2. Nạp catalog sản phẩm theo từng nhóm ngành hàng
        $this->call([
            SmartphoneSeeder::class, // Điện thoại thông minh (Apple, Samsung, Xiaomi, OPPO)
            LaptopSeeder::class,     // Laptop cao cấp & gaming (MacBook, Dell XPS, ASUS ROG, ThinkPad, MSI)
            AccessorySeeder::class,  // Chuột, Bàn phím cơ & Màn hình (Logitech, Keychron, Razer, Dell UltraSharp, ROG)
            AudioSeeder::class,      // Tai nghe & Âm thanh (AirPods, Sony WH-1000XM5, Marshall, Bose)
            SmartwatchSeeder::class, // Đồng hồ thông minh (Apple Watch Ultra 2, Galaxy Watch Ultra, Garmin Fenix 7)
        ]);
    }
}
