<?php

declare(strict_types=1);

namespace App\Support;

class SeedImage
{
    /**
     * Danh sách định dạng ảnh thực tế được hỗ trợ.
     *
     * @var array<string>
     */
    public const REAL_IMAGE_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp'];

    /**
     * Lấy URL ảnh sản phẩm cho seed theo thứ tự ưu tiên:
     * a) <product-slug>/<option-slug>.(jpg|jpeg|png|webp)
     * b) <product-slug>/default.(jpg|jpeg|png|webp)
     * c) <product-slug>/<option-slug>.svg placeholder
     */
    public static function for(string $productSlug, string $optionSlug): string
    {
        $baseDir = public_path("seed-images/{$productSlug}");

        // a) Kiểm tra ảnh riêng của biến thể
        foreach (self::REAL_IMAGE_EXTENSIONS as $ext) {
            $filePath = "{$baseDir}/{$optionSlug}.{$ext}";
            if (file_exists($filePath)) {
                return "/seed-images/{$productSlug}/{$optionSlug}.{$ext}";
            }
        }

        // b) Kiểm tra ảnh mặc định của sản phẩm
        foreach (self::REAL_IMAGE_EXTENSIONS as $ext) {
            $filePath = "{$baseDir}/default.{$ext}";
            if (file_exists($filePath)) {
                return "/seed-images/{$productSlug}/default.{$ext}";
            }
        }

        // c) Fallback về SVG placeholder
        return "/seed-images/{$productSlug}/{$optionSlug}.svg";
    }

    /**
     * Xác định trạng thái của ảnh: 'found' (ảnh riêng), 'default' (ảnh default), 'missing' (placeholder svg).
     */
    public static function resolveStatus(string $productSlug, string $optionSlug): string
    {
        $baseDir = public_path("seed-images/{$productSlug}");

        // a) Ảnh riêng
        foreach (self::REAL_IMAGE_EXTENSIONS as $ext) {
            if (file_exists("{$baseDir}/{$optionSlug}.{$ext}")) {
                return 'found';
            }
        }

        // b) Ảnh default
        foreach (self::REAL_IMAGE_EXTENSIONS as $ext) {
            if (file_exists("{$baseDir}/default.{$ext}")) {
                return 'default';
            }
        }

        // c) Placeholder
        return 'missing';
    }
}
