<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\WarrantyClaim;
use App\Repositories\Contracts\AnalyticsRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AnalyticsRepository implements AnalyticsRepositoryInterface
{
    public function getSummaryMetrics(): array
    {
        return [
            'total_revenue' => (float) Order::where('status', '!=', 'cancelled')->sum('total_price'),
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'processing_orders' => Order::where('status', 'processing')->count(),
            'shipped_orders' => Order::where('status', 'shipped')->count(),
            'delivered_orders' => Order::where('status', 'delivered')->count(),
            'cancelled_orders' => Order::where('status', 'cancelled')->count(),
            'total_customers' => User::where('role', 'customer')->orWhereNull('role')->count(),
            'total_products' => Product::count(),
            'total_coupons' => Coupon::where('is_active', true)->count(),
            'pending_warranties' => WarrantyClaim::where('status', 'pending')->count(),
            'total_warranties' => WarrantyClaim::count(),
        ];
    }

    public function getMonthlyRevenueStats(int $months = 6): Collection
    {
        return Order::select(
            DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
            DB::raw("SUM(CASE WHEN status != 'cancelled' THEN total_price ELSE 0 END) as revenue"),
            DB::raw("COUNT(id) as order_count")
        )
            ->where('created_at', '>=', now()->subMonths($months)->startOfMonth())
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();
    }

    public function getRecentOrders(int $limit = 8): Collection
    {
        return Order::with(['user:id,name,email', 'address'])
            ->latest()
            ->take($limit)
            ->get();
    }
}

