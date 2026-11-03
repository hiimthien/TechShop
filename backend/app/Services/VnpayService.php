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

        $inputData = [
            'vnp_Version'    => '2.1.0',
            'vnp_TmnCode'    => $this->tmnCode,
            'vnp_Amount'     => $vnp_Amount,
            'vnp_Command'    => 'pay',
            'vnp_CreateDate' => date('YmdHis'),
            'vnp_CurrCode'   => 'VND',
            'vnp_IpAddr'     => $vnp_IpAddr,
            'vnp_Locale'     => $vnp_Locale,
            'vnp_OrderInfo'  => $vnp_OrderInfo,
            'vnp_OrderType'  => $vnp_OrderType,
            'vnp_ReturnUrl'  => $this->returnUrl,
            'vnp_TxnRef'     => $vnp_TxnRef,
            'vnp_ExpireDate' => date('YmdHis', strtotime('+30 minutes')),
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
        $vnp_SecureHash = $params['vnp_SecureHash'] ?? '';
        unset($params['vnp_SecureHash'], $params['vnp_SecureHashType']);

        ksort($params);
        $i = 0;
        $hashData = '';

        foreach ($params as $key => $value) {
            if ($i == 1) {
                $hashData .= '&' . urlencode($key) . '=' . urlencode((string) $value);
            } else {
                $hashData .= urlencode($key) . '=' . urlencode((string) $value);
                $i = 1;
            }
        }

        $secureHash = hash_hmac('sha512', $hashData, $this->hashSecret);
        $isValid = hash_equals(strtolower($vnp_SecureHash), strtolower($secureHash));

        if (!$isValid) {
            Log::warning('[VnpayService] Chữ ký VNPay không hợp lệ (Invalid Checksum). Params: ' . json_encode($params));
            return [
                'is_valid'       => false,
                'order_id'       => (int) ($params['vnp_TxnRef'] ?? 0),
                'response_code'  => $params['vnp_ResponseCode'] ?? '97',
                'is_success'     => false,
                'transaction_no' => null,
                'message'        => 'Chữ ký số xác thực giao dịch không hợp lệ.',
            ];
        }

        $responseCode = $params['vnp_ResponseCode'] ?? '';
        $isSuccess = ($responseCode === '00');
        $orderId = (int) ($params['vnp_TxnRef'] ?? 0);
        $transactionNo = $params['vnp_TransactionNo'] ?? null;

        return [
            'is_valid'       => true,
            'order_id'       => $orderId,
            'response_code'  => $responseCode,
            'is_success'     => $isSuccess,
            'transaction_no' => $transactionNo,
            'message'        => $isSuccess ? 'Giao dịch thanh toán thành công.' : 'Giao dịch thanh toán không thành công hoặc bị hủy.',
        ];
    }
}

