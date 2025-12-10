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
                        @foreach($booking->bookingDetails as $detail)
                            @php
                                $itemName = 'Unknown';
                                $itemType = 'Unknown';
                                $details = [];
                                
                                if ($detail->booking_info) {
                                    $itemName = $detail->booking_info['name'] ?? 'Unknown';
                                    $details = $detail->booking_info;
                                }
                                
                                if(strpos($detail->bookable_type, 'Tour') !== false) {
                                    $itemType = '🎫 Tour';
                                } elseif(strpos($detail->bookable_type, 'Room') !== false || strpos($detail->bookable_type, 'Hotel') !== false) {
                                    $itemType = '🏨 Phòng/Khách sạn';
                                }
                            @endphp
                            <div class="card border mb-3">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <h6 class="card-title mb-2">
                                                <strong>{{ $itemName }}</strong>
                                                <span class="badge bg-info ms-2">{{ $itemType }}</span>
                                            </h6>
                                            
                                            @if(isset($details['destination']))
                                                <p class="mb-1">
                                                    <strong class="text-muted">Điểm Đến:</strong> {{ $details['destination'] }}
                                                </p>
                                            @endif
                                            
                                            @if(isset($details['duration']))
                                                <p class="mb-1">
                                                    <strong class="text-muted">Thời Gian:</strong> {{ $details['duration'] }}
                                                </p>
                                            @endif
                                            
                                            @if(isset($details['hotel']))
                                                <p class="mb-1">
                                                    <strong class="text-muted">Khách Sạn:</strong> {{ $details['hotel'] }}
                                                </p>
                                            @endif
                                            
                                            @if(isset($details['location']))
                                                <p class="mb-1">
                                                    <strong class="text-muted">Địa Chỉ:</strong> {{ $details['location'] }}
                                                </p>
                                            @endif
                                            
                                            @if(isset($details['capacity']))
                                                <p class="mb-1">
                                                    <strong class="text-muted">Sức Chứa:</strong> {{ $details['capacity'] }} người
                                                </p>
                                            @endif
                                            
                                            @if(isset($details['description']) && $details['description'])
                                                <p class="mb-1">
                                                    <strong class="text-muted">Mô Tả:</strong><br>
                                                    <small>{{ $details['description'] }}...</small>
                                                </p>
                                            @endif
                                        </div>
                                        <div class="col-md-4 text-end">
                                            <p class="mb-1">
                                                <strong class="text-muted">Giá:</strong><br>
                                                <strong>{{ number_format($detail->price) }}đ</strong>
                                            </p>
                                            <p class="mb-1">
                                                <strong class="text-muted">Số Lượng:</strong><br>
                                                <strong>{{ $detail->quantity }}</strong>
                                            </p>
                                            <p class="mb-0">
                                                <strong class="text-muted">Tổng:</strong><br>
                                                <strong class="text-primary">{{ number_format($detail->price * $detail->quantity) }}đ</strong>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        
                        <div class="card border-0 bg-light">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-8">
                                        <h6 class="mb-0">TỔNG CỘNG</h6>
                                    </div>
                                    <div class="col-md-4 text-end">
                                        <h5 class="mb-0 text-primary">{{ number_format($booking->total_amount) }}đ</h5>
                                    </div>
                                </div>
                            </div>
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
