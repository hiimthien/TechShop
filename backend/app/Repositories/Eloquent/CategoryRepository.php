<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Category;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function getAllWithProductCount(?string $search = null): Collection
    {
        $query = Category::withCount('products');

        if (!empty($search)) {
            $query->where('name', 'like', "%{$search}%");
        }

        return $query->latest('id')->get();
    }

    public function findById(int|string $id): ?Category
    {
        return Category::withCount('products')->find($id);
    }

    public function create(array $data): Category
    {
        return Category::create($data);
    }

    public function update(Category $category, array $data): Category
    {
        $category->update($data);
        return $category;
    }

    public function delete(Category $category): bool
    {
        return (bool) $category->delete();
    }
}

