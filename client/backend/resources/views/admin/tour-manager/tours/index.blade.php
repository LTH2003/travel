@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h1 class="h3 mb-0">Quản Lý Tours</h1>
            <a href="{{ route('admin.tour-manager.tours.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Thêm Tour Mới
            </a>
        </div>
    </div>

    <!-- Search & Filter -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.tour-manager.tours.index') }}" method="GET" class="row g-3">
                        <div class="col-md-6">
                            <input type="text" name="search" class="form-control" placeholder="Tìm tours..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <select name="sort" class="form-select">
                                <option value="latest" @selected(request('sort') === 'latest')>Mới nhất</option>
                                <option value="popular" @selected(request('sort') === 'popular')>Phổ biến nhất</option>
                            </select>
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

    <!-- Tours Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
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
                            @forelse($tours as $tour)
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
                <div class="card-footer">
                    {{ $tours->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
