<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Order;
use Exception;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class VnpayService
{
    protected string $tmnCode;
    protected string $hashSecret;
    protected string $vnpUrl;
    protected string $returnUrl;

    public function __construct()
    {
        $this->tmnCode = (string) Config::get('services.vnpay.tmn_code', 'DEMOVNPAY');
        $this->hashSecret = (string) Config::get('services.vnpay.hash_secret', 'SECRETKEYDEMOVNPAYFORTECHSHOP123');
        $this->vnpUrl = (string) Config::get('services.vnpay.url', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html');
        $this->returnUrl = (string) Config::get('services.vnpay.return_url', 'http://localhost:5173/checkout/vnpay-return');
    }

    /**
     * Khởi tạo URL chuyển hướng sang cổng thanh toán VNPay Sandbox
     */
    public function createPaymentUrl(Order $order, string $ipAddress): string
    {
        $vnp_TxnRef = (string) $order->id;
        $vnp_OrderInfo = 'Thanh toan don hang TechShop #' . str_pad((string) $order->id, 6, '0', STR_PAD_LEFT);
        $vnp_OrderType = 'billpayment';
        $vnp_Amount = (int) round((float) $order->total_price * 100);
        $vnp_Locale = 'vn';
        $vnp_BankCode = '';
        $vnp_IpAddr = $ipAddress ?: '127.0.0.1';

        // VNPay yêu cầu thời gian bắt buộc theo múi giờ Việt Nam (GMT+7)
        $now = now('Asia/Ho_Chi_Minh');
        $vnp_CreateDate = $now->format('YmdHis');
        $vnp_ExpireDate = $now->copy()->addMinutes(30)->format('YmdHis');

        $inputData = [
            'vnp_Version'    => '2.1.0',
            'vnp_TmnCode'    => $this->tmnCode,
            'vnp_Amount'     => $vnp_Amount,
            'vnp_Command'    => 'pay',
            'vnp_CreateDate' => $vnp_CreateDate,
            'vnp_CurrCode'   => 'VND',
            'vnp_IpAddr'     => $vnp_IpAddr,
            'vnp_Locale'     => $vnp_Locale,
            'vnp_OrderInfo'  => $vnp_OrderInfo,
            'vnp_OrderType'  => $vnp_OrderType,
            'vnp_ReturnUrl'  => $this->returnUrl,
            'vnp_TxnRef'     => $vnp_TxnRef,
            'vnp_ExpireDate' => $vnp_ExpireDate,
        ];

        if (!empty($vnp_BankCode)) {
            $inputData['vnp_BankCode'] = $vnp_BankCode;
        }

        ksort($inputData);
        $query = '';
        $i = 0;
        $hashdata = '';

        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . '=' . urlencode((string) $value);
            } else {
                $hashdata .= urlencode($key) . '=' . urlencode((string) $value);
                $i = 1;
            }
            $query .= urlencode($key) . '=' . urlencode((string) $value) . '&';
        }

        $vnpUrl = $this->vnpUrl . '?' . $query;
        $vnpSecureHash = hash_hmac('sha512', $hashdata, $this->hashSecret);
        $vnpUrl .= 'vnp_SecureHash=' . $vnpSecureHash;

        return $vnpUrl;
    }

    /**
     * Xác thực tính toàn vẹn của kết quả callback hoặc IPN từ VNPay (Idempotent & Secure)
     *
     * @return array{is_valid: bool, order_id: int, response_code: string, is_success: bool, transaction_no: string|null, message: string}
     */
    public function verifyCallback(array $params): array
    {
        $vnp_SecureHash = (string) ($params['vnp_SecureHash'] ?? '');

        // Chỉ lấy các trường bắt đầu bằng vnp_ theo chuẩn đặc tả VNPay v2.1.0
        $inputData = [];
        foreach ($params as $key => $value) {
            if (str_starts_with((string) $key, 'vnp_') && $key !== 'vnp_SecureHash' && $key !== 'vnp_SecureHashType') {
                $inputData[(string) $key] = (string) $value;
            }
        }

        ksort($inputData);
        $i = 0;
        $hashData = '';

        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData .= '&' . urlencode($key) . '=' . urlencode((string) $value);
            } else {
                $hashData .= urlencode($key) . '=' . urlencode((string) $value);
                $i = 1;
            }
        }

        $secureHash = hash_hmac('sha512', $hashData, $this->hashSecret);
        $isValid = !empty($vnp_SecureHash) && hash_equals(strtolower($vnp_SecureHash), strtolower($secureHash));

        if (!$isValid) {
            Log::warning('[VnpayService] Chữ ký VNPay không hợp lệ (Invalid Checksum). Params: ' . json_encode($params));
            return [
                'is_valid'       => false,
                'order_id'       => (int) ($params['vnp_TxnRef'] ?? 0),
                'response_code'  => (string) ($params['vnp_ResponseCode'] ?? '97'),
                'is_success'     => false,
                'transaction_no' => null,
                'message'        => 'Chữ ký số xác thực giao dịch không hợp lệ.',
            ];
        }

        $responseCode = (string) ($params['vnp_ResponseCode'] ?? '');
        $isSuccess = ($responseCode === '00');
        $orderId = (int) ($params['vnp_TxnRef'] ?? 0);
        $transactionNo = isset($params['vnp_TransactionNo']) ? (string) $params['vnp_TransactionNo'] : null;

        return [
            'is_valid'       => true,
            'order_id'       => $orderId,
            'response_code'  => $responseCode,
            'is_success'     => $isSuccess,
            'transaction_no' => $transactionNo,
            'message'        => $this->getResponseMessage($responseCode),
        ];
    }

    /**
     * Diễn giải mã phản hồi trả về từ cổng thanh toán VNPay
     */
    public function getResponseMessage(string $code): string
    {
        return match ($code) {
            '00' => 'Giao dịch thanh toán thành công.',
            '07' => 'Trừ tiền thành công. Giao dịch bị nghi ngờ (liên quan tới lừa đảo, giao dịch bất thường).',
            '09' => 'Thẻ/Tài khoản của quý khách chưa đăng ký dịch vụ InternetBanking tại ngân hàng.',
            '10' => 'Quý khách xác thực thông tin thẻ/tài khoản không đúng quá 3 lần.',
            '11' => 'Đã hết hạn chờ thanh toán. Vui lòng thực hiện lại giao dịch.',
            '12' => 'Thẻ/Tài khoản của quý khách bị khóa.',
            '13' => 'Quý khách nhập sai mật khẩu xác thực giao dịch (OTP).',
            '24' => 'Khách hàng đã hủy giao dịch.',
            '51' => 'Tài khoản của quý khách không đủ số dư để thực hiện giao dịch.',
            '65' => 'Tài khoản của quý khách đã vượt quá hạn mức giao dịch trong ngày.',
            '75' => 'Ngân hàng thanh toán đang bảo trì. Vui lòng thử lại sau.',
            '79' => 'Quý khách nhập sai mật khẩu thanh toán quá số lần quy định.',
            '97' => 'Chữ ký số không hợp lệ (Invalid Checksum).',
            default => 'Giao dịch thanh toán không thành công hoặc đã bị hủy.',
        };
    }
}

