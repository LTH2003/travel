@extends('admin.layouts.app')

@section('title', 'Thống Kê Thanh Toán')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 text-gray-800 mb-0">📊 Thống Kê Thanh Toán</h1>
            <p class="text-muted small mb-0">Phân tích chi tiết các giao dịch thanh toán</p>
        </div>
        <a href="{{ route('admin.payments.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Quay Lại
        </a>
    </div>

    <!-- Key Metrics -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <p class="text-muted small mb-1">💰 Tổng Doanh Thu</p>
                    <h3 class="text-primary mb-0">{{ number_format($totalAmount, 0, ',', '.') }}đ</h3>
                    <small class="text-success">✅ {{ $successfulPaymentRecords }} giao dịch thành công</small>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <p class="text-muted small mb-1">📈 Tổng Giao Dịch</p>
                    <h3 class="mb-0">{{ $totalCount }}</h3>
                    <small class="text-muted">Tất cả trạng thái kết hợp</small>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <p class="text-muted small mb-1">✅ Tỷ Lệ Thành Công</p>
                    <h3 class="text-success mb-0">{{ number_format($conversionRate, 1) }}%</h3>
                    <small class="text-muted">Từ tổng số giao dịch</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Method Details -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-light border-bottom">
            <h5 class="mb-0">🔍 Chi Tiết Theo Phương Thức Thanh Toán</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="fw-bold">Phương Thức</th>
                        <th class="fw-bold text-center">Tổng Giao Dịch</th>
                        <th class="fw-bold text-center">Thành Công</th>
                        <th class="fw-bold text-center">Thất Bại</th>
                        <th class="fw-bold text-center">Tỷ Lệ Thành Công</th>
                        <th class="fw-bold">Tổng Doanh Thu</th>
                        <th class="fw-bold">Doanh Thu Trung Bình</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $methodLabel = [
                            'momo' => '💜 MoMo',
                            'vietqr' => '🏦 VietQR',
                            'card' => '💳 Thẻ',
                            'ewallet' => '📱 E-Wallet',
                            'zalopay' => '🟠 ZaloPay',
                        ];
                    @endphp
                    @forelse($byMethod as $method)
                        <tr>
                            <td class="fw-bold">{{ $methodLabel[$method->payment_method] ?? $method->payment_method }}</td>
                            <td class="text-center">
                                <span class="badge bg-primary">{{ $method->count }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-success">{{ $method->success_count }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-danger">{{ $method->failed_count }}</span>
                            </td>
                            <td class="text-center">
                                @php
                                    $rate = $method->count > 0 
                                        ? ($method->success_count / $method->count) * 100 
                                        : 0;
                                @endphp
                                <strong class="text-success">{{ number_format($rate, 1) }}%</strong>
                            </td>
                            <td class="text-primary">
                                {{ number_format($method->success_amount, 0, ',', '.') }}đ
                            </td>
                            <td>
                                {{ number_format($method->avg_amount ?? 0, 0, ',', '.') }}đ
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-3">Không có dữ liệu</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
