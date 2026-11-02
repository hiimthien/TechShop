<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Brand;
use Illuminate\Database\Eloquent\Collection;

interface BrandRepositoryInterface
{
    /**
     * Lấy danh sách thương hiệu kèm số lượng sản phẩm
     */
    public function getAllWithProductCount(?string $search = null): Collection;

    /**
     * Tìm thương hiệu theo ID
     */
    public function findById(int|string $id): ?Brand;

    /**
     * Tạo thương hiệu mới
     */
    public function create(array $data): Brand;

    /**
     * Cập nhật thương hiệu
     */
    public function update(Brand $brand, array $data): Brand;

    /**
     * Xóa thương hiệu
     */
    public function delete(Brand $brand): bool;
}

