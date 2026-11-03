<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;

interface AnalyticsRepositoryInterface
{
    /**
     * Tổng hợp các chỉ số KPI doanh thu, đơn hàng, khách hàng có hỗ trợ lọc thời gian
     */
    public function getSummaryMetrics(?string $fromDate = null, ?string $toDate = null): array;

    /**
     * Thống kê biểu đồ doanh thu theo ngày hoặc tháng tùy theo khoảng thời gian
     */
    public function getRevenueChartData(string $period = '30days', ?string $fromDate = null, ?string $toDate = null): Collection;

    /**
     * Thống kê doanh thu theo tháng (tương thích ngược)
     */
    public function getMonthlyRevenueStats(int $months = 6): Collection;

    /**
     * Lấy danh sách đơn hàng gần đây có hỗ trợ lọc theo trạng thái
     */
    public function getRecentOrders(int $limit = 8, ?string $status = null): Collection;
}

