<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Order;
use App\Repositories\Contracts\OrderRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class OrderRepository implements OrderRepositoryInterface
{
    public function getPaginated(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Order::with([
            'user:id,name,email,phone',
            'address',
            'coupon:id,code,value,type',
            'items.variant.product.images',
        ]);

        if (!empty($filters['status']) && $filters['status'] !== 'all') {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['search'])) {
            $search = trim((string) $filters['search']);
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                    ->orWhere('ghn_order_code', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    })
                    ->orWhereHas('address', function ($aq) use ($search) {
                        $aq->where('name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%")
                            ->orWhere('address', 'like', "%{$search}%");
                    });
            });
        }

        return $query->latest()->paginate($perPage);
    }

    public function getByUserId(int|string $userId): Collection
    {
        return Order::where('user_id', $userId)->latest()->get();
    }

    public function findById(int|string $id, array $relations = ['user', 'address', 'coupon', 'items.variant.product.images']): ?Order
    {
        return Order::with($relations)->find($id);
    }

    public function create(array $data): Order
    {
        return Order::create($data);
    }

    public function updateStatus(int|string $id, string $status): Order
    {
        $order = Order::with('items.variant')->findOrFail($id);
        $oldStatus = $order->status;

        DB::transaction(function () use ($order, $status, $oldStatus) {
            $order->status = $status;
            $order->save();

            // Tự động hoàn lại tồn kho nếu admin chuyển trạng thái sang đã hủy
            if ($status === 'cancelled' && $oldStatus !== 'cancelled') {
                foreach ($order->items as $item) {
                    $item->variant?->increment('stock', $item->quantity);
                }
            }
        });

        return $order;
    }

    public function delete(int|string $id): bool
    {
        $order = Order::findOrFail($id);
        return (bool) $order->delete();
    }
}

