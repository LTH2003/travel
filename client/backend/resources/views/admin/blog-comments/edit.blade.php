@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0">Chỉnh sửa Bình luận</h1>
                <a href="{{ route('admin.blog-comments.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Quay lại
                </a>
            </div>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Có lỗi xảy ra!</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Chỉnh sửa nội dung</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.blog-comments.update', $comment->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Người bình luận -->
                        <div class="mb-3">
                            <label class="form-label">Người bình luận</label>
                            <input type="text" class="form-control" disabled 
                                   value="{{ $comment->user?->name ?? 'Ẩn danh' }} ({{ $comment->user?->email ?? 'N/A' }})">
                            <small class="text-muted">Không thể thay đổi</small>
                        </div>

                        <!-- Bài viết -->
                        <div class="mb-3">
                            <label class="form-label">Bài viết</label>
                            <input type="text" class="form-control" disabled 
                                   value="{{ $comment->blog?->title ?? 'Bài viết không tồn tại' }}">
                            <small class="text-muted">Không thể thay đổi</small>
                        </div>

                        <!-- Nội dung -->
                        <div class="mb-3">
                            <label class="form-label">Nội dung bình luận <span class="text-danger">*</span></label>
                            <textarea name="content" class="form-control @error('content') is-invalid @enderror" 
                                      rows="6" required>{{ old('content', $comment->content) }}</textarea>
                            @error('content')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <small class="text-muted d-block mt-2">Tối thiểu 3 ký tự, tối đa 5000 ký tự</small>
                        </div>

                        <!-- Trạng thái duyệt -->
                        <div class="mb-3">
                            <label class="form-label">Trạng thái duyệt <span class="text-danger">*</span></label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="is_approved" id="approved_yes" 
                                       value="1" {{ old('is_approved', $comment->is_approved) ? 'checked' : '' }}>
                                <label class="form-check-label" for="approved_yes">
                                    <span class="badge bg-success">Đã duyệt</span> - Cho phép hiển thị trên website
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="is_approved" id="approved_no" 
                                       value="0" {{ !old('is_approved', $comment->is_approved) ? 'checked' : '' }}>
                                <label class="form-check-label" for="approved_no">
                                    <span class="badge bg-warning">Chờ duyệt</span> - Ẩn khỏi website
                                </label>
                            </div>
                        </div>

                        <!-- Ngày đăng -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label text-muted">Ngày đăng</label>
                                    <p>{{ $comment->created_at->format('d/m/Y H:i:s') }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label text-muted">Cập nhật lần cuối</label>
                                    <p>{{ $comment->updated_at->format('d/m/Y H:i:s') }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Lưu thay đổi
                            </button>
                            <a href="{{ route('admin.blog-comments.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Hủy
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Thông tin</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label text-muted">ID</label>
                        <code>{{ $comment->id }}</code>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">Blog ID</label>
                        <code>{{ $comment->blog_id }}</code>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">User ID</label>
                        <code>{{ $comment->user_id ?? 'N/A' }}</code>
                    </div>
                </div>
            </div>

            <!-- Delete Button -->
            <div class="card mt-3">
                <div class="card-body">
                    <form action="{{ route('admin.blog-comments.destroy', $comment->id) }}" method="POST"
                          onsubmit="return confirm('Chắc chắn xóa bình luận này? Hành động này không thể hoàn tác.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="fas fa-trash me-2"></i>Xóa bình luận
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

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
