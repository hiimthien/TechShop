<?php

use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\Admin\BrandController as AdminBrandController;
use App\Http\Controllers\Api\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Api\Admin\CouponController as AdminCouponController;
use App\Http\Controllers\Api\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Api\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Api\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Api\Admin\UploadController as AdminUploadController;
use App\Http\Controllers\Api\Admin\WarrantyController as AdminWarrantyController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BrandController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\WarrantyClaimController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Auth Routes
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);
    });
});

// Public Catalog Routes
Route::get('categories', [CategoryController::class, 'index']);
Route::get('brands', [BrandController::class, 'index']);
Route::get('products', [ProductController::class, 'index']);
Route::get('products/{slug}', [ProductController::class, 'show']);

// Public Warranty Claims (tra cứu & gửi yêu cầu)
Route::post('warranty-claims', [WarrantyClaimController::class, 'store']);
Route::get('warranty-claims/lookup', [WarrantyClaimController::class, 'lookup']);

// Authenticated Customer Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('cart')->group(function () {
        Route::get('/', [CartController::class, 'index']);
        Route::post('items', [CartController::class, 'store']);
        Route::put('items/{item}', [CartController::class, 'update']);
        Route::delete('items/{item}', [CartController::class, 'destroy']);
    });

    Route::apiResource('orders', OrderController::class)->only(['index', 'show', 'store']);
    Route::apiResource('addresses', AddressController::class)->except(['show']);
    Route::get('warranty-claims/me', [WarrantyClaimController::class, 'myClaims']);
});

// Admin Protected Routes
Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {
    // Image Upload
    Route::post('upload', [AdminUploadController::class, 'upload']);
    Route::post('upload-multiple', [AdminUploadController::class, 'uploadMultiple']);

    // Dashboard Stats & Analytics
    Route::get('dashboard', [AdminDashboardController::class, 'index']);

    // Order Management
    Route::get('orders', [AdminOrderController::class, 'index']);
    Route::get('orders/{id}', [AdminOrderController::class, 'show']);
    Route::patch('orders/{id}/status', [AdminOrderController::class, 'updateStatus']);
    Route::delete('orders/{id}', [AdminOrderController::class, 'destroy']);

    // Coupon / Voucher Management
    Route::apiResource('coupons', AdminCouponController::class);
    Route::patch('coupons/{id}/toggle', [AdminCouponController::class, 'toggle']);

    // Warranty Management
    Route::get('warranties', [AdminWarrantyController::class, 'index']);
    Route::get('warranties/{id}', [AdminWarrantyController::class, 'show']);
    Route::patch('warranties/{id}/status', [AdminWarrantyController::class, 'updateStatus']);
    Route::delete('warranties/{id}', [AdminWarrantyController::class, 'destroy']);

    // Product, Category & Brand Management
    Route::apiResource('products', AdminProductController::class);
    Route::apiResource('categories', AdminCategoryController::class);
    Route::apiResource('brands', AdminBrandController::class);
});
