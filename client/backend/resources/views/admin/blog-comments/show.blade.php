@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0">Chi tiết Bình luận</h1>
                <a href="{{ route('admin.blog-comments.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Quay lại
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Comment Content -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Nội dung bình luận</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label text-muted">Người bình luận</label>
                        <p class="h5">
                            {{ $comment->user?->name ?? 'Ẩn danh' }}
                            @if($comment->user)
                                <small class="text-muted">({{ $comment->user->email }})</small>
                            @endif
                        </p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted">Bài viết</label>
                        <p>
                            <a href="{{ route('admin.blogs.show', $comment->blog_id) }}" class="text-decoration-none">
                                {{ $comment->blog?->title ?? 'Bài viết không tồn tại' }}
                            </a>
                        </p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted">Nội dung</label>
                        <div class="bg-light p-3 rounded border">
                            {{ $comment->content }}
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label text-muted">Ngày đăng</label>
                            <p>{{ $comment->created_at->format('d/m/Y H:i:s') }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Cập nhật lần cuối</label>
                            <p>{{ $comment->updated_at->format('d/m/Y H:i:s') }}</p>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted">Trạng thái duyệt</label>
                        <p>
                            @if($comment->is_approved)
                                <span class="badge bg-success">
                                    <i class="fas fa-check me-1"></i>Đã duyệt
                                </span>
                            @else
                                <span class="badge bg-warning">
                                    <i class="fas fa-clock me-1"></i>Chờ duyệt
                                </span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="card">
                <div class="card-body">
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.blog-comments.edit', $comment->id) }}" class="btn btn-primary">
                            <i class="fas fa-edit me-2"></i>Chỉnh sửa
                        </a>
                        <form action="{{ route('admin.blog-comments.destroy', $comment->id) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('Chắc chắn xóa bình luận này?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash me-2"></i>Xóa
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Info -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Thông tin</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label text-muted d-block">ID</label>
                        <code>{{ $comment->id }}</code>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted d-block">Blog ID</label>
                        <code>{{ $comment->blog_id }}</code>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted d-block">User ID</label>
                        <code>{{ $comment->user_id ?? 'N/A' }}</code>
                    </div>

                    @if($comment->parent_id)
                        <div class="mb-3">
                            <label class="form-label text-muted d-block">Reply to Comment</label>
                            <a href="{{ route('admin.blog-comments.show', $comment->parent_id) }}" class="badge bg-info">
                                Comment #{{ $comment->parent_id }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
