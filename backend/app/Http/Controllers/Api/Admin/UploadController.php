<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UploadController extends Controller
{
    public function upload(Request $request): JsonResponse
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // max 5MB
        ]);

        $file = $request->file('image');
        $fileName = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('uploads/products', $fileName, 'public');

        $url = asset('storage/' . $path);

        return response()->json([
            'status' => true,
            'message' => 'Upload ảnh thành công',
            'url' => $url,
            'path' => $path,
        ]);
    }

    public function uploadMultiple(Request $request): JsonResponse
    {
        $request->validate([
            'images' => 'required|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        $urls = [];
        foreach ($request->file('images') as $file) {
            $fileName = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('uploads/products', $fileName, 'public');
            $urls[] = asset('storage/' . $path);
        }

        return response()->json([
            'status' => true,
            'message' => 'Upload ' . count($urls) . ' ảnh thành công',
            'urls' => $urls,
        ]);
    }
}
