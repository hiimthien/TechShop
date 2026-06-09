<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{

    public function index()
    {
        $products = Product::all();

        return response()->json([
            'status' => true,
            'data' => $products
        ]);
    }

    public function store(StoreProductRequest $request)
    {
        $product = Product::create([
            'name'        => $request->name,
            'slug'       => Str::slug($request->name),
            'brand_id'     => $request->brand_id,
            'category_id'        => $request->category_id,
            'is_active'   => $request->is_active,
            'description' => $request->description,

        ]);

        return response()->json([
            'status' => true,
            'message' => 'Product created successfully',
        ]);
    }

    public function update(StoreProductRequest $request, string $id)
    {
        $product = Product::where('id', $id)
            ->firstOrFail();

        $data = $request->only('name', 'brand_id', 'category_id', 'is_active', 'description');

        if ($request->filled('name')) {
            $data['slug'] = Str::slug($request->name);
        }

        $product->update($data);

        return response()->json([
            'status' => true,
            'message' => 'Product updated successfully',
        ]);
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return response()->json([
            'status' => true,
            'message' => 'Product is deleted'
        ]);
    }
}
