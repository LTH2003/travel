<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background: white;
        }
        .container {
            max-width: 210mm;
            height: 297mm;
            margin: 0 auto;
            padding: 20mm;
            background: white;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }
        .header h1 {
            font-size: 28px;
            margin-bottom: 5px;
            color: #2c3e50;
            font-family: DejaVu Sans, Arial, sans-serif;
        }
        .header p {
            font-size: 12px;
            color: #666;
            font-family: DejaVu Sans, Arial, sans-serif;
        }
        .invoice-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            font-size: 12px;
        }
        .info-block {
            width: 45%;
        }
        .info-block .label {
            font-weight: bold;
            color: #2c3e50;
        }
        .section-title {
            font-size: 13px;
            font-weight: bold;
            color: #2c3e50;
            background-color: #ecf0f1;
            padding: 8px;
            margin-top: 15px;
            margin-bottom: 10px;
            border-left: 3px solid #3498db;
            font-family: DejaVu Sans, Arial, sans-serif;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 12px;
            font-family: DejaVu Sans, Arial, sans-serif;
        }
        table th {
            background-color: #3498db;
            color: white;
            padding: 8px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #2980b9;
            font-family: DejaVu Sans, Arial, sans-serif;
        }
        table td {
            padding: 8px;
            border: 1px solid #ddd;
            font-family: DejaVu Sans, Arial, sans-serif;
        }
        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .text-right {
            text-align: right;
        }
        .total-section {
            margin-top: 20px;
            padding: 15px;
            background-color: #ecf0f1;
            border: 2px solid #3498db;
            text-align: right;
            font-family: DejaVu Sans, Arial, sans-serif;
        }
        .total-amount {
            font-size: 20px;
            font-weight: bold;
            color: #e74c3c;
            margin-top: 10px;
        }
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 11px;
            color: #666;
            font-family: DejaVu Sans, Arial, sans-serif;
        }
        .customer-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            font-size: 12px;
            font-family: DejaVu Sans, Arial, sans-serif;
        }
        .customer-block {
            width: 48%;
        }
        .customer-block .label {
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 3px;
        }
        .customer-block .value {
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>HÓA ĐƠN THANH TOÁN</h1>
            <p>TravelVN - Hệ Thống Quản Lý Tour</p>
        </div>

        <!-- Invoice Info -->
        <div class="invoice-info">
            <div class="info-block">
                <div><span class="label">Mã Hóa Đơn:</span> INV-{{ $order->order_code }}</div>
                <div><span class="label">Mã Đơn Hàng:</span> {{ $order->order_code }}</div>
                <div><span class="label">Ngày In:</span> {{ $invoiceDate }}</div>
            </div>
            <div class="info-block">
                <div><span class="label">Trạng Thái:</span> 
                    @if($status === 'completed')
                        ✅ Hoàn tất
                    @elseif($status === 'checked_in')
                        🔵 Đã check-in
                    @else
                        {{ ucfirst($status) }}
                    @endif
                </div>
                <div><span class="label">Check-in:</span> 
                    {{ $order->checked_in_at ? $order->checked_in_at->format('H:i d/m/Y') : 'Chưa check-in' }}
                </div>
            </div>
        </div>

        <!-- Customer Info -->
        <div class="section-title">THÔNG TIN KHÁCH HÀNG</div>
        <div class="customer-info">
            <div class="customer-block">
                <div class="label">Tên Khách:</div>
                <div class="value">{{ $customer->name ?? 'N/A' }}</div>
                
                <div class="label">Điện Thoại:</div>
                <div class="value">{{ $customer->phone ?? 'N/A' }}</div>
            </div>
            <div class="customer-block">
                <div class="label">Email:</div>
                <div class="value">{{ $customer->email ?? 'N/A' }}</div>
                
                <div class="label">Địa Chỉ:</div>
                <div class="value">{{ $customer->address ?? 'N/A' }}</div>
            </div>
        </div>

        <!-- Booking Details -->
        <div class="section-title">CHI TIẾT ĐẶT PHÒNG</div>
        <table>
            <thead>
                <tr>
                    <th>Phòng</th>
                    <th>Check-in</th>
                    <th>Check-out</th>
                    <th style="text-align: center;">Số Đêm</th>
                    <th style="text-align: right;">Giá/Đêm</th>
                    <th style="text-align: right;">Thành Tiền</th>
                </tr>
            </thead>
            <tbody>
                @if(is_array($bookingDetails) && count($bookingDetails) > 0)
                    @foreach($bookingDetails as $booking)
                        <tr>
                            <td>{{ $booking['room_number'] ?? $booking['room_name'] ?? 'N/A' }}</td>
                            <td>{{ $booking['check_in'] ?? 'N/A' }}</td>
                            <td>{{ $booking['check_out'] ?? 'N/A' }}</td>
                            <td style="text-align: center;">{{ $booking['nights'] ?? '1' }}</td>
                            <td style="text-align: right;">{{ number_format($booking['price_per_night'] ?? $booking['price'] ?? 0, 0) }} ₫</td>
                            <td style="text-align: right;">{{ number_format($booking['total_price'] ?? $booking['price'] ?? 0, 0) }} ₫</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="6" style="text-align: center; color: #999;">Không có chi tiết đặt phòng</td>
                    </tr>
                @endif
            </tbody>
        </table>

        <!-- Total Section -->
        <div class="total-section">
            <div>Tổng Tiền Dịch Vụ</div>
            <div class="total-amount">{{ number_format($totalAmount, 0) }} ₫</div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>TravelVN Hotel Management System</strong></p>
            <p>Hotline: 1900-XXXX | Email: support@travelvn.com | Website: www.travelvn.com</p>
            <p style="margin-top: 10px;">Cảm ơn quý khách đã tin tưởng và sử dụng dịch vụ của chúng tôi!</p>
            <p style="margin-top: 10px; font-size: 10px; opacity: 0.7;">
                Hóa đơn này được tự động tạo bởi hệ thống. Nếu có thắc mắc, vui lòng liên hệ bộ phận lễ tân.
            </p>
        </div>
    </div>
</body>
</html>
