@extends('admin.layouts.app')

@section('title', 'Quản lý Tour')
@section('page-title', 'Tour')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Danh sách Tour</h5>
        <a href="{{ route('admin.tours.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-circle"></i> Thêm Tour
        </a>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên Tour</th>
                    <th>Điểm đến</th>
                    <th>Giá</th>
                    <th>Thời gian</th>
                    <th>Rating</th>
                    <th>Người tạo</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tours as $tour)
                    <tr>
                        <td><strong>#{{ $tour->id }}</strong></td>
                        <td>{{ $tour->title }}</td>
                        <td>{{ $tour->destination }}</td>
                        <td><strong>{{ number_format($tour->price) }} VNĐ</strong></td>
                        <td>{{ $tour->duration }}</td>
                        <td>
                            @if($tour->rating)
                                <i class="bi bi-star-fill" style="color: #ffc107;"></i>
                                {{ $tour->rating }}/5
                            @else
                                <em class="text-muted">N/A</em>
                            @endif
                        </td>
                        <td>
                            @if($tour->creator)
                                <span class="badge bg-secondary">{{ $tour->creator->name }}</span><br>
                                <small class="text-muted">{{ $tour->created_at->format('d/m/Y H:i') }}</small>
                            @else
                                <em class="text-muted">N/A</em>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.tours.show', $tour) }}" class="btn btn-sm btn-info">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('admin.tours.edit', $tour) }}" class="btn btn-sm btn-warning">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('admin.tours.destroy', $tour) }}" method="POST" style="display: inline;" onsubmit="return confirm('Bạn chắc chắn muốn xóa?');">
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
                        <td colspan="8" class="text-center text-muted py-4">
                            <i class="bi bi-inbox" style="font-size: 2rem;"></i><br>
                            Chưa có tour nào
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($tours->hasPages())
        <div class="card-body">
            {{ $tours->links() }}
        </div>
    @endif
</div>
@endsection
