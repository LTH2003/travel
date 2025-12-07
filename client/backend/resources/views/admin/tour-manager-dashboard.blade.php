@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4">Dashboard Quản Lý Tours</h1>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h6 class="card-title">Tổng Tours</h6>
                    <h2 class="mb-0">{{ $stats['total_tours'] ?? 0 }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h6 class="card-title">Tổng Views</h6>
                    <h2 class="mb-0">{{ $stats['total_views'] ?? 0 }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h6 class="card-title">Đánh Giá Trung Bình</h6>
                    <h2 class="mb-0">{{ $stats['avg_rating'] ?? 0 }}/5</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h6 class="card-title">Tác Vụ</h6>
                    <h2 class="mb-0">0</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Tours -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Tours Gần Đây</h5>
                    <a href="{{ route('admin.tours.create') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus me-2"></i>Thêm Tour
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Tên Tour</th>
                                <th>Địa điểm</th>
                                <th>Giá</th>
                                <th>Thời gian</th>
                                <th>Đánh giá</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentTours as $tour)
                            <tr>
                                <td><strong>{{ $tour->title }}</strong></td>
                                <td>{{ $tour->destination }}</td>
                                <td>{{ number_format($tour->price) }}đ</td>
                                <td>{{ $tour->duration }} ngày</td>
                                <td><span class="badge bg-warning">{{ $tour->rating ?? 'N/A' }}</span></td>
                                <td>
                                    <a href="{{ route('admin.tours.show', $tour) }}" class="btn btn-sm btn-info" title="Xem">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.tours.edit', $tour) }}" class="btn btn-sm btn-warning" title="Sửa">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.tours.destroy', $tour) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Xóa" onclick="return confirm('Xác nhận xóa?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">Không có tours</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
