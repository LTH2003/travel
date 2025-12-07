@extends('admin.layouts.app')

@section('title', 'Chi tiết Tour')
@section('page-title', 'Chi tiết Tour')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Chi tiết: {{ $tour->title }}</h5>
                <div class="gap-2 d-flex">
                    <a href="{{ route('admin.tours.edit', $tour) }}" class="btn btn-warning btn-sm">
                        <i class="bi bi-pencil"></i> Chỉnh sửa
                    </a>
                    <a href="{{ route('admin.tours.index') }}" class="btn btn-secondary btn-sm">
                        <i class="bi bi-arrow-left"></i> Quay lại
                    </a>
                </div>
            </div>
            <div class="card-body">
                @if($tour->image)
                    <div class="mb-3">
                        <img src="{{ $tour->image }}" alt="{{ $tour->name }}" class="img-fluid rounded" style="max-height: 300px;">
                    </div>
                @endif

                <div class="mb-3">
                    <label class="form-label">Tên Tour</label>
                    <p class="form-control-plaintext fw-bold">{{ $tour->title }}</p>
                </div>

                <div class="mb-3">
                    <label class="form-label">Điểm đến</label>
                    <p class="form-control-plaintext">{{ $tour->destination }}</p>
                </div>

                <div class="mb-3">
                    <label class="form-label">Mô tả</label>
                    <p class="form-control-plaintext">{{ $tour->description }}</p>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Giá</label>
                            <p class="form-control-plaintext fw-bold text-success">
                                {{ number_format($tour->price) }} VNĐ
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Thời gian</label>
                            <p class="form-control-plaintext">{{ $tour->duration }}</p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Rating</label>
                            <p class="form-control-plaintext">
                                @if($tour->rating)
                                    <i class="bi bi-star-fill" style="color: #ffc107;"></i>
                                    {{ $tour->rating }}/5
                                @else
                                    <em class="text-muted">Chưa có rating</em>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Ngày tạo</label>
                    <p class="form-control-plaintext">
                        @if($tour->created_at)
                            {{ $tour->created_at->format('d/m/Y H:i:s') }}
                        @else
                            <em>N/A</em>
                        @endif
                    </p>
                </div>

                <div class="d-flex gap-2">
                    <a href="{{ route('admin.tours.edit', $tour) }}" class="btn btn-primary">
                        <i class="bi bi-pencil"></i> Chỉnh sửa
                    </a>
                    <form action="{{ route('admin.tours.destroy', $tour) }}" method="POST" style="display: inline;" onsubmit="return confirm('Bạn chắc chắn muốn xóa tour này?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-trash"></i> Xóa
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
