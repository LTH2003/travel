<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Danh Sách Booking</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', 'Liberation Sans', sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.4;
        }
        
        .container {
            width: 100%;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        
        .header h1 {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
            color: #0066cc;
        }
        
        .header p {
            font-size: 11px;
            color: #666;
        }
        
        .filter-info {
            background-color: #f5f5f5;
            padding: 8px;
            margin-bottom: 15px;
            border-radius: 3px;
            font-size: 11px;
        }
        
        .filter-info p {
            margin-bottom: 3px;
        }
        
        .statistics {
            display: table;
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        
        .stat-box {
            display: table-cell;
            width: 25%;
            padding: 10px;
            border: 1px solid #ddd;
            text-align: center;
            background-color: #f9f9f9;
        }
        
        .stat-value {
            font-size: 16px;
            font-weight: bold;
            color: #0066cc;
        }
        
        .stat-label {
            font-size: 10px;
            color: #666;
            margin-top: 3px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        
        table thead {
            background-color: #0066cc;
            color: white;
            font-weight: bold;
        }
        
        table th {
            padding: 8px;
            text-align: left;
            border: 1px solid #ddd;
            font-size: 11px;
        }
        
        table td {
            padding: 8px;
            border: 1px solid #ddd;
            font-size: 11px;
        }
        
        table tbody tr:nth-child(odd) {
            background-color: #f9f9f9;
        }
        
        table tbody tr:nth-child(even) {
            background-color: #fff;
        }
        
        .status-completed {
            background-color: #d4edda;
            color: #155724;
            padding: 3px 6px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
            display: inline-block;
        }
        
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
            padding: 3px 6px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
            display: inline-block;
        }
        
        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
        
        .summary {
            background-color: #f0f0f0;
            padding: 10px;
            border-radius: 3px;
            margin-bottom: 15px;
            font-size: 11px;
        }
        
        .summary p {
            margin-bottom: 3px;
        }
        
        .currency {
            text-align: right;
            font-weight: bold;
            color: #d9534f;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>DANH SÁCH BOOKING KHÁCH HÀNG</h1>
            <p>Xuất lúc: {{ $export_date }}</p>
        </div>
        
        <!-- Filter Info -->
        @if($search || $status)
            <div class="filter-info">
                <p><strong>Bộ lọc áp dụng:</strong></p>
                @if($search)
                    <p>• Tìm kiếm: <strong>{{ $search }}</strong></p>
                @endif
                @if($status)
                    <p>• Trạng thái: <strong>{{ $status === 'completed' ? 'Hoàn thành' : 'Đang chờ' }}</strong></p>
                @endif
            </div>
        @endif
        
        <!-- Statistics -->
        <div class="statistics">
            <div class="stat-box">
                <div class="stat-value">{{ $stats['total_count'] }}</div>
                <div class="stat-label">Tổng Booking</div>
            </div>
            <div class="stat-box">
                <div class="stat-value">{{ $stats['completed_count'] }}</div>
                <div class="stat-label">Hoàn Thành</div>
            </div>
            <div class="stat-box">
                <div class="stat-value">{{ $stats['pending_count'] }}</div>
                <div class="stat-label">Đang Chờ</div>
            </div>
            <div class="stat-box">
                <div class="stat-value" style="color: #27ae60;">{{ number_format($stats['total_revenue'], 0, ',', '.') }}đ</div>
                <div class="stat-label">Doanh Thu</div>
            </div>
        </div>
        
        <!-- Bookings Table -->
        @if($bookings->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th style="width: 5%;">STT</th>
                        <th style="width: 12%;">Mã Booking</th>
                        <th style="width: 18%;">Tên Khách Hàng</th>
                        <th style="width: 14%;">Điện Thoại</th>
                        <th style="width: 10%;">Ngày Tạo</th>
                        <th style="width: 13%;">Tổng Tiền</th>
                        <th style="width: 14%;">Trạng Thái</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bookings as $index => $booking)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td><strong>{{ $booking->order_code }}</strong></td>
                            <td>{{ $booking->user->name ?? 'N/A' }}</td>
                            <td>{{ $booking->user->phone ?? 'N/A' }}</td>
                            <td>{{ $booking->created_at->format('d/m/Y') }}</td>
                            <td class="currency">{{ number_format($booking->total_amount, 0, ',', '.') }}đ</td>
                            <td>
                                @if($booking->status === 'completed')
                                    <span class="status-completed">✓ Hoàn Thành</span>
                                @else
                                    <span class="status-pending">⟳ Đang Chờ</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div style="text-align: center; padding: 30px; color: #999; font-style: italic;">
                Không có booking nào phù hợp với bộ lọc
            </div>
        @endif
        
        <!-- Summary -->
        @if($bookings->count() > 0)
            <div class="summary">
                <p><strong>Tóm Tắt:</strong></p>
                <p>• Tổng số booking: <strong>{{ $stats['total_count'] }}</strong></p>
                <p>• Booking hoàn thành: <strong>{{ $stats['completed_count'] }}</strong></p>
                <p>• Booking đang chờ: <strong>{{ $stats['pending_count'] }}</strong></p>
                <p>• Tổng doanh thu: <strong style="color: #27ae60;">{{ number_format($stats['total_revenue'], 0, ',', '.') }}đ</strong></p>
                <p>• Doanh thu trung bình: <strong style="color: #27ae60;">{{ $stats['completed_count'] > 0 ? number_format($stats['total_revenue'] / $stats['completed_count'], 0, ',', '.') : 0 }}đ</strong></p>
            </div>
        @endif
        
        <!-- Footer -->
        <div class="footer">
            <p>Báo cáo được tạo tự động bởi Hệ Thống Quản Lý Booking</p>
            <p>Ngày xuất: {{ now()->format('d/m/Y H:i:s') }}</p>
        </div>
    </div>
</body>
</html>
