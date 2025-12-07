@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h1 class="h3 mb-0">Chi tiết Khách Sạn: {{ $hotel->name }}</h1>
            <div>
                <a href="{{ route('admin.hotel-manager.hotels.edit', $hotel) }}" class="btn btn-warning">
                    <i class="fas fa-edit me-2"></i>Sửa
                </a>
                <form action="{{ route('admin.hotel-manager.hotels.destroy', $hotel) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Xác nhận xóa?')">
                        <i class="fas fa-trash me-2"></i>Xóa
                    </button>
                </form>
                <a href="{{ route('admin.hotel-manager.hotels.index') }}" class="btn btn-secondary">
                    Quay lại
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-body">
                    @if($hotel->image)
                    <div class="mb-4">
                        <img src="{{ asset('storage/' . $hotel->image) }}" alt="{{ $hotel->name }}" style="max-height: 400px; width: 100%; object-fit: cover;" class="img-thumbnail">
                    </div>
                    @endif

                    <h2>{{ $hotel->name }}</h2>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <p><strong>Địa chỉ:</strong> {{ $hotel->address }}</p>
                            <p><strong>Thành phố:</strong> {{ $hotel->city }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Phòng:</strong> <span class="badge bg-info">{{ $hotel->rooms_count ?? 0 }}</span></p>
                            <p><strong>Đánh giá:</strong> <span class="badge bg-warning">{{ $hotel->rating ?? 'N/A' }}</span></p>
                        </div>
                    </div>

                    <hr>

                    <h5>Mô tả</h5>
                    <div class="mb-4">
                        {{ $hotel->description }}
                    </div>

                    @if($hotel->amenities)
                    <hr>
                    <h5>Tiện nghi</h5>
                    <div class="mb-4">
                        @foreach(explode(',', $hotel->amenities) as $amenity)
                        <span class="badge bg-secondary me-2">{{ trim($amenity) }}</span>
                        @endforeach
                    </div>
                    @endif

                    <hr>

                    <div class="row text-muted small">
                        <div class="col-md-6">
                            <p><strong>Được tạo:</strong> {{ $hotel->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Cập nhật lần cuối:</strong> {{ $hotel->updated_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Tóm tắt</h5>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-6">Tên:</dt>
                        <dd class="col-sm-6">{{ $hotel->name }}</dd>

                        <dt class="col-sm-6">Thành phố:</dt>
                        <dd class="col-sm-6">{{ $hotel->city }}</dd>

                        <dt class="col-sm-6">Phòng:</dt>
                        <dd class="col-sm-6"><span class="badge bg-info">{{ $hotel->rooms_count ?? 0 }}</span></dd>

                        <dt class="col-sm-6">Đánh giá:</dt>
                        <dd class="col-sm-6"><span class="badge bg-warning">{{ $hotel->rating ?? 'N/A' }}</span></dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
