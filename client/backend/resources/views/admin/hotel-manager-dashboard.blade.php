@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4">Dashboard Quản Lý Khách Sạn</h1>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h6 class="card-title">Tổng Khách Sạn</h6>
                    <h2 class="mb-0">{{ $stats['total_hotels'] ?? 0 }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h6 class="card-title">Tổng Đánh Giá</h6>
                    <h2 class="mb-0">{{ $stats['total_reviews'] ?? 0 }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h6 class="card-title">Đánh Giá Trung Bình</h6>
                    <h2 class="mb-0">{{ $stats['average_rating'] ?? 0 }}/5</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Hotels -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Khách Sạn Gần Đây</h5>
                    <a href="{{ route('admin.hotels.create') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus me-2"></i>Thêm Khách Sạn
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Tên Khách Sạn</th>
                                <th>Địa chỉ</th>
                                <th>Thành phố</th>
                                <th>Phòng</th>
                                <th>Đánh giá</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentHotels as $hotel)
                            <tr>
                                <td><strong>{{ $hotel->name }}</strong></td>
                                <td>{{ Str::limit($hotel->address, 40) }}</td>
                                <td>{{ $hotel->city }}</td>
                                <td><span class="badge bg-info">{{ $hotel->rooms_count ?? 0 }}</span></td>
                                <td><span class="badge bg-warning">{{ $hotel->rating ?? 'N/A' }}</span></td>
                                <td>
                                    <a href="{{ route('admin.hotels.show', $hotel) }}" class="btn btn-sm btn-info" title="Xem">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.hotels.edit', $hotel) }}" class="btn btn-sm btn-warning" title="Sửa">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.hotels.destroy', $hotel) }}" method="POST" class="d-inline">
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
                                <td colspan="6" class="text-center text-muted py-4">Không có khách sạn</td>
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
