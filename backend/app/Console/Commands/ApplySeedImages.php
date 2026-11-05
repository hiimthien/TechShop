<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Product;
use App\Support\SeedImage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ApplySeedImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seed-images:apply';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gắn / cập nhật đường dẫn ảnh (JPG thực tế hoặc SVG fallback) từ SeedImage vào bảng product_images và product_variants';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $products = Product::with(['images.option', 'variants.options'])->get();

        if ($products->isEmpty()) {
            $this->warn('Không tìm thấy sản phẩm nào trong database.');
            return self::FAILURE;
        }

        $this->info("Bắt đầu gắn ảnh cho {$products->count()} sản phẩm...");

        $updatedVariants = 0;
        $updatedImages = 0;

        DB::beginTransaction();

        try {
            foreach ($products as $p) {
                // 1. Cập nhật product_variants
                foreach ($p->variants as $v) {
                    $optionSlug = '';
                    if ($v->options->isNotEmpty()) {
                        $sortedValues = $v->options->sortBy('attribute_id')->pluck('value');
                        $optionSlug = Str::slug($sortedValues->implode('-'));
                    }

                    $resolvedUrl = SeedImage::for($p->slug, $optionSlug);
                    if ($v->image_url !== $resolvedUrl) {
                        $v->image_url = $resolvedUrl;
                        $v->save();
                        $updatedVariants++;
                    }
                }

                // 2. Cập nhật product_images
                foreach ($p->images as $img) {
                    $optionSlug = $img->option ? Str::slug($img->option->value) : '';
                    if (empty($optionSlug)) {
                        $firstVar = $p->variants->first();
                        if ($firstVar && $firstVar->options->isNotEmpty()) {
                            $sortedValues = $firstVar->options->sortBy('attribute_id')->pluck('value');
                            $optionSlug = Str::slug($sortedValues->implode('-'));
                        }
                    }

                    $resolvedUrl = SeedImage::for($p->slug, $optionSlug);
                    if ($img->image_url !== $resolvedUrl) {
                        $img->image_url = $resolvedUrl;
                        $img->save();
                        $updatedImages++;
                    }
                }
            }

            DB::commit();

            $this->info("Đã gắn ảnh thành công:");
            $this->line(" - Cập nhật {$updatedVariants} bản ghi trong product_variants");
            $this->line(" - Cập nhật {$updatedImages} bản ghi trong product_images");

            // Tự động đồng bộ sang frontend
            $this->call('seed-images:sync');

            return self::SUCCESS;
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->error("Lỗi khi gắn ảnh: {$e->getMessage()}");
            return self::FAILURE;
        }
    }
}

