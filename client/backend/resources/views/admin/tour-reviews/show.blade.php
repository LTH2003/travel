@extends('admin.layouts.app')

@section('title', 'Chi Tiết Đánh Giá')
@section('page-title', 'Chi Tiết Đánh Giá')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Chi Tiết Đánh Giá</h5>
                <a href="{{ route('admin.tour-reviews.index') }}" class="btn btn-secondary btn-sm">
                    <i class="bi bi-arrow-left"></i> Quay Lại
                </a>
            </div>

            <div class="card-body">
                <!-- Messages -->
                @if ($message = Session::get('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle"></i> {{ $message }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- Tour Info -->
                <div class="mb-4">
                    <h6 class="text-muted mb-2">TOUR</h6>
                    <div class="d-flex align-items-center">
                        @if($tourReview->tour->image)
                            <img src="{{ $tourReview->tour->image }}" alt="{{ $tourReview->tour->title }}" 
                                 style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px; margin-right: 15px;">
                        @endif
                        <div>
                            <h5 class="mb-1">{{ $tourReview->tour->title }}</h5>
                            <p class="text-muted mb-0">
                                <i class="bi bi-geo-alt"></i> {{ $tourReview->tour->destination }} | 
                                <i class="bi bi-clock"></i> {{ $tourReview->tour->duration }}
                            </p>
                        </div>
                    </div>
                </div>

                <hr>

                <!-- User Info -->
                <div class="mb-4">
                    <h6 class="text-muted mb-2">NGƯỜI ĐÁNH GIÁ</h6>
                    <div class="d-flex align-items-center">
                        @if($tourReview->user->avatar)
                            <img src="{{ $tourReview->user->avatar }}" alt="{{ $tourReview->user->name }}" 
                                 class="rounded-circle me-3" style="width: 50px; height: 50px; object-fit: cover;">
                        @else
                            <div class="rounded-circle bg-secondary me-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                <span class="text-white fw-bold">{{ substr($tourReview->user->name, 0, 1) }}</span>
                            </div>
                        @endif
                        <div>
                            <h5 class="mb-1">{{ $tourReview->user->name }}</h5>
                            <p class="text-muted mb-0">{{ $tourReview->user->email }}</p>
                        </div>
                    </div>
                </div>

                <hr>

                <!-- Rating -->
                <div class="mb-4">
                    <h6 class="text-muted mb-2">ĐÁNH GIÁ</h6>
                    <div class="d-flex align-items-center">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= $tourReview->rating)
                                <i class="bi bi-star-fill" style="color: #ffc107; font-size: 1.5rem; margin-right: 5px;"></i>
                            @else
                                <i class="bi bi-star" style="color: #ddd; font-size: 1.5rem; margin-right: 5px;"></i>
                            @endif
                        @endfor
                        <span class="ms-2 fs-5 fw-bold">{{ $tourReview->rating }}/5</span>
                    </div>
                </div>

                <hr>

                <!-- Title -->
                @if($tourReview->title)
                    <div class="mb-4">
                        <h6 class="text-muted mb-2">TIÊU ĐỀ</h6>
                        <h5>{{ $tourReview->title }}</h5>
                    </div>
                    <hr>
                @endif

                <!-- Comment -->
                @if($tourReview->comment)
                    <div class="mb-4">
                        <h6 class="text-muted mb-2">NỘI DUNG</h6>
                        <p style="line-height: 1.6; color: #333;">{{ $tourReview->comment }}</p>
                    </div>
                    <hr>
                @endif

                <!-- Metadata -->
                <div class="row text-muted small">
                    <div class="col-md-6">
                        <p><strong>Ngày đánh giá:</strong> {{ $tourReview->created_at->format('d/m/Y H:i:s') }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Cập nhật lần cuối:</strong> {{ $tourReview->updated_at->format('d/m/Y H:i:s') }}</p>
                    </div>
                </div>

                <!-- Status Badge -->
                <div class="mb-4">
                    <h6 class="text-muted mb-2">TRẠNG THÁI</h6>
                    @if($tourReview->is_approved)
                        <span class="badge bg-success p-2">
                            <i class="bi bi-check-circle"></i> Đã Duyệt
                        </span>
                    @else
                        <span class="badge bg-warning p-2">
                            <i class="bi bi-hourglass"></i> Chờ Duyệt
                        </span>
                    @endif
                </div>
            </div>

            <!-- Actions -->
            <div class="card-footer d-flex gap-2 justify-content-end">
                @if(!$tourReview->is_approved)
                    <form action="{{ route('admin.tour-reviews.approve', $tourReview) }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-circle"></i> Duyệt Đánh Giá
                        </button>
                    </form>
                @else
                    <form action="{{ route('admin.tour-reviews.reject', $tourReview) }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-x-circle"></i> Từ Chối Đánh Giá
                        </button>
                    </form>
                @endif

                <form action="{{ route('admin.tour-reviews.destroy', $tourReview) }}" method="POST" style="display: inline;" onsubmit="return confirm('Bạn chắc chắn muốn xóa đánh giá này?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash"></i> Xóa Đánh Giá
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Sidebar: Tour Stats -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Thống Kê Tour</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <small class="text-muted">Đánh Giá Trung Bình</small>
                    <h4 class="mb-0">
                        {{ $tourReview->tour->rating ?? 'N/A' }} / 5
                    </h4>
                </div>
                <div class="mb-3">
                    <small class="text-muted">Tổng Đánh Giá</small>
                    <h4 class="mb-0">{{ $tourReview->tour->review_count ?? 0 }}</h4>
                </div>
                <div>
                    <small class="text-muted">Giá</small>
                    <h4 class="mb-0 text-success">
                        {{ number_format($tourReview->tour->price) }}đ
                    </h4>
                </div>
                <hr>
                <a href="{{ route('admin.tours.show', $tourReview->tour) }}" class="btn btn-info btn-sm w-100">
                    <i class="bi bi-eye"></i> Xem Tour
                </a>
            </div>
        </div>

        <!-- Recent Reviews for this Tour -->
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="mb-0">Đánh Giá Gần Đây Của Tour</h5>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @forelse($tourReview->tour->reviews()->latest()->limit(5)->get() as $review)
                        <a href="{{ route('admin.tour-reviews.show', $review) }}" class="list-group-item list-group-item-action p-3">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <strong>{{ $review->user->name }}</strong>
                                    <div class="text-warning small">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= $review->rating)
                                                <i class="bi bi-star-fill"></i>
                                            @else
                                                <i class="bi bi-star"></i>
                                            @endif
                                        @endfor
                                    </div>
                                </div>
                                @if($review->is_approved)
                                    <span class="badge bg-success">Duyệt</span>
                                @else
                                    <span class="badge bg-warning">Chờ</span>
                                @endif
                            </div>
                        </a>
                    @empty
                        <div class="p-3 text-muted text-center">Không có đánh giá nào khác</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
