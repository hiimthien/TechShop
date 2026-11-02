<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface ProductRepositoryInterface
{
    /**
     * Lấy danh sách sản phẩm phân trang theo bộ lọc
     */
    public function getPaginated(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    /**
     * Lấy toàn bộ sản phẩm theo bộ lọc
     */
    public function getFiltered(array $filters = []): Collection;

    /**
     * Tìm sản phẩm theo ID kèm quan hệ
     */
    public function findById(int|string $id, array $relations = ['brand', 'category', 'images', 'variants.options']): ?Product;

    /**
     * Tìm sản phẩm theo Slug kèm quan hệ
     */
    public function findBySlug(string $slug, array $relations = ['brand', 'category', 'images', 'variants.options']): ?Product;

    /**
     * Tạo bản ghi sản phẩm
     */
    public function create(array $data): Product;

    /**
     * Cập nhật bản ghi sản phẩm
     */
    public function update(Product $product, array $data): Product;

    /**
     * Xóa bản ghi sản phẩm
     */
    public function delete(Product $product): bool;
}

