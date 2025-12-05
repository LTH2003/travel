<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background: #f9f9f9;
            border-radius: 8px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #2563eb;
        }
        .booking-info {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border: 2px solid #2563eb;
        }
        .booking-details {
            background: #f0f4ff;
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
        }
        .item-card {
            background: #fff;
            padding: 12px;
            margin: 10px 0;
            border-left: 4px solid #2563eb;
        }
        .qr-section {
            text-align: center;
            margin: 20px 0;
            padding: 20px;
            background: #f0f4ff;
            border-radius: 8px;
        }
        .qr-image {
            max-width: 200px;
            margin: 10px auto;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 15px;
        }
        .success-badge {
            display: inline-block;
            background: #10b981;
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: bold;
            margin: 10px 0;
        }
        .button {
            display: inline-block;
            background: #2563eb;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">✈️ TravelVN</div>
            <h2>Xác Nhận Đặt Tour / Khách Sạn</h2>
        </div>

        <div class="booking-info">
            <h3>🎉 Đặt Hàng Thành Công!</h3>
            <div class="success-badge">✓ Đã Xác Nhận</div>

            <div class="booking-details">
                <p><strong>Mã Đơn Hàng:</strong> {{ $order->order_code }}</p>
                <p><strong>Tên Khách Hàng:</strong> {{ $order->user->name }}</p>
                <p><strong>Email:</strong> {{ $order->user->email }}</p>
                <p><strong>Tổng Tiền:</strong> <span style="color: #d97706; font-weight: bold;">{{ number_format($order->total_amount) }} VNĐ</span></p>
                <p><strong>Ngày Xác Nhận:</strong> {{ $order->completed_at->format('d/m/Y H:i') }}</p>
            </div>

            <h4>📦 Chi Tiết Đặt Hàng:</h4>
            @foreach($bookingDetails as $detail)
                <div class="item-card">
                    <strong>{{ $detail['name'] }}</strong><br>
                    <small>Loại: {{ strtolower($detail['type']) === 'tour' ? '🎫 Tour' : '🏨 Khách Sạn' }}</small><br>
                    <small>Số Lượng: {{ $detail['quantity'] }} | Giá: {{ number_format($detail['price']) }} VNĐ</small>
                </div>
            @endforeach

            <div class="qr-section">
                <h4>📱 Mã QR Xác Nhận</h4>
                <p>Nhân viên sẽ quét mã QR này để xác nhận thông tin của bạn</p>
                <img src="cid:qr_code_{{ $order->order_code }}.png" alt="QR Code" class="qr-image">
                <p style="font-size: 12px; color: #666;">Mã đơn: {{ $order->order_code }}</p>
            </div>
        </div>

        <div style="text-align: center; margin: 20px 0;">
            <a href="http://localhost:5173/purchase-history" class="button">Xem Lịch Sử Mua</a>
            <a href="http://localhost:5173/profile" class="button" style="background: #6b7280;">Trang Cá Nhân</a>
        </div>

        <div class="footer">
            <p>Cảm ơn bạn đã sử dụng dịch vụ TravelVN!</p>
            <p>Hotline: 0889421997 | Email: huyhoahien86@gmail.com</p>
            <p>Hỗ trợ 24/7 - Đặt tour ngay!</p>
        </div>
    </div>
</body>
</html>
