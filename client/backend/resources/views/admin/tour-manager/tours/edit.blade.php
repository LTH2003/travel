@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-0">Sửa Tour: {{ $tour->name }}</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.tour-manager.tours.update', $tour) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Tour Name -->
                        <div class="mb-3">
                            <label for="name" class="form-label">Tên Tour <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $tour->name) }}" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Destination -->
                        <div class="mb-3">
                            <label for="destination" class="form-label">Địa điểm <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('destination') is-invalid @enderror" id="destination" name="destination" value="{{ old('destination', $tour->destination) }}" required>
                            @error('destination') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Duration & Price Row -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="duration" class="form-label">Thời gian (ngày) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('duration') is-invalid @enderror" id="duration" name="duration" value="{{ old('duration', $tour->duration) }}" min="1" required>
                                @error('duration') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="price" class="form-label">Giá (đ) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('price') is-invalid @enderror" id="price" name="price" value="{{ old('price', $tour->price) }}" min="0" required>
                                @error('price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label for="description" class="form-label">Mô tả <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4" required>{{ old('description', $tour->description) }}</textarea>
                            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Image -->
                        <div class="mb-3">
                            <label for="image" class="form-label">Hình ảnh</label>
                            @if($tour->image)
                            <div class="mb-2">
                                <img src="{{ asset('storage/' . $tour->image) }}" alt="{{ $tour->name }}" style="max-height: 200px;" class="img-thumbnail">
                            </div>
                            @endif
                            <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image" accept="image/*">
                            <small class="text-muted">Định dạng: JPG, PNG. Kích thước tối đa: 2MB</small>
                            @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Rating -->
                        <div class="mb-3">
                            <label for="rating" class="form-label">Đánh giá (0-5)</label>
                            <input type="number" class="form-control @error('rating') is-invalid @enderror" id="rating" name="rating" value="{{ old('rating', $tour->rating) }}" min="0" max="5" step="0.1">
                            @error('rating') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Actions -->
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Cập nhật
                            </button>
                            <a href="{{ route('admin.tour-manager.tours.index') }}" class="btn btn-secondary">
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
