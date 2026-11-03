<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Product::with([
            'brand',
            'category',
            'images'
        ])->withMin('variants', 'price')
            ->where('is_active', true);

        // Lọc danh mục: Hỗ trợ cả slug (bảo mật SEO không lộ ID) và category_id (tương thích ngược)
        if ($request->filled('category_slug') || $request->filled('category')) {
            $catSlug = (string) ($request->input('category_slug') ?: $request->input('category'));
            $aliases = [
                'smartphones'  => 'dien-thoai',
                'smartphone'   => 'dien-thoai',
                'laptops'      => 'laptop',
                'keyboards'    => 'ban-phim',
                'keyboard'     => 'ban-phim',
                'monitors'     => 'man-hinh',
                'monitor'      => 'man-hinh',
                'headphones'   => 'tai-nghe',
                'headphone'    => 'tai-nghe',
                'audio'        => 'tai-nghe-am-thanh',
                'smartwatches' => 'dong-ho-thong-minh',
                'smartwatch'   => 'dong-ho-thong-minh',
                'watches'      => 'dong-ho-thong-minh',
                'mouse'        => 'chuot',
                'mice'         => 'chuot',
            ];
            $targetSlug = $aliases[$catSlug] ?? $catSlug;
            $query->whereHas('category', fn($q) => $q->where('slug', $targetSlug));
        } elseif ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Lọc đa thương hiệu: Hỗ trợ cả slug (bảo mật SEO không lộ ID) và brand_ids
        if ($request->filled('brand_slugs') || $request->filled('brands') || $request->filled('brand')) {
            $rawBrands = $request->input('brand_slugs') ?: $request->input('brands') ?: $request->input('brand');
            $brandSlugs = is_array($rawBrands) ? $rawBrands : explode(',', (string) $rawBrands);
            $query->whereHas('brand', fn($q) => $q->whereIn('slug', array_filter($brandSlugs)));
        } elseif ($request->filled('brand_ids')) {
            $brandIds = is_array($request->brand_ids)
                ? $request->brand_ids
                : explode(',', (string) $request->brand_ids);
            $query->whereIn('brand_id', array_filter($brandIds));
        } elseif ($request->filled('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }

        // Tìm kiếm từ khóa
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Lọc theo tình trạng còn hàng
        if ($request->boolean('in_stock')) {
            $query->whereHas('variants', function ($q) {
                $q->where('stock', '>', 0);
            });
        }

        // 1. Laptop: Lọc theo RAM
        if ($request->filled('ram')) {
            $ram = $request->ram;
            $query->where(function ($q) use ($ram) {
                $q->whereHas('variants.options', fn($opt) => $opt->where('value', $ram))
                  ->orWhere('name', 'like', "%{$ram}%");
            });
        }

        // 2. Laptop: Lọc theo Ổ cứng SSD
        if ($request->filled('storage')) {
            $storage = $request->storage;
            $query->where(function ($q) use ($storage) {
                $q->whereHas('variants.options', fn($opt) => $opt->where('value', $storage))
                  ->orWhere('name', 'like', "%{$storage}%");
            });
        }

        // 3. Chuột / Bàn phím / Tai nghe: Lọc theo Kiểu kết nối (Wireless / Bluetooth / Có dây)
        if ($request->filled('connection')) {
            $conn = $request->connection;
            $query->where(function ($q) use ($conn) {
                $q->whereHas('variants.options', fn($opt) => $opt->where('value', 'like', "%{$conn}%"))
                  ->orWhere('name', 'like', "%{$conn}%")
                  ->orWhere('description', 'like', "%{$conn}%");
            });
        }

        // 4. Màn hình: Lọc theo Độ phân giải / Tần số quét (4K, 2K, 144Hz, 165Hz)
        if ($request->filled('screen_feature')) {
            $feat = $request->screen_feature;
            $query->where(function ($q) use ($feat) {
                $q->whereHas('variants.options', fn($opt) => $opt->where('value', 'like', "%{$feat}%"))
                  ->orWhere('name', 'like', "%{$feat}%")
                  ->orWhere('description', 'like', "%{$feat}%");
            });
        }

        // Lọc khoảng giá
        if ($request->filled('min_price')) {
            $query->whereHas('variants', function ($q) use ($request) {
                $q->where('price', '>=', (float) $request->min_price);
            });
        }

        if ($request->filled('max_price')) {
            $query->whereHas('variants', function ($q) use ($request) {
                $q->where('price', '<=', (float) $request->max_price);
            });
        }

        // Sắp xếp
        $sort = $request->get('sort', 'latest');
        match ($sort) {
            'price_asc'  => $query->orderBy('variants_min_price', 'asc'),
            'price_desc' => $query->orderBy('variants_min_price', 'desc'),
            'name_asc'   => $query->orderBy('name', 'asc'),
            default      => $query->latest('id'),
        };

        $products = $query->paginate($request->get('per_page', 9));

        return response()->json([
            'data' => ProductResource::collection($products),
            'pagination' => [
                'total'        => $products->total(),
                'per_page'     => $products->perPage(),
                'current_page' => $products->currentPage(),
                'last_page'    => $products->lastPage(),
            ],
        ]);
    }

    public function show(string $slug): JsonResponse
    {
        $product = Product::with(['brand', 'category', 'variants.options', 'images'])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        // Lấy danh sách 8 sản phẩm liên quan để hiển thị khối "Có thể bạn cũng thích"
        $relatedProducts = Product::with(['brand', 'category', 'images'])
            ->withMin('variants', 'price')
            ->where('is_active', true)
            ->where('id', '!=', $product->id)
            ->where(function ($query) use ($product) {
                $query->where('category_id', $product->category_id)
                    ->orWhere('brand_id', $product->brand_id);
            })
            ->latest('id')
            ->limit(8)
            ->get();

        $product->related_products = $relatedProducts;

        return response()->json(new ProductResource($product));
    }
}
