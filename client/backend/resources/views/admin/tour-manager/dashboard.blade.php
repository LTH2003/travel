@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-0">Dashboard - Tour Manager</h1>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-light">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-0">Tổng Tours</p>
                            <h3 class="mb-0">{{ $stats['total_tours'] }}</h3>
                        </div>
                        <i class="fas fa-map fa-3x text-primary opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-light">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-0">Lượt xem</p>
                            <h3 class="mb-0">{{ $stats['total_views'] }}</h3>
                        </div>
                        <i class="fas fa-eye fa-3x text-info opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-light">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-0">Đánh giá trung bình</p>
                            <h3 class="mb-0">{{ number_format($stats['avg_rating'], 1) }}/5</h3>
                        </div>
                        <i class="fas fa-star fa-3x text-warning opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Tours -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Tours Gần Đây</h5>
                    <a href="{{ route('admin.tour-manager.tours.create') }}" class="btn btn-sm btn-primary">
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
                                <th>Thời gian (ngày)</th>
                                <th>Đánh giá</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentTours as $tour)
                            <tr>
                                <td><strong>{{ $tour->name }}</strong></td>
                                <td>{{ $tour->destination }}</td>
                                <td>{{ number_format($tour->price) }}đ</td>
                                <td>{{ $tour->duration }} ngày</td>
                                <td>
                                    <span class="badge bg-warning">{{ $tour->rating ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.tour-manager.tours.show', $tour) }}" class="btn btn-sm btn-info" title="Xem">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.tour-manager.tours.edit', $tour) }}" class="btn btn-sm btn-warning" title="Sửa">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.tour-manager.tours.destroy', $tour) }}" method="POST" class="d-inline">
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
