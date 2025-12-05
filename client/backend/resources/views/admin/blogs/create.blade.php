@extends('admin.layouts.app')

@section('title', 'Tạo Blog Mới')
@section('page-title', 'Tạo Blog')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Tạo Blog Mới</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.blogs.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label" for="title">Tiêu đề <span class="text-danger">*</span></label>
                        <input 
                            type="text" 
                            class="form-control @error('title') is-invalid @enderror" 
                            id="title" 
                            name="title" 
                            value="{{ old('title') }}"
                            placeholder="Nhập tiêu đề blog"
                            required
                        >
                        @error('title')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="slug">Slug <span class="text-danger">*</span></label>
                        <input 
                            type="text" 
                            class="form-control @error('slug') is-invalid @enderror" 
                            id="slug" 
                            name="slug" 
                            value="{{ old('slug') }}"
                            placeholder="blog-title"
                            required
                        >
                        @error('slug')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="excerpt">Trích dẫn <span class="text-danger">*</span></label>
                        <textarea 
                            class="form-control @error('excerpt') is-invalid @enderror" 
                            id="excerpt" 
                            name="excerpt" 
                            rows="2"
                            placeholder="Nhập trích dẫn ngắn"
                            required
                        >{{ old('excerpt') }}</textarea>
                        @error('excerpt')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="content">Nội dung <span class="text-danger">*</span></label>
                        <textarea 
                            class="form-control @error('content') is-invalid @enderror" 
                            id="content" 
                            name="content" 
                            rows="6"
                            placeholder="Nhập nội dung blog"
                            required
                        >{{ old('content') }}</textarea>
                        @error('content')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="author">Tác giả</label>
                                <input 
                                    type="text" 
                                    class="form-control @error('author') is-invalid @enderror" 
                                    id="author" 
                                    name="author" 
                                    value="{{ old('author') }}"
                                    placeholder="Tên tác giả"
                                >
                                @error('author')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="category">Danh mục <span class="text-danger">*</span></label>
                                <input 
                                    type="text" 
                                    class="form-control @error('category') is-invalid @enderror" 
                                    id="category" 
                                    name="category" 
                                    value="{{ old('category') }}"
                                    placeholder="Danh mục"
                                    required
                                >
                                @error('category')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="image">Hình ảnh (URL)</label>
                                <input 
                                    type="text" 
                                    class="form-control @error('image') is-invalid @enderror" 
                                    id="image" 
                                    name="image" 
                                    value="{{ old('image') }}"
                                    placeholder="https://..."
                                >
                                @error('image')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="tags">Tags (cách bằng dấu phẩy)</label>
                                <input 
                                    type="text" 
                                    class="form-control @error('tags') is-invalid @enderror" 
                                    id="tags" 
                                    name="tags" 
                                    value="{{ old('tags') }}"
                                    placeholder="tag1, tag2, tag3"
                                >
                                @error('tags')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="published_at">Ngày đăng</label>
                                <input 
                                    type="date" 
                                    class="form-control @error('published_at') is-invalid @enderror" 
                                    id="published_at" 
                                    name="published_at" 
                                    value="{{ old('published_at') }}"
                                >
                                @error('published_at')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="read_time">Thời gian đọc (phút)</label>
                                <input 
                                    type="number" 
                                    class="form-control @error('read_time') is-invalid @enderror" 
                                    id="read_time" 
                                    name="read_time" 
                                    value="{{ old('read_time') }}"
                                    min="1"
                                >
                                @error('read_time')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Tạo Blog
                        </button>
                        <a href="{{ route('admin.blogs.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Hủy
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
