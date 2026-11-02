<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\OrderRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(
        protected OrderRepositoryInterface $orderRepository
    ) {}

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['status', 'search']);
        $perPage = (int) $request->input('per_page', 15);

        $orders = $this->orderRepository->getPaginated($filters, $perPage);

        return response()->json([
            'status' => true,
            'data' => $orders,
        ]);
    }

    public function show(string $id): JsonResponse
    {
        $order = $this->orderRepository->findById($id);

        if (!$order) {
            return response()->json(['status' => false, 'message' => 'Không tìm thấy đơn hàng'], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $order,
        ]);
    }

    public function updateStatus(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'status' => 'required|string|in:pending,processing,shipped,delivered,cancelled',
        ]);

        $order = $this->orderRepository->updateStatus($id, $request->status);

        return response()->json([
            'status' => true,
            'message' => 'Cập nhật trạng thái đơn hàng thành công',
            'data' => $order,
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $this->orderRepository->delete($id);

        return response()->json([
            'status' => true,
            'message' => 'Đã xóa đơn hàng thành công',
        ]);
    }
}
