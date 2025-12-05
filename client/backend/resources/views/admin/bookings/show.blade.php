@extends('admin.layouts.app')

@section('title', 'Chi Tiết Booking - ' . $booking->order_code)

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="mb-0">{{ $booking->order_code }}</h2>
            <small class="text-muted">Chi tiết đơn booking</small>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('admin.bookings.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Quay lại
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Left Column -->
        <div class="col-md-8">
            <!-- Order Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Thông Tin Đơn Hàng</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p class="text-muted mb-1">Mã Đơn</p>
                            <p class="fw-bold mb-3">{{ $booking->order_code }}</p>

                            <p class="text-muted mb-1">Tổng Tiền</p>
                            <p class="fw-bold text-primary mb-3">{{ number_format($booking->total_amount) }}đ</p>

                            <p class="text-muted mb-1">Phương Thức Thanh Toán</p>
                            <p class="fw-bold mb-3">{{ ucfirst($booking->payment_method ?? 'Chưa cập nhật') }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="text-muted mb-1">Trạng Thái</p>
                            <p class="mb-3">
                                @if($booking->status === 'completed')
                                    <span class="badge bg-success">✓ Hoàn Tất</span>
                                @else
                                    <span class="badge bg-warning">⏳ Chờ Xử Lý</span>
                                @endif
                            </p>

                            <p class="text-muted mb-1">Ngày Tạo</p>
                            <p class="fw-bold mb-3">{{ $booking->created_at->format('d/m/Y H:i') }}</p>

                            @if($booking->completed_at)
                                <p class="text-muted mb-1">Ngày Hoàn Tất</p>
                                <p class="fw-bold mb-3">{{ $booking->completed_at->format('d/m/Y H:i') }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Booking Items -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Các Mục Đặt Hàng</h5>
                </div>
                <div class="card-body">
                    @if($booking->bookingDetails->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Sản Phẩm</th>
                                        <th>Loại</th>
                                        <th class="text-center">Số Lượng</th>
                                        <th class="text-right">Giá</th>
                                        <th class="text-right">Tổng</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($booking->bookingDetails as $detail)
                                        @php
                                            $itemName = 'Unknown';
                                            if ($detail->booking_info && isset($detail->booking_info['name'])) {
                                                $itemName = $detail->booking_info['name'];
                                            }
                                        @endphp
                                        <tr>
                                            <td>{{ $itemName }}</td>
                                            <td>
                                                @if(strtolower($detail->bookable_type) === 'tour')
                                                    <span class="badge bg-info">Tour</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ $detail->bookable_type }}</span>
                                                @endif
                                            </td>
                                            <td class="text-center">{{ $detail->quantity }}</td>
                                            <td class="text-right">{{ number_format($detail->price) }}đ</td>
                                            <td class="text-right fw-bold">{{ number_format($detail->price * $detail->quantity) }}đ</td>
                                        </tr>
                                    @endforeach
                                    <tr class="table-light">
                                        <td colspan="4" class="text-end fw-bold">Tổng Cộng:</td>
                                        <td class="text-right fw-bold text-primary">{{ number_format($booking->total_amount) }}đ</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted mb-0">Không có item nào</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="col-md-4">
            <!-- Customer Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Thông Tin Khách Hàng</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-1">Tên</p>
                    <p class="fw-bold mb-3">{{ $booking->user->name }}</p>

                    <p class="text-muted mb-1">Email</p>
                    <p class="fw-bold mb-3">
                        <a href="mailto:{{ $booking->user->email }}">{{ $booking->user->email }}</a>
                    </p>

                    <p class="text-muted mb-1">Điện Thoại</p>
                    <p class="fw-bold mb-3">{{ $booking->user->phone ?? 'Chưa cập nhật' }}</p>

                    <p class="text-muted mb-1">Địa Chỉ</p>
                    <p class="fw-bold mb-3">{{ $booking->user->address ?? 'Chưa cập nhật' }}</p>
                </div>
            </div>

            <!-- Status Update -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Cập Nhật Trạng Thái</h5>
                </div>
                <div class="card-body">
                    @if($booking->status !== 'completed')
                        <form action="{{ route('admin.bookings.updateStatus', $booking->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="completed">
                            <button type="submit" class="btn btn-success w-100">
                                <i class="bi bi-check-circle"></i> Mark Hoàn Tất
                            </button>
                        </form>
                    @else
                        <div class="alert alert-success mb-0">
                            <i class="bi bi-check-circle"></i> Booking đã hoàn tất
                        </div>
                    @endif
                </div>
            </div>

            <!-- Notes -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Ghi Chú</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.bookings.updateStatus', $booking->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <textarea name="notes" class="form-control" rows="4" placeholder="Thêm ghi chú...">{{ $booking->notes }}</textarea>
                        <button type="submit" class="btn btn-primary mt-2 w-100">
                            <i class="bi bi-save"></i> Lưu Ghi Chú
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@endsection
