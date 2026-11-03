<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\AnalyticsRepositoryInterface;

class DashboardService
{
    public function __construct(
        protected AnalyticsRepositoryInterface $analyticsRepository
    ) {}

    /**
     * Aggregate all dashboard KPIs, dynamic revenue chart, and filtered recent orders.
     */
    public function getDashboardMetrics(array $filters = []): array
    {
        $period = (string) ($filters['period'] ?? '30days');
        $fromDate = $filters['from_date'] ?? null;
        $toDate = $filters['to_date'] ?? null;
        $orderStatus = $filters['order_status'] ?? null;

        // Auto calculate dates from predefined period if not manually provided
        if (!$fromDate || !$toDate) {
            match ($period) {
                'today' => [
                    $fromDate = now()->toDateString(),
                    $toDate = now()->toDateString(),
                ],
                '7days' => [
                    $fromDate = now()->subDays(7)->toDateString(),
                    $toDate = now()->toDateString(),
                ],
                '30days' => [
                    $fromDate = now()->subDays(30)->toDateString(),
                    $toDate = now()->toDateString(),
                ],
                'this_month' => [
                    $fromDate = now()->startOfMonth()->toDateString(),
                    $toDate = now()->endOfMonth()->toDateString(),
                ],
                'this_year' => [
                    $fromDate = now()->startOfYear()->toDateString(),
                    $toDate = now()->endOfYear()->toDateString(),
                ],
                default => null,
            };
        }

        return [
            'summary'       => $this->analyticsRepository->getSummaryMetrics($fromDate, $toDate),
            'revenue_chart' => $this->analyticsRepository->getRevenueChartData($period, $fromDate, $toDate),
            'recent_orders' => $this->analyticsRepository->getRecentOrders(10, $orderStatus),
            'filters'       => [
                'period'       => $period,
                'from_date'    => $fromDate,
                'to_date'      => $toDate,
                'order_status' => $orderStatus,
            ],
        ];
    }
}
