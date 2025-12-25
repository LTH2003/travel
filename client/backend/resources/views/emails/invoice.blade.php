<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #2c3e50;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f9f9f9;
            padding: 20px;
            border: 1px solid #ddd;
        }
        .section {
            margin-bottom: 20px;
        }
        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #2c3e50;
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        table th {
            background-color: #3498db;
            color: white;
            padding: 10px;
            text-align: left;
        }
        table td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
        .total-row {
            background-color: #ecf0f1;
            font-weight: bold;
            font-size: 16px;
        }
        .footer {
            background-color: #2c3e50;
            color: white;
            padding: 15px;
            text-align: center;
            border-radius: 0 0 5px 5px;
            font-size: 12px;
        }
        .label {
            font-weight: bold;
            color: #2c3e50;
            width: 150px;
            display: inline-block;
        }
        .info-line {
            margin-bottom: 8px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🏨 HÓA ĐƠN THANH TOÁN</h1>
            <p>TravelVN - Hệ Thống Quản Lý Tour</p>
        </div>

        <div class="content">
            <!-- Thông tin khách hàng -->
            <div class="section">
                <div class="section-title">📋 Thông Tin Khách Hàng</div>
                <div class="info-line">
                    <span class="label">Tên:</span>
                    {{ $customer->name ?? 'N/A' }}
                </div>
                <div class="info-line">
                    <span class="label">Email:</span>
                    {{ $customer->email ?? 'N/A' }}
                </div>
                <div class="info-line">
                    <span class="label">Điện thoại:</span>
                    {{ $customer->phone ?? 'N/A' }}
                </div>
            </div>

            <!-- Thông tin đơn hàng -->
            <div class="section">
                <div class="section-title">📝 Thông Tin Đơn Hàng</div>
                <div class="info-line">
                    <span class="label">Mã đơn hàng:</span>
                    {{ $order->order_code }}
                </div>
                <div class="info-line">
                    <span class="label">Ngày check-in:</span>
                    {{ $order->checked_in_at ? $order->checked_in_at->format('H:i d/m/Y') : 'Chưa check-in' }}
                </div>
                <div class="info-line">
                    <span class="label">Trạng thái:</span>
                    @if($order->status === 'completed')
                        <span style="color: green;">✅ Hoàn tất</span>
                    @elseif($order->status === 'checked_in')
                        <span style="color: blue;">🔵 Đã check-in</span>
                    @else
                        <span style="color: orange;">⏳ {{ ucfirst($order->status) }}</span>
                    @endif
                </div>
            </div>

            <!-- Chi tiết đặt phòng -->
            <div class="section">
                <div class="section-title">🏠 Chi Tiết Đặt Phòng</div>
                <table>
                    <thead>
                        <tr>
                            <th>Phòng</th>
                            <th>Check-in</th>
                            <th>Check-out</th>
                            <th>Số Đêm</th>
                            <th style="text-align: right;">Giá</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($order->booking_details) && is_array($order->booking_details))
                            @foreach($order->booking_details as $booking)
                                <tr>
                                    <td>{{ $booking['room_number'] ?? $booking['room_name'] ?? 'N/A' }}</td>
                                    <td>{{ $booking['check_in'] ?? 'N/A' }}</td>
                                    <td>{{ $booking['check_out'] ?? 'N/A' }}</td>
                                    <td>{{ $booking['nights'] ?? 'N/A' }}</td>
                                    <td style="text-align: right;">
                                        {{ number_format($booking['price'] ?? 0, 0) }} ₫
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="5" style="text-align: center; color: #999;">Không có chi tiết đặt phòng</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <!-- Tổng cộng -->
            <div class="section">
                <table>
                    <tbody>
                        <tr>
                            <td style="text-align: right; font-size: 18px; font-weight: bold; padding: 15px;">
                                💰 TỔNG TIỀN:
                            </td>
                            <td style="text-align: right; font-size: 18px; font-weight: bold; color: #e74c3c; padding: 15px;">
                                {{ number_format($order->total_amount ?? 0, 0) }} ₫
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Ghi chú -->
            <div class="section">
                <div class="section-title">📌 Ghi Chú</div>
                <p style="color: #666; font-style: italic;">
                    Cảm ơn quý khách đã sử dụng dịch vụ của chúng tôi. 
                    Nếu có bất kỳ câu hỏi nào, vui lòng liên hệ với chúng tôi.
                </p>
            </div>
        </div>

        <div class="footer">
            <p>TravelVN Hotel Management System © 2025</p>
            <p>Hotline: 1900-XXXX | Email: support@travelvn.com</p>
            <p style="margin-top: 10px; font-size: 11px; opacity: 0.8;">
                Hóa đơn này đã được tự động tạo bởi hệ thống. Vui lòng đính kèm file PDF để giữ lại bản gốc.
            </p>
        </div>
    </div>
</body>
</html>
