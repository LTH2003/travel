@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h1 class="h3 mb-0">Chi tiết Tour: {{ $tour->name }}</h1>
            <div>
                <a href="{{ route('admin.tour-manager.tours.edit', $tour) }}" class="btn btn-warning">
                    <i class="fas fa-edit me-2"></i>Sửa
                </a>
                <form action="{{ route('admin.tour-manager.tours.destroy', $tour) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Xác nhận xóa?')">
                        <i class="fas fa-trash me-2"></i>Xóa
                    </button>
                </form>
                <a href="{{ route('admin.tour-manager.tours.index') }}" class="btn btn-secondary">
                    Quay lại
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-body">
                    @if($tour->image)
                    <div class="mb-4">
                        <img src="{{ asset('storage/' . $tour->image) }}" alt="{{ $tour->name }}" style="max-height: 400px; width: 100%; object-fit: cover;" class="img-thumbnail">
                    </div>
                    @endif

                    <h2>{{ $tour->name }}</h2>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <p><strong>Địa điểm:</strong> {{ $tour->destination }}</p>
                            <p><strong>Thời gian:</strong> {{ $tour->duration }} ngày</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Giá:</strong> <span class="text-success">{{ number_format($tour->price) }}đ</span></p>
                            <p><strong>Đánh giá:</strong> <span class="badge bg-warning">{{ $tour->rating ?? 'N/A' }}</span></p>
                        </div>
                    </div>

                    <hr>

                    <h5>Mô tả</h5>
                    <div class="mb-4">
                        {{ $tour->description }}
                    </div>

                    <hr>

                    <div class="row text-muted small">
                        <div class="col-md-6">
                            <p><strong>Được tạo:</strong> {{ $tour->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Cập nhật lần cuối:</strong> {{ $tour->updated_at->format('d/m/Y H:i') }}</p>
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
                        <dd class="col-sm-6">{{ $tour->name }}</dd>

                        <dt class="col-sm-6">Địa điểm:</dt>
                        <dd class="col-sm-6">{{ $tour->destination }}</dd>

                        <dt class="col-sm-6">Thời gian:</dt>
                        <dd class="col-sm-6">{{ $tour->duration }} ngày</dd>

                        <dt class="col-sm-6">Giá:</dt>
                        <dd class="col-sm-6 text-success">{{ number_format($tour->price) }}đ</dd>

                        <dt class="col-sm-6">Đánh giá:</dt>
                        <dd class="col-sm-6"><span class="badge bg-warning">{{ $tour->rating ?? 'N/A' }}</span></dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
