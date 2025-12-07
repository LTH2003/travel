@extends('admin.layouts.app')

@section('title', 'Chỉnh sửa Khách sạn')
@section('page-title', 'Chỉnh sửa Khách sạn')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Chỉnh sửa Khách sạn: {{ $hotel->name }}</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.hotels.update', $hotel) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label" for="name">Tên Khách sạn <span class="text-danger">*</span></label>
                        <input 
                            type="text" 
                            class="form-control @error('name') is-invalid @enderror" 
                            id="name" 
                            name="name" 
                            value="{{ old('name', $hotel->name) }}"
                            placeholder="Nhập tên khách sạn"
                            required
                        >
                        @error('name')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="location">Địa điểm <span class="text-danger">*</span></label>
                        <input 
                            type="text" 
                            class="form-control @error('location') is-invalid @enderror" 
                            id="location" 
                            name="location" 
                            value="{{ old('location', $hotel->location) }}"
                            placeholder="Nhập địa điểm"
                            required
                        >
                        @error('location')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="description">Mô tả</label>
                        <textarea 
                            class="form-control @error('description') is-invalid @enderror" 
                            id="description" 
                            name="description" 
                            rows="4"
                            placeholder="Nhập mô tả khách sạn"
                        >{{ old('description', $hotel->description) }}</textarea>
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
                                    value="{{ old('price', $hotel->price) }}"
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
                                    value="{{ old('original_price', $hotel->original_price) }}"
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
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label" for="rating">Đánh giá (0-5)</label>
                                <input 
                                    type="number" 
                                    class="form-control @error('rating') is-invalid @enderror" 
                                    id="rating" 
                                    name="rating" 
                                    value="{{ old('rating', $hotel->rating) }}"
                                    min="0"
                                    max="5"
                                    step="0.1"
                                >
                                @error('rating')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label" for="reviews">Số lượng đánh giá</label>
                                <input 
                                    type="number" 
                                    class="form-control @error('reviews') is-invalid @enderror" 
                                    id="reviews" 
                                    name="reviews" 
                                    value="{{ old('reviews', $hotel->reviews) }}"
                                    min="0"
                                >
                                @error('reviews')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label" for="image">Hình ảnh (URL)</label>
                                <input 
                                    type="text" 
                                    class="form-control @error('image') is-invalid @enderror" 
                                    id="image" 
                                    name="image" 
                                    value="{{ old('image', $hotel->image) }}"
                                    placeholder="https://..."
                                >
                                @error('image')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label" for="check_in">Giờ nhận phòng</label>
                                <input 
                                    type="text" 
                                    class="form-control @error('check_in') is-invalid @enderror" 
                                    id="check_in" 
                                    name="check_in" 
                                    value="{{ old('check_in', $hotel->check_in) }}"
                                    placeholder="vd: 14:00"
                                >
                                @error('check_in')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label" for="check_out">Giờ trả phòng</label>
                                <input 
                                    type="text" 
                                    class="form-control @error('check_out') is-invalid @enderror" 
                                    id="check_out" 
                                    name="check_out" 
                                    value="{{ old('check_out', $hotel->check_out) }}"
                                    placeholder="vd: 11:00"
                                >
                                @error('check_out')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label" for="cancellation">Chính sách hủy phòng</label>
                                <input 
                                    type="text" 
                                    class="form-control @error('cancellation') is-invalid @enderror" 
                                    id="cancellation" 
                                    name="cancellation" 
                                    value="{{ old('cancellation', $hotel->cancellation) }}"
                                    placeholder="vd: Miễn phí hủy 24h trước"
                                >
                                @error('cancellation')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="children">Chính sách trẻ em</label>
                        <textarea 
                            class="form-control @error('children') is-invalid @enderror" 
                            id="children" 
                            name="children" 
                            rows="2"
                            placeholder="Nhập chính sách trẻ em"
                        >{{ old('children', $hotel->children) }}</textarea>
                        @error('children')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Cập nhật
                        </button>
                        <a href="{{ route('admin.hotels.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Hủy
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
