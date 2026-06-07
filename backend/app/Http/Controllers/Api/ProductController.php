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

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $products = $query->paginate($request->get('per_page', 10));

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

        return response()->json(new ProductResource($product));
    }
}
