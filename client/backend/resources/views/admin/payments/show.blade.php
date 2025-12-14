@extends('admin.layouts.app')

@section('title', 'Chi Tiết Thanh Toán')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 text-gray-800 mb-0">💳 Chi Tiết Giao Dịch Thanh Toán</h1>
            <p class="text-muted small mb-0">Mã giao dịch: <code>{{ $payment->transaction_id }}</code></p>
        </div>
        <a href="{{ route('admin.payments.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Quay Lại
        </a>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Payment Info -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light border-bottom">
                    <h5 class="mb-0">🧾 Thông Tin Giao Dịch</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p class="text-muted small mb-1">Mã Giao Dịch</p>
                            <p class="fw-bold text-break">{{ $payment->transaction_id }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="text-muted small mb-1">Request ID</p>
                            <p class="fw-bold text-break">{{ $payment->request_id ?? 'N/A' }}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p class="text-muted small mb-1">Số Tiền</p>
                            <p class="h5 text-primary mb-0">{{ number_format($payment->amount, 0, ',', '.') }}đ</p>
                        </div>
                        <div class="col-md-6">
                            <p class="text-muted small mb-1">Phương Thức Thanh Toán</p>
                            <p class="fw-bold">
                                @php
                                    $methodIcons = [
                                        'momo' => '💜 MoMo',
                                        'vietqr' => '🏦 VietQR',
                                        'card' => '💳 Thẻ',
                                        'ewallet' => '📱 E-Wallet',
                                    ];
                                @endphp
                                {{ $methodIcons[$payment->payment_method] ?? ucfirst($payment->payment_method) }}
                            </p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <p class="text-muted small mb-1">Trạng Thái</p>
                            <p class="mb-0">
                                @php
                                    $displayStatus = env('APP_PAYMENT_MOCK', false) && $payment->status === 'pending' ? 'success' : $payment->status;
                                @endphp
                                @if($displayStatus === 'success')
                                    <span class="badge bg-success fs-6">✅ Thành Công</span>
                                @elseif($displayStatus === 'pending')
                                    <span class="badge bg-warning fs-6">⏳ Chờ Xử Lý</span>
                                @elseif($displayStatus === 'failed')
                                    <span class="badge bg-danger fs-6">❌ Thất Bại</span>
                                @else
                                    <span class="badge bg-secondary fs-6">{{ strtoupper($displayStatus) }}</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p class="text-muted small mb-1">Ngày Tạo</p>
                            <p class="fw-bold">{{ $payment->created_at->format('d/m/Y H:i:s') }}</p>
                        </div>
                    </div>

                    @if($payment->paid_at)
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <p class="text-muted small mb-1">Ngày Thanh Toán</p>
                                <p class="fw-bold text-success">{{ $payment->paid_at->format('d/m/Y H:i:s') }}</p>
                            </div>
                            <div class="col-md-6">
                                <p class="text-muted small mb-1">Thời Gian Xử Lý</p>
                                <p class="fw-bold">{{ $payment->paid_at->diffInMinutes($payment->created_at) }} phút</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Order Info -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light border-bottom">
                    <h5 class="mb-0">📦 Thông Tin Đơn Hàng Liên Quan</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p class="text-muted small mb-1">Mã Đơn Hàng</p>
                            <p class="fw-bold">
                                <a href="{{ route('admin.bookings.show', $payment->order->id) }}" class="text-primary">
                                    {{ $payment->order->order_code }}
                                </a>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p class="text-muted small mb-1">Tổng Tiền Đơn</p>
                            <p class="fw-bold text-primary">{{ number_format($payment->order->total_amount, 0, ',', '.') }}đ</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p class="text-muted small mb-1">Trạng Thái Đơn</p>
                            <p class="mb-0">
                                @if($payment->order->status === 'completed')
                                    <span class="badge bg-success">✓ Hoàn Tất</span>
                                @else
                                    <span class="badge bg-warning">⏳ Chờ Xử Lý</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p class="text-muted small mb-1">Số Lượng Items</p>
                            <p class="fw-bold">{{ $payment->order->bookingDetails->count() }} sản phẩm</p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <p class="text-muted small mb-1">Các Items Trong Đơn</p>
                            <ul class="list-group list-group-sm">
                                @foreach($payment->order->bookingDetails as $item)
                                    <li class="list-group-item">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <strong>{{ $item->item_details['name'] ?? 'N/A' }}</strong><br>
                                                <small class="text-muted">
                                                    @if($item->item_details['destination'] ?? false)
                                                        📍 {{ $item->item_details['destination'] }}
                                                    @endif
                                                </small>
                                            </div>
                                            <span class="badge bg-light text-dark">
                                                {{ number_format($item->item_details['price'] ?? 0, 0, ',', '.') }}đ
                                            </span>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Response Data -->
            @if($payment->response_data && !empty($payment->response_data))
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-light border-bottom">
                        <h5 class="mb-0">📊 Dữ Liệu Phản Hồi</h5>
                    </div>
                    <div class="card-body">
                        <pre class="bg-light p-3 rounded" style="font-size: 0.85rem; overflow-x: auto;">{{ json_encode($payment->response_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                    </div>
                </div>
            @endif

            <!-- Error Message -->
            @if($payment->error_message)
                <div class="alert alert-danger border-0" role="alert">
                    <h5 class="alert-heading">⚠️ Lỗi Xử Lý</h5>
                    <p class="mb-0">{{ $payment->error_message }}</p>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Customer Info -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light border-bottom">
                    <h5 class="mb-0">👤 Thông Tin Khách Hàng</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="bg-primary-light rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                            <i class="bi bi-person-fill" style="font-size: 28px;"></i>
                        </div>
                    </div>

                    <p class="text-center mb-3">
                        <strong class="d-block">{{ $payment->order->user->name }}</strong>
                        <small class="text-muted">{{ $payment->order->user->email }}</small>
                    </p>

                    <hr>

                    <p class="text-muted small mb-1">Điện Thoại</p>
                    <p class="fw-bold mb-3">{{ $payment->order->user->phone ?? 'N/A' }}</p>

                    <p class="text-muted small mb-1">Địa Chỉ</p>
                    <p class="fw-bold mb-3">{{ $payment->order->user->address ?? 'N/A' }}</p>

                    <a href="{{ route('admin.users.show', $payment->order->user->id) }}" class="btn btn-sm btn-outline-primary w-100">
                        <i class="bi bi-eye"></i> Xem Hồ Sơ Khách Hàng
                    </a>
                </div>
            </div>

            <!-- Related Payments -->
            @if($relatedPayments->count() > 0)
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light border-bottom">
                        <h5 class="mb-0">🔗 Giao Dịch Khác Cùng Đơn</h5>
                    </div>
                    <div class="card-body p-0">
                        @foreach($relatedPayments as $related)
                            <div class="p-3 border-bottom" style="cursor: pointer;" onclick="window.location='{{ route('admin.payments.show', $related->id) }}'">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <p class="fw-bold small mb-1">{{ substr($related->transaction_id, 0, 15) }}...</p>
                                        <small class="text-muted">{{ $related->created_at->format('d/m H:i') }}</small>
                                    </div>
                                    @if($related->status === 'success')
                                        <span class="badge bg-success">✅</span>
                                    @elseif($related->status === 'pending')
                                        <span class="badge bg-warning">⏳</span>
                                    @else
                                        <span class="badge bg-danger">❌</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Mark Failed Modal -->
<div class="modal fade" id="markFailedModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0">
            <div class="modal-header bg-light border-bottom">
                <h5 class="modal-title">Đánh Dấu Thanh Toán Thất Bại</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.payments.markFailed', $payment->id) }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Lý Do Thất Bại</label>
                        <textarea name="error_message" class="form-control" rows="4" placeholder="Nhập lý do..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Huỷ</button>
                    <button type="submit" class="btn btn-danger">Xác Nhận</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .bg-primary-light {
        background-color: rgba(13, 110, 253, 0.1);
    }

    .bg-warning-light {
        background-color: rgba(255, 193, 7, 0.1);
    }
</style>
@endsection
