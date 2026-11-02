<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

interface CategoryRepositoryInterface
{
    /**
     * Lấy danh sách danh mục kèm số lượng sản phẩm
     */
    public function getAllWithProductCount(?string $search = null): Collection;

    /**
     * Tìm danh mục theo ID
     */
    public function findById(int|string $id): ?Category;

    /**
     * Tạo danh mục mới
     */
    public function create(array $data): Category;

    /**
     * Cập nhật danh mục
     */
    public function update(Category $category, array $data): Category;

    /**
     * Xóa danh mục
     */
    public function delete(Category $category): bool;
}

