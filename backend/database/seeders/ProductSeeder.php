<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = Category::all();
        $brands = Brand::all();

        if ($categories->isEmpty() || $brands->isEmpty()) {
            $this->command->warn('Vui lòng chạy CategorySeeder và BrandSeeder trước.');
            return;
        }

        $imagePool = [
            'chuot' => [
                'https://images.unsplash.com/photo-1527864550417-7fd91fc51a46?w=600&auto=format&fit=crop&q=80',
                'https://images.unsplash.com/photo-1615663245857-ac93bb7c39e7?w=600&auto=format&fit=crop&q=80',
                'https://images.unsplash.com/photo-1586953208448-b95a79798f07?w=600&auto=format&fit=crop&q=80',
                'https://images.unsplash.com/photo-1605773527852-c546a8584ea3?w=600&auto=format&fit=crop&q=80',
            ],
            'ban-phim' => [
                'https://images.unsplash.com/photo-1587829741301-dc798b83add3?w=600&auto=format&fit=crop&q=80',
                'https://images.unsplash.com/photo-1595225476474-87563907a212?w=600&auto=format&fit=crop&q=80',
                'https://images.unsplash.com/photo-1511467687858-23d96c32e4ae?w=600&auto=format&fit=crop&q=80',
                'https://images.unsplash.com/photo-1541140532154-b024d705b909?w=600&auto=format&fit=crop&q=80',
            ],
            'tai-nghe' => [
                'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=600&auto=format&fit=crop&q=80',
                'https://images.unsplash.com/photo-1484704849700-f032a568e944?w=600&auto=format&fit=crop&q=80',
                'https://images.unsplash.com/photo-1546435770-a3e426bf472b?w=600&auto=format&fit=crop&q=80',
                'https://images.unsplash.com/photo-1572536147248-ac59a8abfa4b?w=600&auto=format&fit=crop&q=80',
            ],
            'man-hinh' => [
                'https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?w=600&auto=format&fit=crop&q=80',
                'https://images.unsplash.com/photo-1585792180666-f75a7c518801?w=600&auto=format&fit=crop&q=80',
                'https://images.unsplash.com/photo-1593642702821-c8da6771f0c6?w=600&auto=format&fit=crop&q=80',
                'https://images.unsplash.com/photo-1547082299-de196ea013d6?w=600&auto=format&fit=crop&q=80',
            ],
            'laptop' => [
                'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=600&auto=format&fit=crop&q=80',
                'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?w=600&auto=format&fit=crop&q=80',
                'https://images.unsplash.com/photo-1525547719571-a2d4ac8945e2?w=600&auto=format&fit=crop&q=80',
                'https://images.unsplash.com/photo-1531297484001-80022131f5a1?w=600&auto=format&fit=crop&q=80',
            ],
        ];

        $productModels = [
            'chuot' => ['G Pro X Superlight', 'DeathAdder V3', 'Viper Mini', 'Basilisk V3', 'ROG Gladius', 'Magic Mouse 2', 'Master 3S', 'G502 Hero', 'Aerox 3', 'Rival 5'],
            'ban-phim' => ['BlackWidow V4', 'Huntsman Mini', 'G913 TKL', 'K8 Pro Wireless', 'ROG Azoth', 'K70 RGB Pro', 'Apex Pro TKL', 'MonsGeek M1', 'Keychron Q1 Pro', 'Magic Keyboard'],
            'tai-nghe' => ['WH-1000XM5', 'AirPods Max', 'G Pro X 2', 'BlackShark V2 Pro', 'ROG Delta S', 'QuietComfort 45', 'Momentum 4', 'Arctis Nova Pro', 'Quantum 800', 'Kraken V3 Pro'],
            'man-hinh' => ['UltraSharp 27 4K', 'Odyssey OLED G9', 'ROG Swift PG279QM', 'UltraGear 27GP850', 'Studio Display 5K', 'TUF Gaming 24', 'ViewFinity S8', 'Optix MAG274QRF', 'ProArt PA278CV', 'Predator X34'],
            'laptop' => ['MacBook Pro 14 M3', 'MacBook Air 15 M2', 'XPS 15 9530', 'ROG Zephyrus G14', 'ThinkPad X1 Carbon', 'Zenbook 14 OLED', 'Legion Pro 7i', 'Blade 16 Gaming', 'Spectre x360', 'Gram 17 Pro'],
        ];

        $priceRanges = [
            'chuot' => [350000, 3990000, 50000],
            'ban-phim' => [790000, 5500000, 100000],
            'tai-nghe' => [690000, 13900000, 100000],
            'man-hinh' => [2990000, 39900000, 500000],
            'laptop' => [14990000, 79900000, 1000000],
        ];

        $now = now();
        $targetPerCategory = 500;

        foreach ($categories as $category) {
            $catSlug = $category->slug;
            $models = $productModels[$catSlug] ?? ['Standard Edition', 'Pro Version', 'Ultra Max', 'Plus Edition'];
            $images = $imagePool[$catSlug] ?? $imagePool['chuot'];
            $priceRange = $priceRanges[$catSlug] ?? [500000, 5000000, 50000];

            $this->command->info("Đang tạo {$targetPerCategory} sản phẩm cho danh mục: {$category->name}...");

            $productsBatch = [];
            $batchSize = 100;

            for ($i = 1; $i <= $targetPerCategory; $i++) {
                $brand = $brands->random();
                $modelName = $models[($i - 1) % count($models)];
                $suffix = ceil($i / count($models));
                $fullName = "{$category->name} {$brand->name} {$modelName}" . ($suffix > 1 ? " Gen {$suffix}" : '');
                $slug = Str::slug("{$fullName}-{$category->id}-{$i}");

                $minPrice = $priceRange[0];
                $maxPrice = $priceRange[1];
                $step = $priceRange[2];
                $rawPrice = rand($minPrice / $step, $maxPrice / $step) * $step;

                $imageUrl = $images[($i - 1) % count($images)];

                // Insert Product
                $productId = DB::table('products')->insertGetId([
                    'name'        => $fullName,
                    'slug'        => $slug,
                    'brand_id'    => $brand->id,
                    'category_id' => $category->id,
                    'description' => "{$fullName} chính hãng, bảo hành 24 tháng chính hãng tại TechVina. Đổi mới trong 30 ngày nếu có lỗi kỹ thuật từ nhà sản xuất.",
                    'is_active'   => true,
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ]);

                // Insert Product Variant
                DB::table('product_variants')->insert([
                    'product_id' => $productId,
                    'sku'        => 'TECH-' . strtoupper(Str::slug($brand->name)) . '-' . $productId,
                    'price'      => $rawPrice,
                    'stock'      => rand(5, 50),
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                // Insert Primary Image
                DB::table('product_images')->insert([
                    'product_id' => $productId,
                    'image_url'  => $imageUrl,
                    'is_primary' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        $this->command->info('Hoàn tất seed 500 sản phẩm cho mỗi danh mục!');
    }
}
