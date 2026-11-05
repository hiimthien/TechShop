<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class GenerateImagePlaceholders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seed-images:generate {--force : Ghi đè file SVG nếu đã tồn tại}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sinh placeholder SVG 800x800 cho tất cả biến thể sản phẩm chưa có ảnh JPG';

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
        $products = Product::with(['brand', 'images.option', 'variants.options'])->get();

        if ($products->isEmpty()) {
            $this->warn('Không tìm thấy sản phẩm nào trong database.');
            return self::FAILURE;
        }

        $baseDir = public_path('seed-images');
        if (!is_dir($baseDir)) {
            mkdir($baseDir, 0755, true);
        }

        $manifestRows = [];
        $svgGenerated = 0;
        $foundCount = 0;
        $missingCount = 0;

        $this->info("Bắt đầu khởi tạo thư mục và placeholder SVG cho {$products->count()} sản phẩm...");

        foreach ($products as $product) {
            $prodSlug = $product->slug;
            $prodDir = "{$baseDir}/{$prodSlug}";
            if (!is_dir($prodDir)) {
                mkdir($prodDir, 0755, true);
            }

            $brandName = $product->brand?->name ?? 'TechShop';

            // Thu thập các option từ variants hoặc images
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

            // Determine priority
            $isHigh = false;
            foreach ($this->highPriorityKeys as $key) {
                if (str_contains($prodSlug, $key)) {
                    $isHigh = true;
                    break;
                }
            }

            foreach ($optionsMap as $optSlug => $optName) {
                $jpgPath = "{$prodDir}/{$optSlug}.jpg";
                $svgPath = "{$prodDir}/{$optSlug}.svg";
                $expectedFile = "backend/public/seed-images/{$prodSlug}/{$optSlug}.jpg";

                $hasJpg = file_exists($jpgPath);
                $status = $hasJpg ? 'found' : 'missing';

                if ($hasJpg) {
                    $foundCount++;
                } else {
                    $missingCount++;
                }

                // Sinh SVG nếu chưa có hoặc có cờ --force
                if (!file_exists($svgPath) || $this->option('force')) {
                    $svgContent = $this->renderSvg($brandName, $product->name, $optName);
                    file_put_contents($svgPath, $svgContent);
                    $svgGenerated++;
                }

                $manifestRows[] = [
                    'product_id' => $product->id,
                    'product_slug' => $prodSlug,
                    'product_name' => $product->name,
                    'option' => $optName,
                    'expected_file' => $expectedFile,
                    'priority' => $isHigh ? 'high' : 'low',
                    'status' => $status,
                ];
            }
        }

        // Ghi /demo/image-manifest.csv
        $demoDir = base_path('../demo');
        if (!is_dir($demoDir)) {
            mkdir($demoDir, 0755, true);
        }

        $csvPath = "{$demoDir}/image-manifest.csv";
        $fp = fopen($csvPath, 'w');
        // UTF-8 BOM
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

        $this->info("Đã tạo {$svgGenerated} file SVG placeholder.");
        $this->info("Đã lưu manifest vào: {$csvPath} (Tổng: " . count($manifestRows) . " dòng; Found: {$foundCount}, Missing: {$missingCount})");

        // Đồng bộ sang frontend/public/seed-images qua lệnh seed-images:sync
        $this->call('seed-images:sync');

        return self::SUCCESS;
    }

    /**
     * Tạo nội dung SVG 800x800 chuẩn nhận diện, font hệ thống, không tràn chữ.
     */
    protected function renderSvg(string $brand, string $productName, string $optionName): string
    {
        $brandUpper = htmlspecialchars(mb_strtoupper($brand, 'UTF-8'));
        $optionEsc = htmlspecialchars($optionName);

        // Word wrap tên sản phẩm gọn gàng tối đa 3 dòng
        $words = explode(' ', $productName);
        $lines = [];
        $currentLine = '';
        foreach ($words as $word) {
            if (mb_strlen($currentLine . ' ' . $word) > 20) {
                if ($currentLine !== '') {
                    $lines[] = trim($currentLine);
                }
                $currentLine = $word;
            } else {
                $currentLine .= ' ' . $word;
            }
        }
        if ($currentLine !== '') {
            $lines[] = trim($currentLine);
        }
        if (count($lines) > 3) {
            $lines = array_slice($lines, 0, 3);
            $lines[2] .= '...';
        }

        $lineCount = count($lines);
        $startTextY = 380 - (($lineCount - 1) * 22);

        $titleTspans = '';
        foreach ($lines as $i => $line) {
            $y = $startTextY + ($i * 44);
            $lineEsc = htmlspecialchars($line);
            $titleTspans .= "<text x=\"400\" y=\"{$y}\" text-anchor=\"middle\" font-size=\"32\" font-weight=\"800\" fill=\"#0f172a\" font-family=\"system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif\">{$lineEsc}</text>\n";
        }

        $brandY = $startTextY - 65;
        $brandTextY = $brandY + 21;
        $brandBadgeWidth = max(140, mb_strlen($brandUpper) * 12 + 40);
        $brandBadgeX = 400 - ($brandBadgeWidth / 2);

        $optionY = $startTextY + (($lineCount - 1) * 44) + 65;
        $optBadgeWidth = min(720, max(220, mb_strlen($optionName) * 11 + 44));
        $optBadgeX = -($optBadgeWidth / 2);

        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 800" width="800" height="800">
  <defs>
    <linearGradient id="bgGrad" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="#f8fafc" />
      <stop offset="100%" stop-color="#e2e8f0" />
    </linearGradient>
  </defs>
  <rect width="800" height="800" fill="url(#bgGrad)" />
  <rect x="24" y="24" width="752" height="752" rx="28" fill="none" stroke="#cbd5e1" stroke-width="2" />
  
  <!-- Brand Badge -->
  <rect x="{$brandBadgeX}" y="{$brandY}" width="{$brandBadgeWidth}" height="32" rx="16" fill="#e2e8f0" stroke="#cbd5e1" stroke-width="1" />
  <text x="400" y="{$brandTextY}" text-anchor="middle" font-size="14" font-weight="700" letter-spacing="1.5" fill="#475569" font-family="system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif">{$brandUpper}</text>
  
  <!-- Product Title -->
  {$titleTspans}
  
  <!-- Option Pill -->
  <g transform="translate(400, {$optionY})">
    <rect x="{$optBadgeX}" y="-22" width="{$optBadgeWidth}" height="44" rx="22" fill="#eff6ff" stroke="#bfdbfe" stroke-width="1.5" />
    <text x="0" y="6" text-anchor="middle" font-size="18" font-weight="600" fill="#1d4ed8" font-family="system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif">{$optionEsc}</text>
  </g>
  
  <!-- Watermark footer -->
  <text x="400" y="740" text-anchor="middle" font-size="13" font-weight="500" fill="#94a3b8" letter-spacing="1" font-family="system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif">TECHSHOP PRODUCT DEMO</text>
</svg>
SVG;
    }
}

