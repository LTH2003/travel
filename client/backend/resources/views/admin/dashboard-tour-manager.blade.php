@extends('admin.layouts.app')

@section('title', 'Dashboard - Quản Lý Tour')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 d-inline-block me-2">Quản Lý Tour</h1>
            <p class="text-muted">Chào mừng {{ auth()->user()->name }}!</p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-primary text-uppercase mb-1 small font-weight-bold">Tổng số tour</div>
                    <div class="h3 mb-0">{{ $totalTours }}</div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-success text-uppercase mb-1 small font-weight-bold">Tour mới</div>
                    <div class="h3 mb-0">{{ $recentTours->where('created_at', '>', now()->subDays(7))->count() }}</div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <a href="{{ route('admin.tours.create') }}" class="btn btn-info btn-sm text-white">
                        <i class="fas fa-plus"></i> Thêm Tour Mới
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Tours -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Tours Gần Đây</h6>
                    <a href="{{ route('admin.tours.index') }}" class="btn btn-sm btn-primary">Xem Tất Cả</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Tên Tour</th>
                                <th>Điểm Đến</th>
                                <th>Giá</th>
                                <th>Đánh Giá</th>
                                <th>Ngày Tạo</th>
                                <th>Hành Động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentTours as $tour)
                            <tr>
                                <td class="font-weight-bold">{{ $tour->title }}</td>
                                <td>{{ $tour->destination }}</td>
                                <td><span class="font-weight-bold">{{ number_format($tour->price) }} đ</span></td>
                                <td>
                                    <span class="text-warning">
                                        <i class="fas fa-star"></i> {{ $tour->rating ?? 'N/A' }}
                                    </span>
                                </td>
                                <td><small class="text-muted">{{ $tour->created_at?->format('d/m/Y') ?? 'N/A' }}</small></td>
                                <td>
                                    <a href="{{ route('admin.tours.edit', $tour) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.tours.destroy', $tour) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Bạn chắc chứ?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    <i class="fas fa-inbox mb-2"></i><br>
                                    Chưa có tour nào. <a href="{{ route('admin.tours.create') }}">Tạo tour mới</a>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}
.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}
.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}
</style>
@endsection
