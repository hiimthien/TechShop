<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class SyncSeedImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seed-images:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Đồng bộ ảnh (jpg, jpeg, png, webp, svg) từ backend/public/seed-images sang frontend/public/seed-images';

    /**
     * Các định dạng ảnh được phép đồng bộ.
     *
     * @var array<string>
     */
    protected array $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'svg'];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $srcDir = public_path('seed-images');
        $dstDir = base_path('../frontend/public/seed-images');

        if (!is_dir($srcDir)) {
            $this->warn("Thư mục nguồn không tồn tại: {$srcDir}");
            return self::FAILURE;
        }

        if (!is_dir($dstDir)) {
            mkdir($dstDir, 0755, true);
        }

        $copiedCount = 0;
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($srcDir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $item) {
            $subPath = $iterator->getSubPathname();
            $targetPath = "{$dstDir}/{$subPath}";

            if ($item->isDir()) {
                if (!is_dir($targetPath)) {
                    mkdir($targetPath, 0755, true);
                }
            } else {
                $ext = strtolower($item->getExtension());
                if (in_array($ext, $this->allowedExtensions, true)) {
                    $targetDir = dirname($targetPath);
                    if (!is_dir($targetDir)) {
                        mkdir($targetDir, 0755, true);
                    }
                    copy($item->getRealPath(), $targetPath);
                    $copiedCount++;
                }
            }
        }

        $this->info("Đã đồng bộ thành công {$copiedCount} file sang frontend/public/seed-images.");
        return self::SUCCESS;
    }
}

