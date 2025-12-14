<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Báo Cáo Thanh Toán</title>
    <style>
        body {
            font-family: 'DejaVu Sans', 'Liberation Sans', sans-serif;
            color: #333;
            line-height: 1.6;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #0d6efd;
            padding-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            color: #0d6efd;
            font-size: 28px;
        }
        .header p {
            margin: 5px 0 0 0;
            color: #666;
            font-size: 14px;
        }
        .summary {
            display: flex;
            justify-content: space-between;
            margin: 30px 0;
            gap: 20px;
        }
        .summary-item {
            flex: 1;
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 4px;
            background-color: #f8f9fa;
        }
        .summary-item label {
            display: block;
            color: #666;
            font-size: 12px;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .summary-item .value {
            display: block;
            font-size: 20px;
            font-weight: bold;
            color: #0d6efd;
        }
        .section-title {
            background-color: #0d6efd;
            color: white;
            padding: 10px 15px;
            margin-top: 30px;
            margin-bottom: 15px;
            border-radius: 4px;
            font-size: 14px;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 12px;
        }
        th {
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
            font-weight: bold;
            color: #333;
        }
        td {
            border: 1px solid #ddd;
            padding: 10px;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .status-success {
            background-color: #d4edda;
            color: #155724;
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 11px;
        }
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 11px;
        }
        .status-failed {
            background-color: #f8d7da;
            color: #721c24;
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 11px;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            color: #666;
            font-size: 12px;
        }
        .method-badge {
            display: inline-block;
            background-color: #e7f3ff;
            color: #0d6efd;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 11px;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>📊 BÁO CÁO THANH TOÁN</h1>
        <p>Từ ngày {{ request('from_date', date('01/m/Y')) }} đến {{ request('to_date', date('d/m/Y')) }}</p>
        <p>Ngày tạo báo cáo: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>

    <!-- Summary Statistics -->
    <div class="summary">
        <div class="summary-item">
            <label>Tổng Doanh Thu</label>
            <span class="value">{{ number_format($totalAmount, 0, ',', '.') }}đ</span>
        </div>
        <div class="summary-item">
            <label>Giao Dịch Thành Công</label>
            <span class="value">{{ $successCount }}</span>
        </div>
        <div class="summary-item">
            <label>Giao Dịch Thất Bại</label>
            <span class="value">{{ $failureCount }}</span>
        </div>
        <div class="summary-item">
            <label>Doanh Thu Trung Bình</label>
            <span class="value">{{ number_format($averageAmount, 0, ',', '.') }}đ</span>
        </div>
    </div>

    <!-- By Payment Method -->
    <div class="section-title">💳 Phân Bố Theo Phương Thức Thanh Toán</div>
    <table>
        <thead>
            <tr>
                <th>Phương Thức</th>
                <th class="text-center">Tổng Giao Dịch</th>
                <th class="text-center">Thành Công</th>
                <th class="text-right">Tổng Tiền</th>
            </tr>
        </thead>
        <tbody>
            @php
                $methodLabels = [
                    'momo' => 'MoMo',
                    'vietqr' => 'VietQR',
                    'card' => 'Thẻ Tín Dụng',
                    'ewallet' => 'E-Wallet',
                ];
            @endphp
            @forelse($byMethod as $method => $data)
                <tr>
                    <td>
                        <span class="method-badge">{{ $methodLabels[$method] ?? $method }}</span>
                    </td>
                    <td class="text-center">{{ $data['count'] }}</td>
                    <td class="text-center">{{ $data['success_count'] }}</td>
                    <td class="text-right"><strong>{{ number_format($data['amount'], 0, ',', '.') }}đ</strong></td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">Không có dữ liệu</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Daily Revenue -->
    <div class="section-title">📈 Doanh Thu Theo Ngày</div>
    <table>
        <thead>
            <tr>
                <th>Ngày</th>
                <th class="text-center">Tổng Giao Dịch</th>
                <th class="text-center">Thành Công</th>
                <th class="text-right">Doanh Thu</th>
            </tr>
        </thead>
        <tbody>
            @forelse($byDate as $date => $data)
                <tr>
                    <td>{{ date('d/m/Y', strtotime($date)) }}</td>
                    <td class="text-center">{{ $data['count'] }}</td>
                    <td class="text-center">{{ $data['success_count'] }}</td>
                    <td class="text-right"><strong>{{ number_format($data['amount'], 0, ',', '.') }}đ</strong></td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">Không có dữ liệu</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Payment Details -->
    <div class="page-break"></div>
    <div class="section-title">📋 Chi Tiết Giao Dịch</div>
    <table>
        <thead>
            <tr>
                <th>Mã Giao Dịch</th>
                <th>Mã Đơn</th>
                <th>Khách Hàng</th>
                <th>Phương Thức</th>
                <th class="text-right">Số Tiền</th>
                <th class="text-center">Trạng Thái</th>
                <th>Ngày Tạo</th>
            </tr>
        </thead>
        <tbody>
            @forelse($payments as $payment)
                <tr>
                    <td><strong>{{ substr($payment->transaction_id, 0, 20) }}...</strong></td>
                    <td>{{ $payment->order->order_code }}</td>
                    <td>
                        {{ $payment->order->user->name }}<br>
                        <small style="color: #666;">{{ $payment->order->user->email }}</small>
                    </td>
                    <td>
                        <span class="method-badge">
                            @php
                                $methodLabels = [
                                    'momo' => 'MoMo',
                                    'vietqr' => 'VietQR',
                                    'card' => 'Thẻ',
                                    'ewallet' => 'E-Wallet',
                                ];
                            @endphp
                            {{ $methodLabels[$payment->payment_method] ?? $payment->payment_method }}
                        </span>
                    </td>
                    <td class="text-right"><strong>{{ number_format($payment->amount, 0, ',', '.') }}đ</strong></td>
                    <td class="text-center">
                        @if($payment->status === 'success' || $payment->status === 'pending')
                            <span class="status-success">✅ Thành Công</span>
                        @elseif($payment->status === 'failed')
                            <span class="status-failed">❌ Thất Bại</span>
                        @else
                            <span style="font-size: 11px;">{{ strtoupper($payment->status) }}</span>
                        @endif
                    </td>
                    <td>{{ $payment->created_at->format('d/m/Y H:i') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">Không có giao dịch nào</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Footer -->
    <div class="footer">
        <p>Báo cáo này được tạo tự động bởi hệ thống quản lý thanh toán</p>
        <p>© {{ now()->year }} TravelVN. All rights reserved.</p>
    </div>
</body>
</html>
