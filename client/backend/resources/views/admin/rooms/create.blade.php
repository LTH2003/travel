@extends('admin.layouts.app')

@section('title', 'Add New Room')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.hotels.index') }}">Hotels</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.hotels.show', $hotel) }}">{{ $hotel->name }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.hotels.rooms.index', $hotel) }}">Rooms</a></li>
                    <li class="breadcrumb-item active">New</li>
                </ol>
            </nav>
            <h1 class="h2 mb-0">
                <i class="bi bi-door-closed me-2"></i> Add New Room
            </h1>
            <small class="text-muted">{{ $hotel->name }} - Hotel ID: {{ $hotel->id }}</small>
        </div>
        <a href="{{ route('admin.hotels.rooms.index', $hotel) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i> Back to Rooms
        </a>
    </div>

    <!-- Error Messages -->
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-circle me-2"></i>
            <strong>Validation Errors!</strong>
            <ul class="mt-2 mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Main Form Section -->
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-gradient" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                    <h5 class="mb-0">
                        <i class="bi bi-plus-circle me-2"></i> Room Details
                    </h5>
                </div>

                <div class="card-body p-4">
                    <form action="{{ route('admin.hotels.rooms.store', $hotel) }}" method="POST" id="roomForm">
                        @csrf

                        <!-- Section 1: Basic Information -->
                        <div class="mb-5">
                            <h6 class="text-uppercase fw-bold text-muted mb-3">
                                <i class="bi bi-info-circle me-2"></i> Basic Information
                            </h6>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label fw-bold">
                                        Room Name <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="name" id="name" 
                                           value="{{ old('name') }}" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           placeholder="e.g., Deluxe Double Room"
                                           required>
                                    @error('name')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="capacity" class="form-label fw-bold">
                                        Capacity (Guests) <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" name="capacity" id="capacity" 
                                           value="{{ old('capacity') }}" 
                                           min="1" max="10"
                                           class="form-control @error('capacity') is-invalid @enderror" 
                                           placeholder="Number of guests"
                                           required>
                                    @error('capacity')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Advanced Details (Collapsible) -->
                            <div class="card border-light mb-3">
                                <div class="card-header bg-light">
                                    <a class="card-link text-decoration-none" data-bs-toggle="collapse" href="#advancedDetails" role="button">
                                        <i class="bi bi-chevron-down"></i> 
                                        <strong>Thông tin chi tiết (Tùy chọn)</strong>
                                    </a>
                                </div>
                                <div id="advancedDetails" class="collapse">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="size" class="form-label">Diện tích phòng (m²)</label>
                                                <input type="number" name="size" id="size" 
                                                       value="{{ old('size') }}" 
                                                       step="0.01" min="0"
                                                       class="form-control @error('size') is-invalid @enderror" 
                                                       placeholder="e.g., 35">
                                                @error('size')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                                <small class="text-muted d-block mt-1">
                                                    💡 Nhập diện tích tính bằng mét vuông
                                                </small>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="beds" class="form-label">Loại giường</label>
                                                <input type="text" name="beds" id="beds" 
                                                       value="{{ old('beds') }}" 
                                                       class="form-control @error('beds') is-invalid @enderror" 
                                                       placeholder="e.g., 1 giường đôi King, 1 giường đôi King + 1 sofa bed">
                                                @error('beds')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                                <small class="text-muted d-block mt-1">
                                                    💡 Mô tả loại và số lượng giường trong phòng
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Section 2: Pricing & Availability -->
                        <div class="mb-5">
                            <h6 class="text-uppercase fw-bold text-muted mb-3">
                                <i class="bi bi-cash-coin me-2"></i> Pricing & Availability
                            </h6>
                            
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="price" class="form-label fw-bold">
                                        Price per Night (VNĐ) <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" name="price" id="price" 
                                           value="{{ old('price') }}" 
                                           step="0.01" min="0"
                                           class="form-control @error('price') is-invalid @enderror" 
                                           placeholder="e.g., 500000"
                                           required>
                                    @error('price')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted d-block mt-1">
                                        💡 Set base price for this room
                                    </small>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="original_price" class="form-label fw-bold">Original Price <span class="text-muted">(Optional)</span></label>
                                    <input type="number" name="original_price" id="original_price" 
                                           value="{{ old('original_price') }}" 
                                           step="0.01" min="0"
                                           class="form-control @error('original_price') is-invalid @enderror" 
                                           placeholder="Leave empty if no discount">
                                    @error('original_price')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted d-block mt-1">
                                        For displaying original price when on sale
                                    </small>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="available" class="form-label fw-bold">
                                        Số phòng còn lại <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" name="available" id="available" 
                                           value="{{ old('available', 0) }}" 
                                           min="0"
                                           class="form-control @error('available') is-invalid @enderror" 
                                           placeholder="e.g., 5"
                                           required>
                                    @error('available')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted d-block mt-1">
                                        💡 Số lượng phòng còn sẵn
                                    </small>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Section 3: Description & Amenities -->
                        <div class="mb-5">
                            <h6 class="text-uppercase fw-bold text-muted mb-3">
                                <i class="bi bi-text-left me-2"></i> Details & Amenities
                            </h6>
                            
                            <div class="mb-3">
                                <label for="description" class="form-label fw-bold">Room Description</label>
                                <textarea name="description" id="description" rows="4" 
                                          class="form-control @error('description') is-invalid @enderror"
                                          placeholder="Describe the room, features, and special characteristics...">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <small class="text-muted d-block mt-1">
                                    <i class="bi bi-info-circle me-1"></i> Provide detailed description for guests
                                </small>
                            </div>

                            <div class="mb-3">
                                <label for="amenities" class="form-label fw-bold">
                                    Amenities <span class="text-muted">(JSON Format)</span>
                                </label>
                                <textarea name="amenities" id="amenities" rows="4" 
                                          class="form-control @error('amenities') is-invalid @enderror"
                                          placeholder='e.g., ["WiFi", "Air Conditioning", "Smart TV", "Mini Bar"]'>{{ old('amenities') }}</textarea>
                                @error('amenities')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <small class="text-muted d-block mt-1">
                                    <i class="bi bi-info-circle me-1"></i> Enter as JSON array: ["WiFi", "TV", "AC", "Minibar"]
                                </small>
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Section 4: Images -->
                        <div class="mb-5">
                            <h6 class="text-uppercase fw-bold text-muted mb-3">
                                <i class="bi bi-image me-2"></i> Images
                            </h6>
                            
                            <div class="mb-3">
                                <label for="images" class="form-label fw-bold">Room Images (URLs)</label>
                                <textarea name="images" id="images" rows="4" 
                                          class="form-control @error('images') is-invalid @enderror"
                                          placeholder='Enter image URLs as JSON array: ["https://example.com/image1.jpg", "https://example.com/image2.jpg"]'>{{ old('images') }}</textarea>
                                @error('images')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <small class="text-muted d-block mt-1">
                                    <i class="bi bi-info-circle me-1"></i> Enter as JSON array of image URLs
                                </small>
                            </div>
                        </div>

                        <!-- Form Buttons -->
                        <hr class="my-4">
                        <div class="d-flex gap-2 justify-content-between">
                            <a href="{{ route('admin.hotels.rooms.index', $hotel) }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle me-2"></i> Hủy
                            </a>
                            <div>
                                <button type="reset" class="btn btn-outline-secondary me-2">
                                    <i class="bi bi-arrow-clockwise me-2"></i> Đặt lại
                                </button>
                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-check-circle me-2"></i> Tạo Phòng
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar Info Section -->
        <div class="col-lg-4">
            <!-- Hotel Information Card -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-info bg-opacity-10 border-info border-bottom">
                    <h6 class="mb-0">
                        <i class="bi bi-building text-info me-2"></i> Thông tin Khách sạn
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted d-block">Tên Khách sạn</small>
                        <strong>
                            <a href="{{ route('admin.hotels.show', $hotel) }}" class="text-decoration-none">
                                {{ $hotel->name }}
                            </a>
                        </strong>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block">Địa điểm</small>
                        <strong>{{ Str::limit($hotel->location, 50) }}</strong>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block">Phòng hiện tại</small>
                        <strong>{{ $hotel->rooms_count ?? 0 }} phòng</strong>
                    </div>
                    <div class="mb-0">
                        <small class="text-muted d-block">Đánh giá</small>
                        <strong>{{ $hotel->rating }}/5.0 ⭐</strong>
                    </div>
                </div>
            </div>

            <!-- Help & Tips Card -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-warning bg-opacity-10 border-warning border-bottom">
                    <h6 class="mb-0">
                        <i class="bi bi-lightbulb text-warning me-2"></i> Mẹo & Hướng dẫn
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong class="d-block mb-2">✅ Trường bắt buộc</strong>
                        <small class="text-muted">Tên phòng, Giá và Sức chứa là bắt buộc</small>
                    </div>
                    <div class="mb-3">
                        <strong class="d-block mb-2">💰 Mẹo Giá</strong>
                        <small class="text-muted">Đặt giá gốc cao hơn để hiển thị % giảm giá cho khách</small>
                    </div>
                    <div class="mb-3">
                        <strong class="d-block mb-2">🎨 Định dạng Tiện nghi</strong>
                        <small class="text-muted">Sử dụng định dạng JSON array:<br><code>["WiFi", "TV", "AC"]</code></small>
                    </div>
                    <div class="mb-0">
                        <strong class="d-block mb-2">🖼️ Chất lượng Ảnh</strong>
                        <small class="text-muted">Dùng ảnh chất lượng cao (JPG/PNG, 300x300px)</small>
                    </div>
                </div>
            </div>

            <!-- Room Type Examples Card -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-success bg-opacity-10 border-success border-bottom">
                    <h6 class="mb-0">
                        <i class="bi bi-bookmark text-success me-2"></i> Example Room Names
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <small>
                                <i class="bi bi-check2 text-success me-2"></i>
                                <span>Deluxe Double Room</span>
                            </small>
                        </li>
                        <li class="mb-2">
                            <small>
                                <i class="bi bi-check2 text-success me-2"></i>
                                <span>Executive Suite</span>
                            </small>
                        </li>
                        <li class="mb-2">
                            <small>
                                <i class="bi bi-check2 text-success me-2"></i>
                                <span>Standard Single Room</span>
                            </small>
                        </li>
                        <li>
                            <small>
                                <i class="bi bi-check2 text-success me-2"></i>
                                <span>Family Suite 4 Guests</span>
                            </small>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-gradient {
        background-attachment: fixed;
    }
    
    .form-control:focus, 
    .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }
    
    .card {
        border-radius: 0.5rem;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    
    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }

    .breadcrumb {
        background-color: transparent;
        padding: 0;
    }

    .text-uppercase {
        letter-spacing: 0.5px;
    }

    code {
        background-color: #f5f5f5;
        padding: 2px 6px;
        border-radius: 3px;
        color: #d63384;
    }
</style>

<script>
    // Image preview on input change
    document.getElementById('image').addEventListener('change', function() {
        previewImage();
    });

    document.getElementById('image').addEventListener('blur', function() {
        previewImage();
    });

    function previewImage() {
        const imageUrl = document.getElementById('image').value;
        const previewDiv = document.getElementById('imagePreview');
        const previewImg = document.getElementById('previewImg');

        if (imageUrl && isValidUrl(imageUrl)) {
            previewImg.src = imageUrl;
            previewImg.onload = function() {
                previewDiv.classList.remove('d-none');
            };
            previewImg.onerror = function() {
                previewDiv.classList.add('d-none');
            };
        } else {
            previewDiv.classList.add('d-none');
        }
    }

    function isValidUrl(url) {
        try {
            new URL(url);
            return true;
        } catch (error) {
            return false;
        }
    }
</script>
@endsection
