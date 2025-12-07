@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-0">Sửa Khách Sạn: {{ $hotel->name }}</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.hotel-manager.hotels.update', $hotel) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Hotel Name -->
                        <div class="mb-3">
                            <label for="name" class="form-label">Tên Khách Sạn <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $hotel->name) }}" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Address & City Row -->
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label for="address" class="form-label">Địa chỉ <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('address') is-invalid @enderror" id="address" name="address" value="{{ old('address', $hotel->address) }}" required>
                                @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="city" class="form-label">Thành phố <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('city') is-invalid @enderror" id="city" name="city" value="{{ old('city', $hotel->city) }}" required>
                                @error('city') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label for="description" class="form-label">Mô tả <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4" required>{{ old('description', $hotel->description) }}</textarea>
                            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Amenities -->
                        <div class="mb-3">
                            <label for="amenities" class="form-label">Tiện nghi (cách nhau bởi dấu phẩy)</label>
                            <input type="text" class="form-control @error('amenities') is-invalid @enderror" id="amenities" name="amenities" value="{{ old('amenities', $hotel->amenities) }}" placeholder="Wi-Fi, Hồ bơi, Quầy bar...">
                            @error('amenities') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Image -->
                        <div class="mb-3">
                            <label for="image" class="form-label">Hình ảnh</label>
                            @if($hotel->image)
                            <div class="mb-2">
                                <img src="{{ asset('storage/' . $hotel->image) }}" alt="{{ $hotel->name }}" style="max-height: 200px;" class="img-thumbnail">
                            </div>
                            @endif
                            <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image" accept="image/*">
                            <small class="text-muted">Định dạng: JPG, PNG. Kích thước tối đa: 2MB</small>
                            @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Rating -->
                        <div class="mb-3">
                            <label for="rating" class="form-label">Đánh giá (0-5)</label>
                            <input type="number" class="form-control @error('rating') is-invalid @enderror" id="rating" name="rating" value="{{ old('rating', $hotel->rating) }}" min="0" max="5" step="0.1">
                            @error('rating') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Actions -->
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Cập nhật
                            </button>
                            <a href="{{ route('admin.hotel-manager.hotels.index') }}" class="btn btn-secondary">
                                Hủy
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
