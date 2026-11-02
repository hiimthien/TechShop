<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\AnalyticsRepositoryInterface;
use Illuminate\Support\Collection;

class DashboardService
{
    public function __construct(
        protected AnalyticsRepositoryInterface $analyticsRepository
    ) {}

    /**
     * Aggregate all dashboard KPIs, monthly stats, and recent orders.
     */
    public function getDashboardMetrics(): array
    {
        return [
            'summary' => $this->analyticsRepository->getSummaryMetrics(),
            'revenue_chart' => $this->analyticsRepository->getMonthlyRevenueStats(6),
            'recent_orders' => $this->analyticsRepository->getRecentOrders(8),
        ];
    }
}
