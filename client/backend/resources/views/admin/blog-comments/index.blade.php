@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0">Quản lý Bình luận Blog</h1>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-light">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-0">Tổng bình luận</p>
                            <h3 class="mb-0">{{ $stats['total'] ?? 0 }}</h3>
                        </div>
                        <i class="fas fa-comments fa-3x text-primary opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-light">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-0">Đã duyệt</p>
                            <h3 class="mb-0 text-success">{{ $stats['approved'] ?? 0 }}</h3>
                        </div>
                        <i class="fas fa-check-circle fa-3x text-success opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-light">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-0">Chờ duyệt</p>
                            <h3 class="mb-0 text-warning">{{ $stats['pending'] ?? 0 }}</h3>
                        </div>
                        <i class="fas fa-clock fa-3x text-warning opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-light">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-0">Bài viết có BL</p>
                            <h3 class="mb-0 text-info">{{ $blogs->count() ?? 0 }}</h3>
                        </div>
                        <i class="fas fa-newspaper fa-3x text-info opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.blog-comments.index') }}" class="row g-3">
                        <div class="col-md-3">
                            <label for="is_approved" class="form-label">Trạng thái duyệt</label>
                            <select name="is_approved" id="is_approved" class="form-select">
                                <option value="">-- Tất cả --</option>
                                <option value="true" {{ request('is_approved') === 'true' ? 'selected' : '' }}>Đã duyệt</option>
                                <option value="false" {{ request('is_approved') === 'false' ? 'selected' : '' }}>Chờ duyệt</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="blog_id" class="form-label">Bài viết</label>
                            <select name="blog_id" id="blog_id" class="form-select">
                                <option value="">-- Tất cả bài viết --</option>
                                @foreach($blogs as $blog)
                                    <option value="{{ $blog->id }}" {{ request('blog_id') == $blog->id ? 'selected' : '' }}>
                                        {{ $blog->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="search" class="form-label">Tìm kiếm</label>
                            <input type="text" name="search" id="search" class="form-control" 
                                   placeholder="Tìm theo tên user, nội dung..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2 d-flex align-items-end gap-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search me-2"></i>Tìm kiếm
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Comments Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Danh sách bình luận</h5>
                    <div>
                        @if($comments->count() > 0)
                            <form action="{{ route('admin.blog-comments.approve-bulk') }}" method="POST" class="d-inline me-2" id="approveForm">
                                @csrf
                                <input type="hidden" name="ids" id="approveIds" value="">
                                <button type="button" class="btn btn-sm btn-success" onclick="submitBulkAction('approveForm', 'approveIds')">
                                    <i class="fas fa-check me-1"></i>Duyệt chọn
                                </button>
                            </form>
                            <form action="{{ route('admin.blog-comments.reject-bulk') }}" method="POST" class="d-inline me-2" id="rejectForm">
                                @csrf
                                <input type="hidden" name="ids" id="rejectIds" value="">
                                <button type="button" class="btn btn-sm btn-warning" onclick="submitBulkAction('rejectForm', 'rejectIds')">
                                    <i class="fas fa-ban me-1"></i>Từ chối chọn
                                </button>
                            </form>
                            <form action="{{ route('admin.blog-comments.delete-bulk') }}" method="POST" class="d-inline" id="deleteForm">
                                @csrf
                                <input type="hidden" name="ids" id="deleteIds" value="">
                                <button type="button" class="btn btn-sm btn-danger" onclick="submitBulkAction('deleteForm', 'deleteIds')">
                                    <i class="fas fa-trash me-1"></i>Xóa chọn
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="40">
                                    <input type="checkbox" id="selectAll" onchange="toggleSelectAll(this)">
                                </th>
                                <th>Người bình luận</th>
                                <th>Nội dung</th>
                                <th>Bài viết</th>
                                <th>Trạng thái</th>
                                <th>Ngày đăng</th>
                                <th width="100">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($comments as $comment)
                                <tr>
                                    <td>
                                        <input type="checkbox" class="comment-checkbox" value="{{ $comment->id }}">
                                    </td>
                                    <td>
                                        <strong>{{ $comment->user?->name ?? 'Ẩn danh' }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $comment->user?->email ?? 'N/A' }}</small>
                                    </td>
                                    <td>
                                        <div class="text-truncate" title="{{ $comment->content }}">
                                            {{ Str::limit($comment->content, 60) }}
                                        </div>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.blogs.show', $comment->blog_id) }}" class="text-decoration-none">
                                            {{ $comment->blog?->title ?? 'N/A' }}
                                        </a>
                                    </td>
                                    <td>
                                        @if($comment->is_approved)
                                            <span class="badge bg-success">
                                                <i class="fas fa-check me-1"></i>Đã duyệt
                                            </span>
                                        @else
                                            <span class="badge bg-warning">
                                                <i class="fas fa-clock me-1"></i>Chờ duyệt
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <small>{{ $comment->created_at->format('d/m/Y H:i') }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('admin.blog-comments.show', $comment->id) }}" 
                                               class="btn btn-outline-primary" title="Xem">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.blog-comments.edit', $comment->id) }}" 
                                               class="btn btn-outline-warning" title="Chỉnh sửa">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.blog-comments.destroy', $comment->id) }}" 
                                                  method="POST" class="d-inline"
                                                  onsubmit="return confirm('Chắc chắn xóa bình luận này?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger" title="Xóa">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                        Không có bình luận nào
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer d-flex justify-content-between align-items-center">
                    <div class="text-muted">
                        Hiển thị {{ $comments->count() }} trên tổng {{ $comments->total() }} bình luận
                    </div>
                    {{ $comments->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleSelectAll(checkbox) {
    const checkboxes = document.querySelectorAll('.comment-checkbox');
    checkboxes.forEach(cb => cb.checked = checkbox.checked);
}

function submitBulkAction(formId, inputId) {
    const checkboxes = document.querySelectorAll('.comment-checkbox:checked');
    if (checkboxes.length === 0) {
        alert('Vui lòng chọn ít nhất một bình luận');
        return;
    }
    
    const ids = Array.from(checkboxes).map(cb => cb.value);
    document.getElementById(inputId).value = JSON.stringify(ids);
    document.getElementById(formId).submit();
}
</script>

@if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const alert = document.createElement('div');
            alert.className = 'alert alert-success alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3';
            alert.style.zIndex = '9999';
            alert.style.minWidth = '400px';
            alert.innerHTML = `
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.body.appendChild(alert);
            setTimeout(() => alert.remove(), 5000);
        });
    </script>
@endif
@endsection
