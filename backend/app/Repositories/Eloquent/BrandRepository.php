<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Brand;
use App\Repositories\Contracts\BrandRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class BrandRepository implements BrandRepositoryInterface
{
    public function getAllWithProductCount(?string $search = null): Collection
    {
        $query = Brand::withCount('products');

        if (!empty($search)) {
            $query->where('name', 'like', "%{$search}%");
        }

        return $query->latest('id')->get();
    }

    public function findById(int|string $id): ?Brand
    {
        return Brand::withCount('products')->find($id);
    }

    public function create(array $data): Brand
    {
        return Brand::create($data);
    }

    public function update(Brand $brand, array $data): Brand
    {
        $brand->update($data);
        return $brand;
    }

    public function delete(Brand $brand): bool
    {
        return (bool) $brand->delete();
    }
}

