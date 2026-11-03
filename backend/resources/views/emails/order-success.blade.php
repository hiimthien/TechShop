<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác nhận đơn hàng</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f6f8fb;
            margin: 0;
            padding: 24px;
            color: #1e293b;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.06);
            border: 1px solid #e2e8f0;
        }
        .header {
            background: linear-gradient(135deg, #630ed4 0%, #8736f5 100%);
            padding: 32px 24px;
            text-align: center;
            color: #ffffff;
        }
        .header h1 {
            margin: 0 0 8px 0;
            font-size: 24px;
            font-weight: 800;
            letter-spacing: -0.5px;
        }
        .header p {
            margin: 0;
            font-size: 14px;
            opacity: 0.9;
        }
        .content {
            padding: 24px;
        }
        .order-badge {
            display: inline-block;
            background-color: #f3e8ff;
            color: #630ed4;
            font-weight: 700;
            font-size: 13px;
            padding: 6px 14px;
            border-radius: 9999px;
            margin-bottom: 20px;
        }
        .section-title {
            font-size: 15px;
            font-weight: 700;
            color: #0f172a;
            border-bottom: 2px solid #f1f5f9;
            padding-bottom: 8px;
            margin-top: 20px;
            margin-bottom: 12px;
        }
        .info-grid {
            background-color: #f8fafc;
            border-radius: 12px;
            padding: 14px 16px;
            font-size: 13px;
            line-height: 1.6;
            margin-bottom: 20px;
        }
        .item-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
            margin-top: 10px;
        }
        .item-table th {
            text-align: left;
            padding: 10px 8px;
            background-color: #f8fafc;
            color: #64748b;
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
        }
        .item-table td {
            padding: 12px 8px;
            border-bottom: 1px solid #f1f5f9;
        }
        .total-box {
            background-color: #faf5ff;
            border: 1px solid #e9d5ff;
            border-radius: 12px;
            padding: 16px;
            margin-top: 20px;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            margin-bottom: 6px;
        }
        .total-final {
            display: flex;
            justify-content: space-between;
            font-size: 16px;
            font-weight: 800;
            color: #630ed4;
            padding-top: 8px;
            border-top: 1px dashed #d8b4fe;
            margin-top: 8px;
        }
        .footer {
            background-color: #f8fafc;
            padding: 20px 24px;
            text-align: center;
            font-size: 12px;
            color: #64748b;
            border-top: 1px solid #e2e8f0;
        }
        .footer a {
            color: #630ed4;
            text-decoration: none;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>TechShop</h1>
            <p>Xác nhận đơn hàng thành công</p>
        </div>

        <!-- Body -->
        <div class="content">
            <div style="text-align: center;">
                <span class="order-badge">MÃ ĐƠN HÀNG: #{{ str_pad((string)$order->id, 6, '0', STR_PAD_LEFT) }}</span>
            </div>

            <p style="font-size: 14px; margin-top: 0;">
                Xin chào <strong>{{ $order->user?->name ?? 'Quý khách' }}</strong>,<br>
                Cảm ơn bạn đã tin tưởng mua sắm tại <strong>TechShop</strong>! Đơn hàng của bạn đã được tiếp nhận và nhân viên cửa hàng đang tiến hành đóng gói chuẩn bị giao cho đơn vị vận chuyển.
            </p>

            <!-- Thông tin giao hàng -->
            <div class="section-title">📍 Thông tin nhận hàng</div>
            <div class="info-grid">
                <div><strong>Người nhận:</strong> {{ $order->address?->name ?? $order->user?->name }}</div>
                <div><strong>Số điện thoại:</strong> {{ $order->address?->phone ?? $order->user?->phone ?? 'Chưa cập nhật' }}</div>
                <div><strong>Địa chỉ:</strong> {{ $order->address?->address }}, {{ $order->address?->city ?? '' }}</div>
                <div><strong>Thời gian đặt hàng:</strong> {{ $order->created_at?->format('d/m/Y H:i') }}</div>
            </div>

            <!-- Danh sách sản phẩm -->
            <div class="section-title">🛍️ Chi tiết đơn hàng</div>
            <table class="item-table">
                <thead>
                    <tr>
                        <th>Sản phẩm</th>
                        <th style="text-align: center;">SL</th>
                        <th style="text-align: right;">Đơn giá</th>
                        <th style="text-align: right;">Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                        <tr>
                            <td>
                                <strong>{{ $item->variant?->product?->name ?? 'Sản phẩm' }}</strong>
                                @if($item->variant?->sku)
                                    <br><span style="font-size: 11px; color: #64748b;">Phiên bản: {{ $item->variant->sku }}</span>
                                @endif
                            </td>
                            <td style="text-align: center;">{{ $item->quantity }}</td>
                            <td style="text-align: right;">{{ number_format($item->price, 0, ',', '.') }}đ</td>
                            <td style="text-align: right; font-weight: 600;">{{ number_format($item->price * $item->quantity, 0, ',', '.') }}đ</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Tổng kết thanh toán -->
            <div class="total-box">
                <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                    <tr>
                        <td style="padding: 4px 0; color: #475569;">Tạm tính:</td>
                        <td style="padding: 4px 0; text-align: right; font-weight: 600;">{{ number_format($order->total_price, 0, ',', '.') }}đ</td>
                    </tr>
                    <tr>
                        <td style="padding: 4px 0; color: #475569;">Phí vận chuyển:</td>
                        <td style="padding: 4px 0; text-align: right; font-weight: 600;">{{ number_format($order->shipping_fee, 0, ',', '.') }}đ</td>
                    </tr>
                    @if($order->discount_amount && $order->discount_amount > 0)
                        <tr>
                            <td style="padding: 4px 0; color: #16a34a;">Giảm giá khuyến mãi:</td>
                            <td style="padding: 4px 0; text-align: right; font-weight: 600; color: #16a34a;">-{{ number_format($order->discount_amount, 0, ',', '.') }}đ</td>
                        </tr>
                    @endif
                    <tr>
                        <td style="padding: 8px 0 0 0; font-size: 16px; font-weight: 800; color: #630ed4; border-top: 1px dashed #d8b4fe;">Tổng thanh toán:</td>
                        <td style="padding: 8px 0 0 0; text-align: right; font-size: 16px; font-weight: 800; color: #630ed4; border-top: 1px dashed #d8b4fe;">
                            {{ number_format($order->total_price + $order->shipping_fee - ($order->discount_amount ?? 0), 0, ',', '.') }}đ
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p style="margin: 0 0 8px 0;">Mọi thắc mắc xin vui lòng liên hệ Hotline: <strong>1800 2097</strong> (Miễn phí) hoặc email <a href="mailto:techshop.support@gmail.com">techshop.support@gmail.com</a>.</p>
            <p style="margin: 0;">© {{ date('Y') }} TechShop. Hệ thống bán lẻ thiết bị di động & công nghệ chính hãng.</p>
        </div>
    </div>
</body>
</html>

