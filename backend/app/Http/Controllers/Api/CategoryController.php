<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class CategoryController extends Controller
{
    public function index(): JsonResponse
    {
        $categories = Cache::remember('catalog_categories_list', 3600, function () {
            return CategoryResource::collection(Category::all())->resolve();
        });

        return response()->json($categories);
    }

    /**
     * Dữ liệu Mega Menu phân loại đa tầng kèm danh sách thương hiệu có sản phẩm (Được Cache siêu tốc)
     */
    public function megaMenu(): JsonResponse
    {
        $result = Cache::remember('catalog_mega_menu', 3600, function () {
            $categories = Category::with(['products' => function ($q) {
                $q->where('is_active', true)->select('id', 'category_id', 'brand_id');
            }, 'products.brand:id,name,slug,logo_url'])->get();

            return $categories->map(function ($cat) {
                $brands = $cat->products
                    ->pluck('brand')
                    ->filter()
                    ->unique('id')
                    ->values()
                    ->map(function ($b) {
                        return [
                            'id'       => $b->id,
                            'name'     => $b->name,
                            'slug'     => $b->slug,
                            'logo_url' => $b->logo_url,
                        ];
                    })
                    ->all();

                return [
                    'id'          => $cat->id,
                    'name'        => $cat->name,
                    'slug'        => $cat->slug,
                    'description' => $cat->description,
                    'brands'      => $brands,
                ];
            })->all();
        });

        return response()->json([
            'status' => true,
            'data'   => $result,
        ]);
    }
}
