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
    public function getSummaryMetrics(?string $fromDate = null, ?string $toDate = null): array
    {
        $orderQuery = Order::query();
        if ($fromDate) {
            $orderQuery->whereDate('created_at', '>=', $fromDate);
        }
        if ($toDate) {
            $orderQuery->whereDate('created_at', '<=', $toDate);
        }

        $periodOrdersCount = (clone $orderQuery)->count();
        $periodRevenue = (float) (clone $orderQuery)->where('status', '!=', 'cancelled')->sum('total_price');

        return [
            'total_revenue'       => $periodRevenue,
            'all_time_revenue'    => (float) Order::where('status', '!=', 'cancelled')->sum('total_price'),
            'total_orders'        => $periodOrdersCount,
            'all_time_orders'     => Order::count(),
            'pending_orders'      => Order::where('status', 'pending')->count(),
            'processing_orders'   => Order::where('status', 'processing')->count(),
            'shipped_orders'      => Order::where('status', 'shipped')->count(),
            'delivered_orders'    => Order::where('status', 'delivered')->count(),
            'cancelled_orders'    => Order::where('status', 'cancelled')->count(),
            'average_order_value' => $periodOrdersCount > 0 ? round($periodRevenue / $periodOrdersCount, 0) : 0,
            'total_customers'     => User::where('role', 'customer')->orWhereNull('role')->count(),
            'total_products'      => Product::count(),
            'total_coupons'       => Coupon::where('is_active', true)->count(),
            'pending_warranties'  => WarrantyClaim::where('status', 'pending')->count(),
            'total_warranties'    => WarrantyClaim::count(),
        ];
    }

    public function getRevenueChartData(string $period = '30days', ?string $fromDate = null, ?string $toDate = null): Collection
    {
        $isDaily = in_array($period, ['7days', '30days', 'today']);
        $driver = DB::connection()->getDriverName();

        if ($driver === 'sqlite') {
            $dateFormat = $isDaily ? "%d/%m" : "%m/%Y";
            $rawDate = "strftime('{$dateFormat}', created_at) as month";
        } else {
            $dateFormat = $isDaily ? "%d/%m" : "%m/%Y";
            $rawDate = "DATE_FORMAT(created_at, '{$dateFormat}') as month";
        }

        $query = Order::select(
            DB::raw($rawDate),
            DB::raw("SUM(CASE WHEN status != 'cancelled' THEN total_price ELSE 0 END) as revenue"),
            DB::raw("COUNT(id) as order_count")
        );

        if ($fromDate && $toDate) {
            $query->whereDate('created_at', '>=', $fromDate)
                  ->whereDate('created_at', '<=', $toDate);
        } else {
            match ($period) {
                'today' => $query->whereDate('created_at', now()->toDateString()),
                '7days' => $query->where('created_at', '>=', now()->subDays(7)),
                '30days' => $query->where('created_at', '>=', now()->subDays(30)),
                'this_month' => $query->where('created_at', '>=', now()->startOfMonth()),
                'this_year' => $query->where('created_at', '>=', now()->startOfYear()),
                default => $query->where('created_at', '>=', now()->subMonths(6)),
            };
        }

        return $query->groupBy('month')
            ->orderBy(DB::raw("MIN(created_at)"), 'asc')
            ->get();
    }

    public function getMonthlyRevenueStats(int $months = 6): Collection
    {
        return $this->getRevenueChartData('all');
    }

    public function getRecentOrders(int $limit = 8, ?string $status = null): Collection
    {
        $query = Order::with(['user:id,name,email', 'address']);

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        return $query->latest()
            ->take($limit)
            ->get();
    }
}

