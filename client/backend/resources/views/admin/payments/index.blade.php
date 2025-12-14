@extends('admin.layouts.app')

@section('title', 'Quản Lý Thanh Toán')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 text-gray-800 mb-0">💳 Quản Lý Thanh Toán</h1>
            <p class="text-muted small mb-0">Theo dõi tất cả các giao dịch thanh toán</p>
        </div>
        <a href="{{ route('admin.payments.statistics') }}" class="btn btn-primary">
            <i class="bi bi-graph-up"></i> Xem Thống Kê
        </a>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-1">Tổng Giao Dịch</p>
                            <h4 class="mb-0">{{ $totalPayments }}</h4>
                        </div>
                        <div class="badge bg-primary-light" style="font-size: 24px;">💳</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-1">Thành Công</p>
                            <h4 class="mb-0 text-success">{{ $successfulPayments }}</h4>
                        </div>
                        <div class="badge bg-success-light" style="font-size: 24px;">✅</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Hide Chờ Xử Lý card in mock mode --}}
        @if(!env('APP_PAYMENT_MOCK', false))
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-1">Chờ Xử Lý</p>
                            <h4 class="mb-0 text-warning">{{ $pendingPayments }}</h4>
                        </div>
                        <div class="badge bg-warning-light" style="font-size: 24px;">⏳</div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-1">Thất Bại</p>
                            <h4 class="mb-0 text-danger">{{ $failedPayments }}</h4>
                        </div>
                        <div class="badge bg-danger-light" style="font-size: 24px;">❌</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Amount Cards -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <p class="text-muted small mb-1">💰 Doanh Thu Thành Công</p>
                    <h3 class="text-primary mb-0">{{ number_format($totalAmount, 0, ',', '.') }}đ</h3>
                </div>
            </div>
        </div>

        {{-- Hide Doanh Thu Chờ Xác Nhận in mock mode --}}
        @if(!env('APP_PAYMENT_MOCK', false))
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <p class="text-muted small mb-1">⏳ Doanh Thu Chờ Xác Nhận</p>
                    <h3 class="text-warning mb-0">{{ number_format($pendingAmount, 0, ',', '.') }}đ</h3>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Filters Card -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.payments.index') }}" class="row g-3">
                <!-- Search -->
                <div class="col-md-4">
                    <label class="form-label small fw-bold">🔍 Tìm Kiếm</label>
                    <input type="text" name="search" class="form-control form-control-sm" 
                        placeholder="Mã đơn / Transaction ID" value="{{ request('search') }}">
                </div>

                <!-- Status Filter -->
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Trạng Thái</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">Tất Cả</option>
                        <option value="success" @if(request('status') === 'success') selected @endif>✅ Thành Công</option>
                        <option value="pending" @if(request('status') === 'pending') selected @endif>⏳ Chờ Xử Lý</option>
                        <option value="failed" @if(request('status') === 'failed') selected @endif>❌ Thất Bại</option>
                        <option value="expired" @if(request('status') === 'expired') selected @endif>⏰ Hết Hạn</option>
                    </select>
                </div>

                <!-- Payment Method Filter -->
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Phương Thức</label>
                    <select name="payment_method" class="form-select form-select-sm">
                        <option value="">Tất Cả</option>
                        <option value="momo" @if(request('payment_method') === 'momo') selected @endif>MoMo</option>
                        <option value="vietqr" @if(request('payment_method') === 'vietqr') selected @endif>VietQR</option>
                        <option value="card" @if(request('payment_method') === 'card') selected @endif>Thẻ</option>
                        <option value="ewallet" @if(request('payment_method') === 'ewallet') selected @endif>E-Wallet</option>
                    </select>
                </div>

                <!-- Date Range -->
                <div class="col-md-2">
                    <label class="form-label small fw-bold">Từ Ngày</label>
                    <input type="date" name="from_date" class="form-control form-control-sm" 
                        value="{{ request('from_date') }}">
                </div>

                <div class="col-md-2">
                    <label class="form-label small fw-bold">Đến Ngày</label>
                    <input type="date" name="to_date" class="form-control form-control-sm" 
                        value="{{ request('to_date') }}">
                </div>

                <!-- Actions -->
                <div class="col-12">
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="bi bi-search"></i> Tìm Kiếm
                    </button>
                    <a href="{{ route('admin.payments.index') }}" class="btn btn-sm btn-secondary">
                        <i class="bi bi-arrow-clockwise"></i> Reset
                    </a>
                    <a href="{{ route('admin.payments.exportPdf', request()->query()) }}" class="btn btn-sm btn-danger">
                        <i class="bi bi-file-pdf"></i> Xuất PDF
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Payments Table -->
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="fw-bold">Mã Giao Dịch</th>
                        <th class="fw-bold">Mã Đơn</th>
                        <th class="fw-bold">Khách Hàng</th>
                        <th class="fw-bold">Phương Thức</th>
                        <th class="fw-bold">Số Tiền</th>
                        <th class="fw-bold">Trạng Thái</th>
                        <th class="fw-bold">Ngày Tạo</th>
                        <th class="text-center fw-bold">Hành Động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                        <tr>
                            <td>
                                <small class="text-monospace text-primary fw-bold">
                                    {{ substr($payment->transaction_id, 0, 20) }}...
                                </small>
                            </td>
                            <td>
                                <strong class="text-dark">
                                    {{ $payment->order->order_code }}
                                </strong>
                            </td>
                            <td>
                                <div>
                                    <strong>{{ $payment->order->user->name }}</strong><br>
                                    <small class="text-muted">{{ $payment->order->user->email }}</small>
                                </div>
                            </td>
                            <td>
                                @php
                                    $methodIcons = [
                                        'momo' => '💜',
                                        'vietqr' => '🏦',
                                        'card' => '💳',
                                        'ewallet' => '📱',
                                    ];
                                    $methodLabel = [
                                        'momo' => 'MoMo',
                                        'vietqr' => 'VietQR',
                                        'card' => 'Thẻ',
                                        'ewallet' => 'E-Wallet',
                                    ];
                                @endphp
                                <span class="badge bg-light text-dark">
                                    {{ $methodIcons[$payment->payment_method] ?? '' }}
                                    {{ $methodLabel[$payment->payment_method] ?? $payment->payment_method }}
                                </span>
                            </td>
                            <td>
                                <strong class="text-primary">
                                    {{ number_format($payment->amount, 0, ',', '.') }}đ
                                </strong>
                            </td>
                            <td>
                                @php
                                    // In mock mode, treat pending as success
                                    $displayStatus = ($payment->status === 'pending' && env('APP_PAYMENT_MOCK', false)) ? 'success' : $payment->status;
                                @endphp
                                @if($displayStatus === 'success')
                                    <span class="badge bg-success">✅ Thành Công</span>
                                @elseif($displayStatus === 'pending')
                                    <span class="badge bg-warning">⏳ Chờ Xử Lý</span>
                                @elseif($displayStatus === 'failed')
                                    <span class="badge bg-danger">❌ Thất Bại</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($displayStatus) }}</span>
                                @endif
                            </td>
                            <td>
                                <small class="text-muted">
                                    {{ $payment->created_at->format('d/m/Y H:i') }}
                                </small>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('admin.payments.show', $payment->id) }}" class="btn btn-sm btn-info" title="Xem Chi Tiết">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <!-- Refund/Delete Button - Show for successful and pending payments in mock mode -->
                                @php
                                    $canRefund = $payment->status === 'success' || 
                                                 ($payment->status === 'pending' && env('APP_PAYMENT_MOCK', false));
                                @endphp
                                @if($canRefund)
                                    <button type="button" class="btn btn-sm btn-danger" title="Hoàn Tiền / Xóa" data-bs-toggle="modal" data-bs-target="#refundModal{{ $payment->id }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                @endif

                                <!-- Refund Modal -->
                                <div class="modal fade" id="refundModal{{ $payment->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header bg-danger text-white">
                                                <h5 class="modal-title">⚠️ Xác Nhận Hoàn Tiền</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form method="POST" action="{{ route('admin.payments.destroy', $payment->id) }}">
                                                @csrf
                                                @method('DELETE')
                                                <div class="modal-body">
                                                    <p class="text-danger fw-bold">⚠️ Bạn sắp hoàn tiền cho khách hàng:</p>
                                                    <div class="bg-light p-3 rounded mb-3">
                                                        <p class="mb-1"><strong>Khách Hàng:</strong> {{ $payment->order->user->name }}</p>
                                                        <p class="mb-1"><strong>Số Tiền:</strong> <span class="text-danger fw-bold">{{ number_format($payment->amount, 0, ',', '.') }}đ</span></p>
                                                        <p class="mb-0"><strong>Mã Đơn:</strong> {{ $payment->order->order_code }}</p>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">Lý Do Hoàn Tiền (Tùy Chọn)</label>
                                                        <textarea name="reason" class="form-control" rows="3" placeholder="VD: Khách hàng hủy booking"></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                                    <button type="submit" class="btn btn-danger" onclick="return confirm('Hành động này không thể hoàn tác. Bạn có chắc chắn?')">
                                                        <i class="bi bi-check"></i> Xác Nhận Hoàn Tiền
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="bi bi-inbox"></i> Không có giao dịch nào
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</div>

<style>
    .text-monospace {
        font-family: 'Monaco', 'Menlo', 'Courier New', monospace;
        font-size: 0.85rem;
    }

    .bg-primary-light {
        background-color: rgba(13, 110, 253, 0.1);
    }

    .bg-success-light {
        background-color: rgba(25, 135, 84, 0.1);
    }

    .bg-warning-light {
        background-color: rgba(255, 193, 7, 0.1);
    }

    .bg-danger-light {
        background-color: rgba(220, 53, 69, 0.1);
    }
</style>
@endsection
