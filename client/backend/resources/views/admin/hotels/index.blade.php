@extends('admin.layouts.app')

@section('title', 'Quản lý Khách sạn')
@section('page-title', 'Khách sạn')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Danh sách Khách sạn</h5>
        <a href="{{ route('admin.hotels.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-circle"></i> Thêm Khách sạn
        </a>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên Khách sạn</th>
                    <th>Địa điểm</th>
                    <th>Giá</th>
                    <th>Đánh giá</th>
                    <th>Phòng</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse($hotels as $hotel)
                    <tr>
                        <td><strong>#{{ $hotel->id }}</strong></td>
                        <td><strong>{{ $hotel->name }}</strong></td>
                        <td>{{ $hotel->location }}</td>
                        <td><strong>{{ number_format($hotel->price) }} VNĐ</strong></td>
                        <td>
                            @if($hotel->rating)
                                <i class="bi bi-star-fill" style="color: #ffc107;"></i>
                                {{ $hotel->rating }}/5
                            @else
                                <em class="text-muted">N/A</em>
                            @endif
                        </td>
                        <td>
                            @if($hotel->rooms)
                                <a href="{{ route('admin.hotels.rooms.index', $hotel) }}" class="badge bg-info">
                                    {{ $hotel->rooms->count() }} phòng
                                </a>
                            @else
                                <em class="text-muted">0 phòng</em>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.hotels.show', $hotel) }}" class="btn btn-sm btn-info">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('admin.hotels.edit', $hotel) }}" class="btn btn-sm btn-warning">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('admin.hotels.destroy', $hotel) }}" method="POST" style="display: inline;" onsubmit="return confirm('Bạn chắc chắn muốn xóa?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            Không có khách sạn nào
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($hotels instanceof \Illuminate\Pagination\Paginator)
<div class="mt-4">
    {{ $hotels->links() }}
</div>
@endif
@endsection
