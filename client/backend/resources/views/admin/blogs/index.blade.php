@extends('admin.layouts.app')

@section('title', 'Quản lý Blog')
@section('page-title', 'Blog')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Danh sách Blog</h5>
        <a href="{{ route('admin.blogs.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-circle"></i> Thêm Blog
        </a>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tiêu đề</th>
                    <th>Danh mục</th>
                    <th>Tác giả</th>
                    <th>Ngày đăng</th>
                    <th>Lượt xem</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse($blogs as $blog)
                    <tr>
                        <td><strong>#{{ $blog->id }}</strong></td>
                        <td>{{ $blog->title }}</td>
                        <td>
                            <span class="badge bg-info">{{ $blog->category }}</span>
                        </td>
                        <td>
                            @if($blog->author && isset($blog->author['name']))
                                {{ $blog->author['name'] }}
                            @else
                                <em class="text-muted">N/A</em>
                            @endif
                        </td>
                        <td>
                            @if($blog->published_at)
                                {{ $blog->published_at->format('d/m/Y') }}
                            @else
                                <em class="text-muted">Chưa đăng</em>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-secondary">{{ $blog->views ?? 0 }}</span>
                        </td>
                        <td>
                            <a href="{{ route('admin.blogs.show', $blog) }}" class="btn btn-sm btn-info">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('admin.blogs.edit', $blog) }}" class="btn btn-sm btn-warning">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('admin.blogs.destroy', $blog) }}" method="POST" style="display: inline;" onsubmit="return confirm('Bạn chắc chắn muốn xóa?');">
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
                            <i class="bi bi-inbox" style="font-size: 2rem;"></i><br>
                            Chưa có blog nào
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($blogs->hasPages())
        <div class="card-body">
            {{ $blogs->links() }}
        </div>
    @endif
</div>
@endsection
