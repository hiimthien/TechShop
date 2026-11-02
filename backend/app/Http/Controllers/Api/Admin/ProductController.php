<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProductRequest;
use App\Http\Requests\Admin\UpdateProductRequest;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(
        protected ProductRepositoryInterface $productRepository,
        protected ProductService $productService
    ) {}

    /**
     * Display a paginated list of products with filters.
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['search', 'category_id', 'brand_id']);
        $perPage = (int) $request->input('per_page', 15);

        $products = $this->productRepository->getPaginated($filters, $perPage);

        return response()->json([
            'status' => true,
            'data' => $products,
        ]);
    }

    /**
     * Store a newly created product via ProductService.
     */
    public function store(StoreProductRequest $request): JsonResponse
    {
        $product = $this->productService->createProduct($request->validated());

        return response()->json([
            'status' => true,
            'message' => 'Thêm sản phẩm mới thành công',
            'data' => $product,
        ], 201);
    }

    /**
     * Display the specified product with variants and images.
     */
    public function show(string $id): JsonResponse
    {
        $product = $this->productRepository->findById($id);

        if (!$product) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy sản phẩm',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $product,
        ]);
    }

    /**
     * Update the specified product via ProductService.
     */
    public function update(UpdateProductRequest $request, string $id): JsonResponse
    {
        $product = $this->productRepository->findById($id);

        if (!$product) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy sản phẩm',
            ], 404);
        }

        $updated = $this->productService->updateProduct($product, $request->validated());

        return response()->json([
            'status' => true,
            'message' => 'Cập nhật sản phẩm thành công',
            'data' => $updated,
        ]);
    }

    /**
     * Remove the specified product.
     */
    public function destroy(string $id): JsonResponse
    {
        $product = $this->productRepository->findById($id);

        if (!$product) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy sản phẩm',
            ], 404);
        }

        $this->productService->deleteProduct($product);

        return response()->json([
            'status' => true,
            'message' => 'Đã xóa sản phẩm thành công',
        ]);
    }
}
