@extends('admin.layouts.app')

@section('title', 'Chi tiết Blog')
@section('page-title', 'Chi tiết Blog')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Chi tiết: {{ $blog->title }}</h5>
                <div class="gap-2 d-flex">
                    <a href="{{ route('admin.blogs.edit', $blog) }}" class="btn btn-warning btn-sm">
                        <i class="bi bi-pencil"></i> Chỉnh sửa
                    </a>
                    <a href="{{ route('admin.blogs.index') }}" class="btn btn-secondary btn-sm">
                        <i class="bi bi-arrow-left"></i> Quay lại
                    </a>
                </div>
            </div>
            <div class="card-body">
                @if($blog->image)
                    <div class="mb-3">
                        <img src="{{ $blog->image }}" alt="{{ $blog->title }}" class="img-fluid rounded" style="max-height: 300px;">
                    </div>
                @endif

                <div class="mb-3">
                    <label class="form-label">Tiêu đề</label>
                    <p class="form-control-plaintext fw-bold" style="font-size: 1.5rem;">{{ $blog->title }}</p>
                </div>

                <div class="mb-3">
                    <label class="form-label">Slug</label>
                    <p class="form-control-plaintext">{{ $blog->slug }}</p>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Danh mục</label>
                            <p class="form-control-plaintext">
                                <span class="badge bg-info">{{ $blog->category }}</span>
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Tác giả</label>
                            <p class="form-control-plaintext">
                                @if($blog->author && isset($blog->author['name']))
                                    {{ $blog->author['name'] }}
                                @else
                                    <em class="text-muted">N/A</em>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Thời gian đọc</label>
                            <p class="form-control-plaintext">
                                @if($blog->read_time)
                                    {{ $blog->read_time }} phút
                                @else
                                    <em class="text-muted">N/A</em>
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Lượt xem</label>
                            <p class="form-control-plaintext">
                                <span class="badge bg-secondary">{{ $blog->views ?? 0 }}</span>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Trích dẫn</label>
                    <p class="form-control-plaintext">{{ $blog->excerpt }}</p>
                </div>

                <div class="mb-3">
                    <label class="form-label">Nội dung</label>
                    <div class="border p-3 rounded" style="background-color: #f8f9fa;">
                        {{ $blog->content }}
                    </div>
                </div>

                @if($blog->tags && count($blog->tags) > 0)
                    <div class="mb-3">
                        <label class="form-label">Tags</label>
                        <p class="form-control-plaintext">
                            @foreach($blog->tags as $tag)
                                <span class="badge bg-light text-dark me-2">{{ $tag }}</span>
                            @endforeach
                        </p>
                    </div>
                @endif

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Ngày đăng</label>
                            <p class="form-control-plaintext">
                                @if($blog->published_at)
                                    {{ $blog->published_at->format('d/m/Y H:i') }}
                                @else
                                    <em class="text-muted">Chưa đăng</em>
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Ngày tạo</label>
                            <p class="form-control-plaintext">
                                @if($blog->created_at)
                                    {{ $blog->created_at->format('d/m/Y H:i:s') }}
                                @else
                                    <em>N/A</em>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <a href="{{ route('admin.blogs.edit', $blog) }}" class="btn btn-primary">
                        <i class="bi bi-pencil"></i> Chỉnh sửa
                    </a>
                    <form action="{{ route('admin.blogs.destroy', $blog) }}" method="POST" style="display: inline;" onsubmit="return confirm('Bạn chắc chắn muốn xóa blog này?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-trash"></i> Xóa
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
