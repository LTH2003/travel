@extends('admin.layouts.app')

@section('title', 'Quản Lý Booking')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-12">
            <h2 class="mb-0">Quản Lý Booking</h2>
            <small class="text-muted">Quản lý tất cả các đơn booking từ khách hàng</small>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-2">Tổng Booking</p>
                            <h3 class="mb-0">{{ $stats['total_bookings'] ?? 0 }}</h3>
                        </div>
                        <div class="text-primary" style="font-size: 32px;">📦</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-2">Hoàn Tất</p>
                            <h3 class="mb-0 text-success">{{ $stats['completed_bookings'] ?? 0 }}</h3>
                        </div>
                        <div class="text-success" style="font-size: 32px;">✓</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-2">Chờ Xử Lý</p>
                            <h3 class="mb-0 text-warning">{{ $stats['pending_bookings'] ?? 0 }}</h3>
                        </div>
                        <div class="text-warning" style="font-size: 32px;">⏳</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-2">Doanh Thu</p>
                            <h3 class="mb-0 text-primary">{{ number_format($stats['total_revenue'] ?? 0) }}đ</h3>
                        </div>
                        <div class="text-primary" style="font-size: 32px;">💰</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Tìm Kiếm & Lọc</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.bookings.index') }}" class="row g-3">
                <div class="col-md-6">
                    <input type="text" name="search" class="form-control" placeholder="Tìm theo mã đơn, tên khách, email..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">Tất cả trạng thái</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Hoàn tất</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Tìm kiếm
                    </button>
                </div>
                <div class="col-md-1">
                    <a href="{{ route('admin.bookings.index') }}" class="btn btn-outline-secondary w-100" title="Làm mới danh sách">
                        <i class="bi bi-arrow-clockwise"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Bookings Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Danh Sách Booking</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Mã Đơn</th>
                        <th>Khách Hàng</th>
                        <th>Số Item</th>
                        <th>Tổng Tiền</th>
                        <th>Trạng Thái</th>
                        <th>Ngày Tạo</th>
                        <th class="text-center">Hành Động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bookings as $booking)
                        <tr>
                            <td>
                                <a href="{{ route('admin.bookings.show', $booking->id) }}" class="text-primary fw-bold">
                                    {{ $booking->order_code }}
                                </a>
                            </td>
                            <td>
                                <div>
                                    <strong>{{ $booking->user->name }}</strong><br>
                                    <small class="text-muted">{{ $booking->user->email }}</small>
                                </div>
                            </td>
                            <td>
                                {{ $booking->bookingDetails->count() }} item
                            </td>
                            <td class="text-primary fw-bold">
                                {{ number_format($booking->total_amount) }}đ
                            </td>
                            <td>
                                @if($booking->status === 'completed')
                                    <span class="badge bg-success">✓ Hoàn Tất</span>
                                @else
                                    <span class="badge bg-warning">⏳ Chờ Xử Lý</span>
                                @endif
                            </td>
                            <td>{{ $booking->created_at->format('d/m/Y H:i') }}</td>
                            <td class="text-center">
                                <a href="{{ route('admin.bookings.show', $booking->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i> Chi Tiết
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                Không có booking nào
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($bookings->hasPages())
            <div class="card-footer">
                {{ $bookings->links() }}
            </div>
        @endif
    </div>
</div>

<style>
    .card {
        border: 1px solid #dee2e6;
        box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);
    }
</style>
@endsection
