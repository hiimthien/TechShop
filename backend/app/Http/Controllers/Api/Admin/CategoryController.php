<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function __construct(
        protected CategoryRepositoryInterface $categoryRepository
    ) {}

    public function index(Request $request): JsonResponse
    {
        $categories = $this->categoryRepository->getAllWithProductCount($request->input('search'));

        return response()->json([
            'status' => true,
            'data' => $categories,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string',
        ]);

        $category = $this->categoryRepository->create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'] ?? null,
        ]);

        Cache::forget('catalog_categories_list');
        Cache::forget('catalog_mega_menu');

        return response()->json([
            'status' => true,
            'message' => 'Tạo danh mục mới thành công',
            'data' => $category,
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $category = $this->categoryRepository->findById($id);

        if (!$category) {
            return response()->json(['status' => false, 'message' => 'Không tìm thấy danh mục'], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $category,
        ]);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $category = $this->categoryRepository->findById($id);

        if (!$category) {
            return response()->json(['status' => false, 'message' => 'Không tìm thấy danh mục'], 404);
        }

        $validated = $request->validate([
            'name' => "required|string|max:255|unique:categories,name,{$id}",
            'description' => 'nullable|string',
        ]);

        $updated = $this->categoryRepository->update($category, [
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'] ?? $category->description,
        ]);

        Cache::forget('catalog_categories_list');
        Cache::forget('catalog_mega_menu');

        return response()->json([
            'status' => true,
            'message' => 'Cập nhật danh mục thành công',
            'data' => $updated,
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $category = $this->categoryRepository->findById($id);

        if (!$category) {
            return response()->json(['status' => false, 'message' => 'Không tìm thấy danh mục'], 404);
        }

        if ($category->products()->count() > 0) {
            return response()->json([
                'status' => false,
                'message' => 'Không thể xóa danh mục đang có sản phẩm thuộc về!',
            ], 422);
        }

        $this->categoryRepository->delete($category);

        Cache::forget('catalog_categories_list');
        Cache::forget('catalog_mega_menu');

        return response()->json([
            'status' => true,
            'message' => 'Đã xóa danh mục thành công',
        ]);
    }
}
