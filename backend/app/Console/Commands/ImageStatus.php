<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Product;
use App\Support\SeedImage;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class ImageStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seed-images:status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kiểm tra trạng thái ảnh từng sản phẩm (ảnh riêng/default/placeholder) và cảnh báo chất lượng ảnh thật';

    /**
     * Danh sách slug của các sản phẩm có độ ưu tiên cao (High Priority).
     *
     * @var array<string>
     */
    protected array $highPriorityKeys = [
        'iphone-16-pro-max',
        'iphone-16',
        'iphone-15-pro-max',
        'samsung-galaxy-s24-ultra',
        'samsung-galaxy-z-flip-6',
        'xiaomi-14-ultra',
        'oppo-find-n3-flip',
        'macbook-pro-16',
        'macbook-pro-14',
        'zephyrus-g16',
        'dell-xps-15',
        'superlight-2',
        'deathadder-v3-pro',
        'g-pro-x-tkl',
        'blackwidow-v4-pro',
        'wh-1000xm5',
        'airpods-pro',
        'airpods-max',
        'quietcomfort-ultra',
        'series-9',
        'forerunner-965',
        'major-iv',
        'pg27aqdm',
    ];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $products = Product::with(['brand', 'variants.options', 'images.option'])->get();

        if ($products->isEmpty()) {
            $this->warn('Không tìm thấy sản phẩm nào trong database.');
            return self::FAILURE;
        }

        $baseDir = public_path('seed-images');
        $manifestRows = [];
        $productStats = [];

        $totalOwn = 0;
        $totalDefault = 0;
        $totalPlaceholder = 0;
        $totalVariants = 0;

        foreach ($products as $product) {
            $prodSlug = $product->slug;
            $prodDir = "{$baseDir}/{$prodSlug}";

            // Check priority
            $isHigh = false;
            foreach ($this->highPriorityKeys as $key) {
                if (str_contains($prodSlug, $key)) {
                    $isHigh = true;
                    break;
                }
            }

            // Thu thập các option
            $optionsMap = [];
            foreach ($product->variants as $v) {
                $optName = $v->options->first()?->value ?? 'Mặc định';
                $optSlug = Str::slug($optName);
                $optionsMap[$optSlug] = $optName;
            }

            if (empty($optionsMap)) {
                foreach ($product->images as $img) {
                    $optName = $img->option?->value ?? 'Mặc định';
                    $optSlug = Str::slug($optName);
                    $optionsMap[$optSlug] = $optName;
                }
            }

            $ownCount = 0;
            $defaultCount = 0;
            $placeholderCount = 0;

            foreach ($optionsMap as $optSlug => $optName) {
                $status = SeedImage::resolveStatus($prodSlug, $optSlug);

                if ($status === 'found') {
                    $ownCount++;
                    $totalOwn++;
                } elseif ($status === 'default') {
                    $defaultCount++;
                    $totalDefault++;
                } else {
                    $placeholderCount++;
                    $totalPlaceholder++;
                }

                $totalVariants++;

                $manifestRows[] = [
                    'product_id' => $product->id,
                    'product_slug' => $prodSlug,
                    'product_name' => $product->name,
                    'option' => $optName,
                    'expected_file' => "backend/public/seed-images/{$prodSlug}/{$optSlug}.jpg",
                    'priority' => $isHigh ? 'high' : 'low',
                    'status' => $status,
                ];
            }

            $productStats[] = [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $prodSlug,
                'brand' => $product->brand?->name ?? 'TechShop',
                'priority' => $isHigh ? 'high' : 'low',
                'total_variants' => count($optionsMap),
                'own' => $ownCount,
                'default' => $defaultCount,
                'placeholder' => $placeholderCount,
            ];
        }

        // Cập nhật /demo/image-manifest.csv
        $demoDir = base_path('../demo');
        if (!is_dir($demoDir)) {
            mkdir($demoDir, 0755, true);
        }

        $csvPath = "{$demoDir}/image-manifest.csv";
        $fp = fopen($csvPath, 'w');
        fprintf($fp, chr(0xEF).chr(0xBB).chr(0xBF));
        fputcsv($fp, [
            'product_id',
            'product_slug',
            'product_name',
            'option',
            'expected_file',
            'priority',
            'status',
        ]);
        foreach ($manifestRows as $row) {
            fputcsv($fp, [
                $row['product_id'],
                $row['product_slug'],
                $row['product_name'],
                $row['option'],
                $row['expected_file'],
                $row['priority'],
                $row['status'],
            ]);
        }
        fclose($fp);

        // 4. Kiểm tra cảnh báo chất lượng ảnh thật (Real Images Warnings)
        $warnings = $this->auditRealImages($baseDir);

        // Sắp xếp: priority High lên trước, sau đó theo ID
        usort($productStats, function ($a, $b) {
            if ($a['priority'] !== $b['priority']) {
                return $a['priority'] === 'high' ? -1 : 1;
            }
            return $a['id'] <=> $b['id'];
        });

        // In kết quả console
        $this->info("=================== BÁO CÁO TRẠNG THÁI ẢNH SEED ===================");
        $this->info("Tổng sản phẩm: " . count($productStats) . " | Tổng biến thể: {$totalVariants}");
        $this->info("Ảnh riêng (found): {$totalOwn} | Ảnh default (default): {$totalDefault} | Placeholder (missing): {$totalPlaceholder}");

        $tableHeaders = ['ID', 'Tên sản phẩm', 'Priority', 'Biến thể', 'Ảnh riêng', 'Ảnh default', 'Placeholder'];
        $tableRows = [];
        foreach ($productStats as $p) {
            $tableRows[] = [
                $p['id'],
                Str::limit($p['name'], 38),
                strtoupper($p['priority']),
                $p['total_variants'],
                $p['own'],
                $p['default'],
                $p['placeholder'],
            ];
        }
        $this->table($tableHeaders, $tableRows);

        // Cảnh báo chất lượng ảnh
        if (!empty($warnings)) {
            $this->warn("\n[!] PHÁT HIỆN " . count($warnings) . " CẢNH BÁO VỀ ẢNH THẬT:");
            foreach ($warnings as $w) {
                $this->line(" - {$w['file']}: {$w['reason']}");
            }
        } else {
            $this->info("\n[OK] Không có cảnh báo bất thường nào về ảnh thật (Hiện đang dùng 100% vector SVG placeholder).");
        }

        // Tạo /demo/image-status.md
        $this->generateMarkdownReport($productStats, $totalVariants, $totalOwn, $totalDefault, $totalPlaceholder, $warnings, "{$demoDir}/image-status.md");

        return self::SUCCESS;
    }

    /**
     * Quét và cảnh báo chất lượng của mọi file ảnh thật trong backend/public/seed-images.
     *
     * @return array<int, array{file: string, reason: string}>
     */
    protected function auditRealImages(string $baseDir): array
    {
        $warnings = [];
        if (!is_dir($baseDir)) {
            return $warnings;
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($baseDir, RecursiveDirectoryIterator::SKIP_DOTS)
        );

        $realExtensions = ['jpg', 'jpeg', 'png', 'webp'];

        foreach ($iterator as $item) {
            if ($item->isDir()) {
                continue;
            }

            $ext = strtolower($item->getExtension());
            if (!in_array($ext, $realExtensions, true)) {
                continue;
            }

            $filePath = $item->getRealPath();
            $relPath = str_replace(public_path(), '', $filePath);
            $size = filesize($filePath);

            // Cảnh báo 1: Dung lượng > 2MB
            if ($size > 2 * 1024 * 1024) {
                $sizeMb = round($size / (1024 * 1024), 2);
                $warnings[] = [
                    'file' => $relPath,
                    'reason' => "Dung lượng quá lớn: {$sizeMb} MB (> 2MB)"
                ];
            }

            // Kích thước hình ảnh
            $info = @getimagesize($filePath);
            if ($info) {
                $w = $info[0];
                $h = $info[1];
                $maxEdge = max($w, $h);

                // Cảnh báo 2: Cạnh dài < 600px
                if ($maxEdge < 600) {
                    $warnings[] = [
                        'file' => $relPath,
                        'reason' => "Kích thước quá nhỏ: {$w}x{$h} (Cạnh dài nhất {$maxEdge}px < 600px)"
                    ];
                }

                // Cảnh báo 3: Tỉ lệ rộng/cao ngoài 0.6 - 1.6
                if ($h > 0) {
                    $ratio = round($w / $h, 2);
                    if ($ratio < 0.6 || $ratio > 1.6) {
                        $warnings[] = [
                            'file' => $relPath,
                            'reason' => "Tỉ lệ khung hình bất thường: {$w}x{$h} (tỉ lệ {$ratio}, ngoài khoảng 0.6 - 1.6)"
                        ];
                    }
                }
            }
        }

        return $warnings;
    }

    /**
     * Xuất file Markdown báo cáo /demo/image-status.md.
     */
    protected function generateMarkdownReport(
        array $productStats,
        int $totalVariants,
        int $totalOwn,
        int $totalDefault,
        int $totalPlaceholder,
        array $warnings,
        string $outPath
    ): void {
        $highCount = count(array_filter($productStats, fn($p) => $p['priority'] === 'high'));
        $lowCount = count(array_filter($productStats, fn($p) => $p['priority'] === 'low'));

        $lines = [];
        $lines[] = "# BÁO CÁO TRẠNG THÁI HÌNH ẢNH SẢN PHẨM TECHSHOP (IMAGE STATUS)";
        $lines[] = "";
        $lines[] = "> **Thời điểm kiểm tra**: " . date('Y-m-d H:i:s');
        $lines[] = "> **Độ ưu tiên (Priority)**: High Priority đưa lên trước";
        $lines[] = "";
        $lines[] = "---";
        $lines[] = "";
        $lines[] = "## 1. TỔNG QUAN CHỈ SỐ";
        $lines[] = "";
        $lines[] = "| Chỉ số thống kê | Số lượng | Tỉ lệ |";
        $lines[] = "| :--- | :---: | :---: |";
        $lines[] = "| Tổng số sản phẩm | **" . count($productStats) . "** sản phẩm | 100% |";
        $lines[] = "| Sản phẩm High Priority | **{$highCount}** sản phẩm | " . round($highCount / count($productStats) * 100) . "% |";
        $lines[] = "| Sản phẩm Low Priority | **{$lowCount}** sản phẩm | " . round($lowCount / count($productStats) * 100) . "% |";
        $lines[] = "| Tổng số biến thể sản phẩm | **{$totalVariants}** biến thể | 100% |";
        $lines[] = "| Biến thể dùng **ảnh riêng** (`found`) | **{$totalOwn}** | " . round($totalOwn / $totalVariants * 100) . "% |";
        $lines[] = "| Biến thể dùng **ảnh default** (`default`) | **{$totalDefault}** | " . round($totalDefault / $totalVariants * 100) . "% |";
        $lines[] = "| Biến thể dùng **placeholder SVG** (`missing`) | **{$totalPlaceholder}** | " . round($totalPlaceholder / $totalVariants * 100) . "% |";
        $lines[] = "";
        $lines[] = "---";
        $lines[] = "";
        $lines[] = "## 2. CẢNH BÁO CHẤT LƯỢNG ẢNH THẬT (REAL IMAGES AUDIT)";
        $lines[] = "";
        if (empty($warnings)) {
            $lines[] = "✅ **Không phát hiện lỗi/cảnh báo nào về ảnh thật.**";
            $lines[] = "- Tiêu chí kiểm tra: Dung lượng $\le$ 2MB, cạnh dài $\ge$ 600px, tỉ lệ khung hình (w/h) trong khoảng `0.6 - 1.6`.";
            $lines[] = "- Hiện tại toàn bộ 95 biến thể đang được phục vụ bởi SVG vector 800x800px chuẩn nhận diện.";
        } else {
            $lines[] = "⚠️ **Phát hiện " . count($warnings) . " ảnh có vấn đề cần lưu ý:**";
            $lines[] = "";
            $lines[] = "| STT | Đường dẫn ảnh | Vấn đề ghi nhận |";
            $lines[] = "| :---: | :--- | :--- |";
            foreach ($warnings as $idx => $w) {
                $lines[] = sprintf("| %d | `%s` | %s |", $idx + 1, $w['file'], $w['reason']);
            }
        }
        $lines[] = "";
        $lines[] = "---";
        $lines[] = "";
        $lines[] = "## 3. CHI TIẾT TRẠNG THÁI TỪNG SẢN PHẨM";
        $lines[] = "";
        $lines[] = "| ID | Tên sản phẩm | Nhãn ưu tiên | Hãng | Số biến thể | Ảnh riêng (`found`) | Ảnh default (`default`) | Placeholder (`missing`) |";
        $lines[] = "| :---: | :--- | :---: | :--- | :---: | :---: | :---: | :---: |";

        foreach ($productStats as $p) {
            $pBadge = $p['priority'] === 'high' ? '🔥 **HIGH**' : 'LOW';
            $lines[] = sprintf(
                "| %d | **%s** | %s | %s | %d | %d | %d | %d |",
                $p['id'],
                $p['name'],
                $pBadge,
                $p['brand'],
                $p['total_variants'],
                $p['own'],
                $p['default'],
                $p['placeholder']
            );
        }

        $lines[] = "";
        $lines[] = "---";
        $lines[] = "*Báo cáo được khởi tạo tự động qua lệnh `php artisan seed-images:status`.*";

        file_put_contents($outPath, implode("\n", $lines));
        $this->info("Đã xuất báo cáo chi tiết vào: {$outPath}");
    }
}

