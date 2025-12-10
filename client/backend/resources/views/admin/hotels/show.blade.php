@extends('admin.layouts.app')

@section('title', 'Chi tiết Khách sạn - ' . $hotel->name)
@section('page-title', 'Chi tiết Khách sạn')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">{{ $hotel->name }}</h5>
                <div>
                    <a href="{{ route('admin.hotels.edit', $hotel) }}" class="btn btn-warning btn-sm">
                        <i class="bi bi-pencil"></i> Chỉnh sửa
                    </a>
                    <a href="{{ route('admin.hotels.index') }}" class="btn btn-secondary btn-sm">
                        <i class="bi bi-arrow-left"></i> Quay lại
                    </a>
                </div>
            </div>
            <div class="card-body">
                <!-- Hình ảnh -->
                @if($hotel->image)
                    <div class="mb-4">
                        <img src="{{ $hotel->image }}" alt="{{ $hotel->name }}" class="img-fluid rounded" style="max-height: 400px; object-fit: cover; width: 100%;">
                    </div>
                @endif

                <!-- Thông tin cơ bản -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-muted">Tên Khách sạn</h6>
                        <p class="fs-5"><strong>{{ $hotel->name }}</strong></p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted">Địa điểm</h6>
                        <p class="fs-5">
                            <i class="bi bi-geo-alt-fill text-danger"></i>
                            <strong>{{ $hotel->location }}</strong>
                        </p>
                    </div>
                </div>

                @if($hotel->address)
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-muted">Địa chỉ</h6>
                            <p>{{ $hotel->address }}</p>
                        </div>
                    </div>
                @endif

                <!-- Giá -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-muted">Giá hiện tại</h6>
                        <p class="fs-5">
                            <strong class="text-success">{{ number_format($hotel->price) }} VNĐ</strong>
                        </p>
                    </div>
                    @if($hotel->original_price && $hotel->original_price != $hotel->price)
                        <div class="col-md-6">
                            <h6 class="text-muted">Giá gốc</h6>
                            <p class="fs-5">
                                <s class="text-muted">{{ number_format($hotel->original_price) }} VNĐ</s>
                                <span class="badge bg-danger ms-2">
                                    -{{ round(((($hotel->original_price - $hotel->price) / $hotel->original_price) * 100)) }}%
                                </span>
                            </p>
                        </div>
                    @endif
                </div>

                <!-- Đánh giá -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-muted">Đánh giá</h6>
                        @if($hotel->rating)
                            <p class="fs-5">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= floor($hotel->rating))
                                        <i class="bi bi-star-fill" style="color: #ffc107;"></i>
                                    @elseif($i - 0.5 < $hotel->rating)
                                        <i class="bi bi-star-half" style="color: #ffc107;"></i>
                                    @else
                                        <i class="bi bi-star" style="color: #ffc107;"></i>
                                    @endif
                                @endfor
                                <strong>{{ $hotel->rating }}/5</strong>
                            </p>
                        @else
                            <p class="text-muted"><em>Chưa có đánh giá</em></p>
                        @endif
                    </div>
                    @if($hotel->reviews)
                        <div class="col-md-6">
                            <h6 class="text-muted">Số lượng đánh giá</h6>
                            <p class="fs-5"><strong>{{ $hotel->reviews }}</strong> đánh giá</p>
                        </div>
                    @endif
                </div>

                <!-- Thông tin chi tiết -->
                @if($hotel->check_in || $hotel->check_out)
                    <div class="row mb-4">
                        @if($hotel->check_in)
                            <div class="col-md-6">
                                <h6 class="text-muted">Giờ Check-in</h6>
                                <p><strong>{{ $hotel->check_in }}</strong></p>
                            </div>
                        @endif
                        @if($hotel->check_out)
                            <div class="col-md-6">
                                <h6 class="text-muted">Giờ Check-out</h6>
                                <p><strong>{{ $hotel->check_out }}</strong></p>
                            </div>
                        @endif
                    </div>
                @endif

                @if($hotel->cancellation)
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-muted">Chính sách hủy</h6>
                            <p><strong>{{ $hotel->cancellation }}</strong></p>
                        </div>
                    </div>
                @endif

                @if($hotel->children)
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-muted">Chính sách trẻ em</h6>
                            <p>{{ $hotel->children }}</p>
                        </div>
                    </div>
                @endif

                <!-- Mô tả -->
                @if($hotel->description)
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-muted">Mô tả</h6>
                            <p>{{ $hotel->description }}</p>
                        </div>
                    </div>
                @endif

                <!-- Tiện nghi -->
                @if($hotel->amenities && is_array($hotel->amenities) && count($hotel->amenities) > 0)
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-muted">Tiện nghi</h6>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($hotel->amenities as $amenity)
                                    <span class="badge bg-info">{{ $amenity }}</span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Metadata -->
                <div class="row mt-5 pt-4 border-top">
                    <div class="col-md-6">
                        <h6 class="text-muted">Tạo lúc</h6>
                        <small class="text-secondary">{{ $hotel->created_at->format('d/m/Y H:i') }}</small>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted">Cập nhật lúc</h6>
                        <small class="text-secondary">{{ $hotel->updated_at->format('d/m/Y H:i') }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Hành động -->
        <div class="card mb-3">
            <div class="card-header">
                <h6 class="mb-0">Hành động</h6>
            </div>
            <div class="card-body">
                <a href="{{ route('admin.hotels.edit', $hotel) }}" class="btn btn-warning w-100 mb-2">
                    <i class="bi bi-pencil-square"></i> Chỉnh sửa
                </a>
                <a href="{{ route('admin.hotels.rooms.index', $hotel) }}" class="btn btn-info w-100 mb-2">
                    <i class="bi bi-door-closed"></i> Quản lý phòng
                </a>
                <form action="{{ route('admin.hotels.destroy', $hotel) }}" method="POST" onsubmit="return confirm('Bạn chắc chắn muốn xóa khách sạn này? Hành động này không thể hoàn tác!');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger w-100">
                        <i class="bi bi-trash"></i> Xóa
                    </button>
                </form>
            </div>
        </div>

        <!-- Thông tin tạo -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Thông tin</h6>
            </div>
            <div class="card-body small">
                <div class="mb-3">
                    <strong class="text-muted d-block">ID</strong>
                    <code>{{ $hotel->id }}</code>
                </div>
                <div class="mb-3">
                    <strong class="text-muted d-block">Tạo bởi</strong>
                    <code>{{ $hotel->created_by }}</code>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
