@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h1 class="h3 mb-0">Quản Lý Khách Sạn</h1>
            <a href="{{ route('admin.hotel-manager.hotels.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Thêm Khách Sạn Mới
            </a>
        </div>
    </div>

    <!-- Search & Filter -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.hotel-manager.hotels.index') }}" method="GET" class="row g-3">
                        <div class="col-md-6">
                            <input type="text" name="search" class="form-control" placeholder="Tìm khách sạn..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <input type="text" name="city" class="form-control" placeholder="Thành phố..." value="{{ request('city') }}">
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search me-2"></i>Tìm
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Hotels Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
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
                            @forelse($hotels as $hotel)
                            <tr>
                                <td><strong>{{ $hotel->name }}</strong></td>
                                <td>{{ Str::limit($hotel->address, 40) }}</td>
                                <td>{{ $hotel->city }}</td>
                                <td><span class="badge bg-info">{{ $hotel->rooms_count ?? 0 }}</span></td>
                                <td>
                                    <span class="badge bg-warning">{{ $hotel->rating ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.hotel-manager.hotels.show', $hotel) }}" class="btn btn-sm btn-info" title="Xem">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.hotel-manager.hotels.edit', $hotel) }}" class="btn btn-sm btn-warning" title="Sửa">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.hotel-manager.hotels.destroy', $hotel) }}" method="POST" class="d-inline">
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
                <div class="card-footer">
                    {{ $hotels->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
