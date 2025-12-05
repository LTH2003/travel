@extends('admin.layouts.app')

@section('title', 'Chỉnh sửa Tour')
@section('page-title', 'Chỉnh sửa Tour')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Chỉnh sửa Tour: {{ $tour->name }}</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.tours.update', $tour) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label" for="name">Tên Tour <span class="text-danger">*</span></label>
                        <input 
                            type="text" 
                            class="form-control @error('name') is-invalid @enderror" 
                            id="name" 
                            name="name" 
                            value="{{ old('name', $tour->name) }}"
                            placeholder="Nhập tên tour"
                            required
                        >
                        @error('name')
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
                            value="{{ old('slug', $tour->slug) }}"
                            placeholder="tour-name"
                            required
                        >
                        @error('slug')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="destination">Điểm đến <span class="text-danger">*</span></label>
                        <input 
                            type="text" 
                            class="form-control @error('destination') is-invalid @enderror" 
                            id="destination" 
                            name="destination" 
                            value="{{ old('destination', $tour->destination) }}"
                            placeholder="Nhập điểm đến"
                            required
                        >
                        @error('destination')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="description">Mô tả <span class="text-danger">*</span></label>
                        <textarea 
                            class="form-control @error('description') is-invalid @enderror" 
                            id="description" 
                            name="description" 
                            rows="4"
                            placeholder="Nhập mô tả tour"
                            required
                        >{{ old('description', $tour->description) }}</textarea>
                        @error('description')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="price">Giá (VNĐ) <span class="text-danger">*</span></label>
                                <input 
                                    type="number" 
                                    class="form-control @error('price') is-invalid @enderror" 
                                    id="price" 
                                    name="price" 
                                    value="{{ old('price', $tour->price) }}"
                                    placeholder="0"
                                    min="0"
                                    required
                                >
                                @error('price')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="duration">Thời gian <span class="text-danger">*</span></label>
                                <input 
                                    type="text" 
                                    class="form-control @error('duration') is-invalid @enderror" 
                                    id="duration" 
                                    name="duration" 
                                    value="{{ old('duration', $tour->duration) }}"
                                    placeholder="3 ngày 2 đêm"
                                    required
                                >
                                @error('duration')
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
                                    value="{{ old('image', $tour->image) }}"
                                    placeholder="https://..."
                                >
                                @error('image')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="rating">Rating (0-5)</label>
                                <input 
                                    type="number" 
                                    class="form-control @error('rating') is-invalid @enderror" 
                                    id="rating" 
                                    name="rating" 
                                    value="{{ old('rating', $tour->rating) }}"
                                    min="0"
                                    max="5"
                                    step="0.1"
                                >
                                @error('rating')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Cập nhật
                        </button>
                        <a href="{{ route('admin.tours.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Hủy
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
