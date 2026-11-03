<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateOrderStatusRequest;
use App\Http\Requests\Admin\UpdateOrderSerialsRequest;
use App\Models\OrderItem;
use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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

        return $this->successResponse($orders);
    }

    public function show(string $id): JsonResponse
    {
        $order = $this->orderRepository->findById($id);

        if (!$order) {
            return $this->errorResponse('Không tìm thấy đơn hàng', 404);
        }

        return $this->successResponse($order);
    }

    public function updateStatus(UpdateOrderStatusRequest $request, string $id): JsonResponse
    {
        $order = $this->orderRepository->updateStatus($id, $request->status);

        if (!$order) {
            return $this->errorResponse('Không tìm thấy đơn hàng để cập nhật', 404);
        }

        // Gửi thông báo đến Khách hàng sở hữu đơn hàng
        try {
            if ($order->user) {
                $statusLabels = [
                    'pending'    => 'Chờ duyệt',
                    'processing' => 'Đang chuẩn bị hàng',
                    'shipped'    => 'Đang giao hàng',
                    'delivered'  => 'Giao hàng thành công',
                    'cancelled'  => 'Đã hủy',
                ];
                $label = $statusLabels[$request->status] ?? $request->status;
                $orderCode = '#TS-' . str_pad((string) $order->id, 6, '0', STR_PAD_LEFT);

                NotificationService::sendToUser($order->user, 'order_status_updated', [
                    'title'        => 'Đơn hàng ' . $orderCode . ' cập nhật trạng thái',
                    'message'      => 'Đơn hàng của bạn đã chuyển sang trạng thái: ' . $label . '.',
                    'category'     => 'order',
                    'link'         => '/account',
                    'icon'         => $request->status === 'delivered' ? 'task_alt' : ($request->status === 'cancelled' ? 'cancel' : 'local_shipping'),
                    'reference_id' => $order->id,
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('Lỗi khi gửi thông báo cập nhật đơn hàng: ' . $e->getMessage());
        }

        return $this->successResponse($order, 'Cập nhật trạng thái đơn hàng thành công.');
    }

    public function destroy(string $id): JsonResponse
    {
        $deleted = $this->orderRepository->delete($id);

        if (!$deleted) {
            return $this->errorResponse('Không thể xóa đơn hàng', 400);
        }

        return $this->successResponse(null, 'Đã xóa đơn hàng thành công.');
    }

    public function updateSerials(UpdateOrderSerialsRequest $request, string $id): JsonResponse
    {
        foreach ($request->validated()['items'] as $itemData) {
            OrderItem::where('id', $itemData['id'])
                ->where('order_id', $id)
                ->update([
                    'serial_number' => !empty($itemData['serial_number']) ? trim($itemData['serial_number']) : null,
                ]);
        }

        $order = $this->orderRepository->findById($id);

        return $this->successResponse($order, 'Đã cập nhật số Serial / IMEI thành công.');
    }
}
