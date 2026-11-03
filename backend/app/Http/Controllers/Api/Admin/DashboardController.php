<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(
        protected DashboardService $dashboardService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['period', 'from_date', 'to_date', 'order_status']);
        $data = $this->dashboardService->getDashboardMetrics($filters);

        return response()->json([
            'status' => true,
            'data' => $data,
        ]);
    }
}
