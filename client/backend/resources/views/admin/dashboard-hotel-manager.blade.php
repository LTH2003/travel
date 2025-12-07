@extends('admin.layouts.app')

@section('title', 'Dashboard - Quản Lý Khách Sạn')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 d-inline-block me-2">Quản Lý Khách Sạn</h1>
            <p class="text-muted">Chào mừng {{ auth()->user()->name }}!</p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-primary text-uppercase mb-1 small font-weight-bold">Tổng số khách sạn</div>
                    <div class="h3 mb-0">{{ $totalHotels }}</div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-success text-uppercase mb-1 small font-weight-bold">Tổng phòng</div>
                    <div class="h3 mb-0">{{ $totalRooms }}</div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <a href="{{ route('admin.hotels.create') }}" class="btn btn-info btn-sm text-white">
                        <i class="fas fa-plus"></i> Thêm Khách Sạn Mới
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Hotels -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Khách Sạn Gần Đây</h6>
                    <a href="{{ route('admin.hotels.index') }}" class="btn btn-sm btn-primary">Xem Tất Cả</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Tên Khách Sạn</th>
                                <th>Địa Điểm</th>
                                <th>Giá</th>
                                <th>Số Phòng</th>
                                <th>Đánh Giá</th>
                                <th>Ngày Tạo</th>
                                <th>Hành Động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentHotels as $hotel)
                            <tr>
                                <td class="font-weight-bold">{{ $hotel->name }}</td>
                                <td>{{ $hotel->location }}</td>
                                <td><span class="badge badge-success">{{ number_format($hotel->price) }} đ</span></td>
                                <td><span class="badge badge-info">{{ $hotel->rooms_count ?? $hotel->rooms->count() }}</span></td>
                                <td>
                                    <span class="text-warning">
                                        <i class="fas fa-star"></i> {{ $hotel->rating ?? 'N/A' }}
                                    </span>
                                </td>
                                <td><small class="text-muted">{{ $hotel->created_at?->format('d/m/Y') ?? 'N/A' }}</small></td>
                                <td>
                                    <a href="{{ route('admin.hotels.edit', $hotel) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="{{ route('admin.hotels.rooms.index', $hotel) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-door-open"></i>
                                    </a>
                                    <form action="{{ route('admin.hotels.destroy', $hotel) }}" method="POST" class="d-inline">
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
                                <td colspan="7" class="text-center py-4 text-muted">
                                    <i class="fas fa-inbox mb-2"></i><br>
                                    Chưa có khách sạn nào. <a href="{{ route('admin.hotels.create') }}">Tạo khách sạn mới</a>
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
