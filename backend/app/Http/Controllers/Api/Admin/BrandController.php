<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\BrandRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class BrandController extends Controller
{
    public function __construct(
        protected BrandRepositoryInterface $brandRepository
    ) {}

    public function index(Request $request): JsonResponse
    {
        $brands = $this->brandRepository->getAllWithProductCount($request->input('search'));

        return response()->json([
            'status' => true,
            'data' => $brands,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:brands,name',
            'logo_url' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $brand = $this->brandRepository->create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'logo_url' => $validated['logo_url'] ?? null,
            'description' => $validated['description'] ?? null,
        ]);

        Cache::forget('catalog_brands_list');
        Cache::forget('catalog_mega_menu');

        return response()->json([
            'status' => true,
            'message' => 'Tạo thương hiệu mới thành công',
            'data' => $brand,
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $brand = $this->brandRepository->findById($id);

        if (!$brand) {
            return response()->json(['status' => false, 'message' => 'Không tìm thấy thương hiệu'], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $brand,
        ]);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $brand = $this->brandRepository->findById($id);

        if (!$brand) {
            return response()->json(['status' => false, 'message' => 'Không tìm thấy thương hiệu'], 404);
        }

        $validated = $request->validate([
            'name' => "required|string|max:255|unique:brands,name,{$id}",
            'logo_url' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $updated = $this->brandRepository->update($brand, [
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'logo_url' => $validated['logo_url'] ?? $brand->logo_url,
            'description' => $validated['description'] ?? $brand->description,
        ]);

        Cache::forget('catalog_brands_list');
        Cache::forget('catalog_mega_menu');

        return response()->json([
            'status' => true,
            'message' => 'Cập nhật thương hiệu thành công',
            'data' => $updated,
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $brand = $this->brandRepository->findById($id);

        if (!$brand) {
            return response()->json(['status' => false, 'message' => 'Không tìm thấy thương hiệu'], 404);
        }

        if ($brand->products()->count() > 0) {
            return response()->json([
                'status' => false,
                'message' => 'Không thể xóa thương hiệu đang có sản phẩm thuộc về!',
            ], 422);
        }

        $this->brandRepository->delete($brand);

        Cache::forget('catalog_brands_list');
        Cache::forget('catalog_mega_menu');

        return response()->json([
            'status' => true,
            'message' => 'Đã xóa thương hiệu thành công',
        ]);
    }
}
