@extends('admin.layouts.app')

@section('title', 'Tạo Tour Mới')
@section('page-title', 'Tạo Tour')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Tạo Tour Mới</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.tours.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label" for="title">Tên Tour <span class="text-danger">*</span></label>
                        <input 
                            type="text" 
                            class="form-control @error('title') is-invalid @enderror" 
                            id="title" 
                            name="title" 
                            value="{{ old('title') }}"
                            placeholder="Nhập tên tour"
                            required
                        >
                        @error('title')
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
                            value="{{ old('destination') }}"
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
                        >{{ old('description') }}</textarea>
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
                                    value="{{ old('price') }}"
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
                                <label class="form-label" for="original_price">Giá gốc (VNĐ)</label>
                                <input 
                                    type="number" 
                                    class="form-control @error('original_price') is-invalid @enderror" 
                                    id="original_price" 
                                    name="original_price" 
                                    value="{{ old('original_price') }}"
                                    placeholder="0"
                                    min="0"
                                >
                                @error('original_price')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="duration">Thời gian <span class="text-danger">*</span></label>
                                <input 
                                    type="text" 
                                    class="form-control @error('duration') is-invalid @enderror" 
                                    id="duration" 
                                    name="duration" 
                                    value="{{ old('duration') }}"
                                    placeholder="3 ngày 2 đêm"
                                    required
                                >
                                @error('duration')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="category">Loại Tour <span class="text-danger">*</span></label>
                                <select 
                                    class="form-control @error('category') is-invalid @enderror" 
                                    id="category" 
                                    name="category"
                                    required
                                >
                                    <option value="">-- Chọn loại tour --</option>
                                    <option value="trong_nuoc" {{ old('category') === 'trong_nuoc' ? 'selected' : '' }}>Trong nước</option>
                                    <option value="quoc_te" {{ old('category') === 'quoc_te' ? 'selected' : '' }}>Quốc tế</option>
                                </select>
                                @error('category')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label" for="max_guests">Số khách tối đa</label>
                                <input 
                                    type="number" 
                                    class="form-control @error('max_guests') is-invalid @enderror" 
                                    id="max_guests" 
                                    name="max_guests" 
                                    value="{{ old('max_guests') }}"
                                    min="1"
                                >
                                @error('max_guests')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label" for="rating">Rating (0-5)</label>
                                <input 
                                    type="number" 
                                    class="form-control @error('rating') is-invalid @enderror" 
                                    id="rating" 
                                    name="rating" 
                                    value="{{ old('rating') }}"
                                    min="0"
                                    max="5"
                                    step="0.1"
                                >
                                @error('rating')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
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
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Tạo Tour
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
