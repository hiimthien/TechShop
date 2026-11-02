<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Order;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface OrderRepositoryInterface
{
    /**
     * Lấy danh sách đơn hàng phân trang theo bộ lọc
     */
    public function getPaginated(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    /**
     * Lấy danh sách đơn hàng theo user ID
     */
    public function getByUserId(int|string $userId): Collection;

    /**
     * Tìm đơn hàng theo ID kèm quan hệ
     */
    public function findById(int|string $id, array $relations = ['user', 'address', 'coupon', 'items.variant.product.images']): ?Order;

    /**
     * Tạo đơn hàng mới
     */
    public function create(array $data): Order;

    /**
     * Cập nhật trạng thái đơn hàng
     */
    public function updateStatus(int|string $id, string $status): Order;

    /**
     * Xóa đơn hàng
     */
    public function delete(int|string $id): bool;
}

