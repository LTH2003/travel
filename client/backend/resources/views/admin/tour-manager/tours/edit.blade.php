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

                        <!-- Departure Points -->
                        <div class="mb-3">
                            <label class="form-label">Điểm khởi hành</label>
                            <div id="departure-container">
                                @php
                                    $departures = old('departure', $tour->departure ?? []);
                                    if (empty($departures)) {
                                        $departures = [''];
                                    }
                                @endphp
                                @foreach($departures as $index => $departure)
                                    <div class="input-group mb-2">
                                        <input 
                                            type="text" 
                                            class="form-control @error('departure.'.$index) is-invalid @enderror" 
                                            name="departure[]" 
                                            value="{{ $departure }}"
                                            placeholder="Nhập điểm khởi hành (vd: Hà Nội, Sài Gòn)"
                                        >
                                        @if($index > 0)
                                            <button type="button" class="btn btn-outline-danger remove-item" data-type="departure" title="Xóa">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                    @error('departure.'.$index)
                                        <small class="text-danger d-block mb-2">{{ $message }}</small>
                                    @enderror
                                @endforeach
                            </div>
                            <button type="button" class="add-item btn btn-sm btn-outline-primary" data-type="departure">
                                <i class="bi bi-plus"></i> Thêm điểm khởi hành
                            </button>
                            @error('departure')
                                <small class="text-danger d-block">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Highlights -->
                        <div class="mb-3">
                            <label class="form-label">Điểm nổi bật (Highlights)</label>
                            <div id="highlights-container">
                                @php
                                    $highlights = old('highlights', $tour->highlights ?? []);
                                    if (empty($highlights)) {
                                        $highlights = [''];
                                    }
                                @endphp
                                @foreach($highlights as $index => $highlight)
                                    <div class="input-group mb-2">
                                        <input 
                                            type="text" 
                                            class="form-control @error('highlights.'.$index) is-invalid @enderror" 
                                            name="highlights[]" 
                                            value="{{ $highlight }}"
                                            placeholder="Nhập điểm nổi bật (vd: Tham quan Hạ Long Bay)"
                                        >
                                        @if($index > 0)
                                            <button type="button" class="btn btn-outline-danger remove-item" data-type="highlights" title="Xóa">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                    @error('highlights.'.$index)
                                        <small class="text-danger d-block mb-2">{{ $message }}</small>
                                    @enderror
                                @endforeach
                            </div>
                            <button type="button" class="add-item btn btn-sm btn-outline-primary" data-type="highlights">
                                <i class="bi bi-plus"></i> Thêm điểm nổi bật
                            </button>
                            @error('highlights')
                                <small class="text-danger d-block">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Includes -->
                        <div class="mb-3">
                            <label class="form-label">Bao gồm (Includes)</label>
                            <div id="includes-container">
                                @php
                                    $includes = old('includes', $tour->includes ?? []);
                                    if (empty($includes)) {
                                        $includes = [''];
                                    }
                                @endphp
                                @foreach($includes as $index => $include)
                                    <div class="input-group mb-2">
                                        <input 
                                            type="text" 
                                            class="form-control @error('includes.'.$index) is-invalid @enderror" 
                                            name="includes[]" 
                                            value="{{ $include }}"
                                            placeholder="Nhập dịch vụ bao gồm (vd: Vé máy bay, Khách sạn 3 sao)"
                                        >
                                        @if($index > 0)
                                            <button type="button" class="btn btn-outline-danger remove-item" data-type="includes" title="Xóa">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                    @error('includes.'.$index)
                                        <small class="text-danger d-block mb-2">{{ $message }}</small>
                                    @enderror
                                @endforeach
                            </div>
                            <button type="button" class="add-item btn btn-sm btn-outline-primary" data-type="includes">
                                <i class="bi bi-plus"></i> Thêm dịch vụ bao gồm
                            </button>
                            @error('includes')
                                <small class="text-danger d-block">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Itinerary -->
                        <div class="mb-3">
                            <label class="form-label">Lịch trình (Itinerary)</label>
                            <div id="itinerary-container">
                                @php
                                    $itineraries = old('itinerary', $tour->itinerary ?? []);
                                    if (empty($itineraries)) {
                                        $itineraries = [''];
                                    }
                                @endphp
                                @foreach($itineraries as $index => $itinerary)
                                    <div class="input-group mb-2">
                                        <input 
                                            type="text" 
                                            class="form-control @error('itinerary.'.$index) is-invalid @enderror" 
                                            name="itinerary[]" 
                                            value="{{ $itinerary }}"
                                            placeholder="Nhập chi tiết lịch trình (vd: Ngày 1: Hà Nội - Hạ Long)"
                                        >
                                        @if($index > 0)
                                            <button type="button" class="btn btn-outline-danger remove-item" data-type="itinerary" title="Xóa">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                    @error('itinerary.'.$index)
                                        <small class="text-danger d-block mb-2">{{ $message }}</small>
                                    @enderror
                                @endforeach
                            </div>
                            <button type="button" class="add-item btn btn-sm btn-outline-primary" data-type="itinerary">
                                <i class="bi bi-plus"></i> Thêm lịch trình
                            </button>
                            @error('itinerary')
                                <small class="text-danger d-block">{{ $message }}</small>
                            @enderror
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fieldConfigs = {
        departure: {
            containerId: 'departure-container',
            placeholder: 'Nhập điểm khởi hành (vd: Hà Nội, Sài Gòn)',
            fieldName: 'departure'
        },
        highlights: {
            containerId: 'highlights-container',
            placeholder: 'Nhập điểm nổi bật (vd: Tham quan Hạ Long Bay)',
            fieldName: 'highlights'
        },
        includes: {
            containerId: 'includes-container',
            placeholder: 'Nhập dịch vụ bao gồm (vd: Vé máy bay, Khách sạn 3 sao)',
            fieldName: 'includes'
        },
        itinerary: {
            containerId: 'itinerary-container',
            placeholder: 'Nhập chi tiết lịch trình (vd: Ngày 1: Hà Nội - Hạ Long)',
            fieldName: 'itinerary'
        }
    };

    // Add item button listeners
    document.querySelectorAll('.add-item').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const type = this.getAttribute('data-type');
            const config = fieldConfigs[type];
            const container = document.getElementById(config.containerId);
            
            const newInput = document.createElement('div');
            newInput.classList.add('input-group', 'mb-2');
            newInput.innerHTML = `
                <input type="text" class="form-control" name="${config.fieldName}[]" placeholder="${config.placeholder}">
                <button type="button" class="btn btn-outline-danger remove-item" data-type="${type}" title="Xóa">
                    <i class="bi bi-trash"></i>
                </button>
            `;
            container.appendChild(newInput);
            attachRemoveListener(newInput.querySelector('.remove-item'));
        });
    });

    // Remove item button listeners
    function attachRemoveListener(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            this.closest('.input-group').remove();
        });
    }

    document.querySelectorAll('.remove-item').forEach(btn => {
        attachRemoveListener(btn);
    });
});
</script>
@endsection
