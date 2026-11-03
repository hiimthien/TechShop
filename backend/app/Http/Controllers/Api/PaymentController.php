<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\NotificationService;
use App\Services\VnpayService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function __construct(
        protected VnpayService $vnpayService
    ) {}

    /**
     * Khởi tạo liên kết thanh toán VNPay Sandbox cho đơn hàng
     */
    public function createVnpayUrl(Request $request): JsonResponse
    {
        $request->validate([
            'order_id' => 'required|integer|exists:orders,id',
        ]);

        $order = Order::where('id', $request->input('order_id'))
            ->where('user_id', Auth::id())
            ->first();

        if (!$order) {
            return $this->errorResponse('Không tìm thấy đơn hàng hoặc bạn không có quyền truy cập.', 404);
        }

        if ($order->payment_status === 'paid') {
            return $this->errorResponse('Đơn hàng này đã được thanh toán trước đó.', 422);
        }

        if ($order->status === 'cancelled') {
            return $this->errorResponse('Đơn hàng này đã bị hủy, không thể tiếp tục thanh toán.', 422);
        }

        $paymentUrl = $this->vnpayService->createPaymentUrl($order, $request->ip() ?? '127.0.0.1');

        return $this->successResponse([
            'payment_url' => $paymentUrl,
            'order_id'    => $order->id,
            'total_price' => (float) $order->total_price,
        ], 'Khởi tạo cổng thanh toán thành công.');
    }

    /**
     * Xử lý khi khách hàng hoàn tất giao dịch và VNPay redirect về lại
     */
    public function handleVnpayReturn(Request $request): JsonResponse
    {
        $params = $request->all();
        $result = $this->vnpayService->verifyCallback($params);

        if (!$result['is_valid']) {
            return $this->errorResponse($result['message'], 400);
        }

        $order = Order::with('user')->find($result['order_id']);

        if (!$order) {
            return $this->errorResponse('Không tìm thấy thông tin đơn hàng.', 404);
        }

        if ($result['is_success']) {
            // Idempotent update: chỉ cập nhật nếu chưa ghi nhận paid
            if ($order->payment_status !== 'paid') {
                DB::transaction(function () use ($order, $result) {
                    $order->update([
                        'payment_status'         => 'paid',
                        'payment_transaction_id' => $result['transaction_no'],
                        'status'                 => 'processing', // Chuyển sang chuẩn bị hàng ngay khi có tiền
                    ]);
                });

                $orderCode = '#TS-' . str_pad((string) $order->id, 6, '0', STR_PAD_LEFT);

                // Báo thông báo cho Quản trị viên
                try {
                    NotificationService::sendToAdmins('payment_received', [
                        'title'        => 'Thanh toán online thành công ' . $orderCode,
                        'message'      => 'Đơn hàng ' . $orderCode . ' đã thanh toán thành công qua VNPay (Mã GD: ' . $result['transaction_no'] . ')',
                        'category'     => 'order',
                        'link'         => '/admin/orders',
                        'icon'         => 'paid',
                        'reference_id' => $order->id,
                    ]);
                } catch (\Throwable $e) {
                    Log::warning('Lỗi gửi thông báo admin khi thanh toán vnpay: ' . $e->getMessage());
                }

                // Báo thông báo cho Khách hàng
                try {
                    if ($order->user) {
                        NotificationService::sendToUser($order->user, 'payment_success', [
                            'title'        => 'Thanh toán thành công ' . $orderCode,
                            'message'      => 'Đơn hàng ' . $orderCode . ' của bạn đã được thanh toán thành công qua VNPay. TechShop đang chuẩn bị hàng cho bạn.',
                            'category'     => 'order',
                            'link'         => '/account',
                            'icon'         => 'verified',
                            'reference_id' => $order->id,
                        ]);
                    }
                } catch (\Throwable $e) {
                    Log::warning('Lỗi gửi thông báo khách khi thanh toán vnpay: ' . $e->getMessage());
                }
            }

            return $this->successResponse([
                'order_id'       => $order->id,
                'order_code'     => '#TS-' . str_pad((string) $order->id, 6, '0', STR_PAD_LEFT),
                'transaction_no' => $result['transaction_no'],
                'amount'         => (float) $order->total_price,
            ], 'Thanh toán đơn hàng thành công!');
        } else {
            // Cập nhật trạng thái thất bại nếu chưa thanh toán
            if ($order->payment_status !== 'paid') {
                $order->update(['payment_status' => 'failed']);
            }

            return $this->errorResponse('Giao dịch thanh toán không thành công hoặc bị hủy.', 400, [
                'order_id'      => $order->id,
                'response_code' => $result['response_code'],
            ]);
        }
    }

    /**
     * Webhook IPN nhận trực tiếp từ VNPay Server-to-Server
     */
    public function handleVnpayIpn(Request $request): JsonResponse
    {
        $params = $request->all();
        $result = $this->vnpayService->verifyCallback($params);

        if (!$result['is_valid']) {
            return response()->json(['RspCode' => '97', 'Message' => 'Invalid Checksum']);
        }

        $order = Order::find($result['order_id']);

        if (!$order) {
            return response()->json(['RspCode' => '01', 'Message' => 'Order Not Found']);
        }

        // Kiểm tra số tiền khớp
        $vnpAmount = (float) ($params['vnp_Amount'] ?? 0) / 100;
        if (abs($vnpAmount - (float) $order->total_price) > 1.0) {
            return response()->json(['RspCode' => '04', 'Message' => 'Invalid Amount']);
        }

        // Idempotent: Nếu đơn đã được xác nhận thanh toán rồi
        if ($order->payment_status === 'paid') {
            return response()->json(['RspCode' => '02', 'Message' => 'Order already confirmed']);
        }

        if ($result['is_success']) {
            $order->update([
                'payment_status'         => 'paid',
                'payment_transaction_id' => $result['transaction_no'],
                'status'                 => 'processing',
            ]);
        } else {
            $order->update(['payment_status' => 'failed']);
        }

        return response()->json(['RspCode' => '00', 'Message' => 'Confirm Success']);
    }
}

