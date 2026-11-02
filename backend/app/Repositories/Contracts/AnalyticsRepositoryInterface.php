<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;

interface AnalyticsRepositoryInterface
{
    /**
     * Tổng hợp các chỉ số KPI doanh thu, đơn hàng, khách hàng
     */
    public function getSummaryMetrics(): array;

    /**
     * Thống kê doanh thu và đơn hàng theo từng tháng
     */
    public function getMonthlyRevenueStats(int $months = 6): Collection;

    /**
     * Lấy danh sách đơn hàng mới nhất
     */
    public function getRecentOrders(int $limit = 8): Collection;
}

