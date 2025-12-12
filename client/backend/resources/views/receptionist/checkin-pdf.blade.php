<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Danh Sách Check-in</title>
    <style>
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            margin: 20px;
        }
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 10px;
        }
        .info {
            text-align: center;
            color: #666;
            margin-bottom: 20px;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table thead {
            background-color: #007bff;
            color: white;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        table tbody tr:hover {
            background-color: #f0f0f0;
        }
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 12px;
            color: #666;
        }
        .summary {
            margin-top: 20px;
            font-weight: bold;
            text-align: right;
        }
    </style>
</head>
<body>
    <h1>DANH SÁCH CHECK-IN KHÁCH HÀNG</h1>
    <div class="info">
        <p>Ngày: <strong>{{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}</strong></p>
        <p>Xuất lúc: <strong>{{ $exportedAt }}</strong></p>
    </div>

    @if($checkedInList->isEmpty())
        <p style="text-align: center; color: #999;">Không có dữ liệu check-in cho ngày này</p>
    @else
        <table>
            <thead>
                <tr>
                    <th style="width: 5%">STT</th>
                    <th style="width: 15%">Mã Đơn Hàng</th>
                    <th style="width: 30%">Tên Khách Hàng</th>
                    <th style="width: 20%">Số Điện Thoại</th>
                    <th style="width: 15%">Thời Gian Check-in</th>
                    <th style="width: 15%">Tổng Tiền</th>
                </tr>
            </thead>
            <tbody>
                @foreach($checkedInList as $index => $order)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td><strong>{{ $order->order_code }}</strong></td>
                    <td>{{ $order->user->name }}</td>
                    <td>{{ $order->user->phone ?? 'N/A' }}</td>
                    <td>{{ $order->checked_in_at->format('H:i d/m/Y') }}</td>
                    <td style="text-align: right;">{{ number_format($order->total_amount, 0, ',', '.') }} VND</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="summary">
            <p>Tổng số khách đã check-in: <strong>{{ $totalCount }} khách</strong></p>
            @if($totalCount > 0)
                <p>Tổng doanh thu: <strong>{{ number_format($checkedInList->sum('total_amount'), 0, ',', '.') }} VND</strong></p>
            @endif
        </div>
    @endif

    <div class="footer">
        <p>Được xuất từ hệ thống quản lý tour</p>
    </div>
</body>
</html>
