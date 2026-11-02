<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ProductRepository implements ProductRepositoryInterface
{
    public function getPaginated(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Product::with(['brand:id,name', 'category:id,name', 'images', 'variants.options']);

        if (!empty($filters['search'])) {
            $search = trim((string) $filters['search']);
            $query->where('name', 'like', "%{$search}%");
        }

        if (!empty($filters['category_id']) && $filters['category_id'] !== 'all') {
            $query->where('category_id', $filters['category_id']);
        }

        if (!empty($filters['brand_id']) && $filters['brand_id'] !== 'all') {
            $query->where('brand_id', $filters['brand_id']);
        }

        return $query->latest('id')->paginate($perPage);
    }

    public function getFiltered(array $filters = []): Collection
    {
        $query = Product::with(['brand', 'category', 'images', 'variants.options'])->where('is_active', true);

        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (!empty($filters['brand_id'])) {
            $query->where('brand_id', $filters['brand_id']);
        }

        return $query->latest('id')->get();
    }

    public function findById(int|string $id, array $relations = ['brand', 'category', 'images', 'variants.options']): ?Product
    {
        return Product::with($relations)->find($id);
    }

    public function findBySlug(string $slug, array $relations = ['brand', 'category', 'images', 'variants.options']): ?Product
    {
        return Product::with($relations)->where('slug', $slug)->first();
    }

    public function create(array $data): Product
    {
        return Product::create($data);
    }

    public function update(Product $product, array $data): Product
    {
        $product->update($data);
        return $product;
    }

    public function delete(Product $product): bool
    {
        return (bool) $product->delete();
    }
}

