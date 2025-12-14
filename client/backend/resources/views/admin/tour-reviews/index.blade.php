@extends('admin.layouts.app')

@section('title', 'Quản lý Đánh Giá Tour')
@section('page-title', 'Đánh Giá Tour')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Quản lý Đánh Giá Tour</h5>
            </div>
            <div class="card-body">
                <!-- Statistics -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card border-success">
                            <div class="card-body">
                                <h6 class="card-title text-success">Tổng Đánh Giá</h6>
                                <h2 class="mb-0">{{ $stats['total_reviews'] }}</h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-primary">
                            <div class="card-body">
                                <h6 class="card-title text-primary">Đã Duyệt</h6>
                                <h2 class="mb-0">{{ $stats['approved_reviews'] }}</h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-warning">
                            <div class="card-body">
                                <h6 class="card-title text-warning">Chờ Duyệt</h6>
                                <h2 class="mb-0">{{ $stats['pending_reviews'] }}</h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-info">
                            <div class="card-body">
                                <h6 class="card-title text-info">Tỷ Lệ Duyệt</h6>
                                <h2 class="mb-0">
                                    @if($stats['total_reviews'] > 0)
                                        {{ round(($stats['approved_reviews'] / $stats['total_reviews']) * 100) }}%
                                    @else
                                        0%
                                    @endif
                                </h2>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Search & Filter -->
                <form method="GET" class="mb-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control" placeholder="Tìm tour, người dùng..." value="{{ $search }}">
                        </div>
                        <div class="col-md-3">
                            <select name="status" class="form-select">
                                <option value="">-- Tất cả trạng thái --</option>
                                <option value="approved" @selected($status === 'approved')>Đã Duyệt</option>
                                <option value="pending" @selected($status === 'pending')>Chờ Duyệt</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-search"></i> Tìm
                            </button>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('admin.tour-reviews.index') }}" class="btn btn-secondary w-100">
                                <i class="bi bi-arrow-clockwise"></i> Reset
                            </a>
                        </div>
                    </div>
                </form>

                <!-- Messages -->
                @if ($message = Session::get('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle"></i> {{ $message }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if ($message = Session::get('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-circle"></i> {{ $message }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Reviews Table -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 40px;">
                                <input type="checkbox" id="selectAll" class="form-check-input">
                            </th>
                            <th>Tour</th>
                            <th>Người Đánh Giá</th>
                            <th>Đánh Giá</th>
                            <th>Tiêu Đề</th>
                            <th>Trạng Thái</th>
                            <th>Ngày</th>
                            <th>Hành Động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reviews as $review)
                            <tr>
                                <td>
                                    <input type="checkbox" class="form-check-input review-checkbox" value="{{ $review->id }}">
                                </td>
                                <td>
                                    <strong>{{ $review->tour->title ?? 'N/A' }}</strong>
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $review->user->name ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= $review->rating)
                                                <i class="bi bi-star-fill" style="color: #ffc107;"></i>
                                            @else
                                                <i class="bi bi-star" style="color: #ddd;"></i>
                                            @endif
                                        @endfor
                                        <span class="ms-2">{{ $review->rating }}/5</span>
                                    </div>
                                </td>
                                <td>
                                    <small class="text-muted">{{ $review->title ? mb_substr($review->title, 0, 30) . '...' : '(Không có)' }}</small>
                                </td>
                                <td>
                                    @if($review->is_approved)
                                        <span class="badge bg-success">
                                            <i class="bi bi-check-circle"></i> Đã Duyệt
                                        </span>
                                    @else
                                        <span class="badge bg-warning">
                                            <i class="bi bi-hourglass"></i> Chờ Duyệt
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <small class="text-muted">{{ $review->created_at->format('d/m/Y H:i') }}</small>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('admin.tour-reviews.show', $review) }}" class="btn btn-info" title="Xem">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        @if(!$review->is_approved)
                                            <form action="{{ route('admin.tour-reviews.approve', $review) }}" method="POST" style="display: inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm" title="Duyệt">
                                                    <i class="bi bi-check-circle"></i>
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route('admin.tour-reviews.reject', $review) }}" method="POST" style="display: inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-warning btn-sm" title="Từ Chối">
                                                    <i class="bi bi-x-circle"></i>
                                                </button>
                                            </form>
                                        @endif
                                        <form action="{{ route('admin.tour-reviews.destroy', $review) }}" method="POST" style="display: inline;" onsubmit="return confirm('Bạn chắc chắn muốn xóa?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" title="Xóa">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    <i class="bi bi-inbox" style="font-size: 2rem;"></i><br>
                                    Không có đánh giá nào
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Bulk Actions -->
            @if($reviews->count() > 0)
                <div class="card-footer">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <small class="text-muted">
                                Chọn <span id="selectedCount">0</span> đánh giá
                            </small>
                        </div>
                        <div class="col-md-6 text-end">
                            <form method="POST" style="display: inline;">
                                @csrf
                                <input type="hidden" name="ids" id="bulkIds" value="">
                                <button type="submit" form="bulkApproveForm" class="btn btn-success btn-sm me-2">
                                    <i class="bi bi-check-circle"></i> Duyệt hàng loạt
                                </button>
                                <button type="submit" form="bulkRejectForm" class="btn btn-warning btn-sm me-2">
                                    <i class="bi bi-x-circle"></i> Từ Chối hàng loạt
                                </button>
                                <button type="submit" form="bulkDeleteForm" class="btn btn-danger btn-sm" onclick="return confirm('Xóa những đánh giá này?');">
                                    <i class="bi bi-trash"></i> Xóa hàng loạt
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endif

            
        </div>
    </div>
</div>

<!-- Forms for bulk operations -->
<form id="bulkApproveForm" action="{{ route('admin.tour-reviews.approve-bulk') }}" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="ids" id="bulkApproveIds" value="">
</form>

<form id="bulkRejectForm" action="{{ route('admin.tour-reviews.reject-bulk') }}" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="ids" id="bulkRejectIds" value="">
</form>

<form id="bulkDeleteForm" action="{{ route('admin.tour-reviews.delete-bulk') }}" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="ids" id="bulkDeleteIds" value="">
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const reviewCheckboxes = document.querySelectorAll('.review-checkbox');
    const selectedCountSpan = document.getElementById('selectedCount');
    const bulkApproveForms = document.getElementById('bulkApproveIds');
    const bulkRejectForms = document.getElementById('bulkRejectIds');
    const bulkDeleteForms = document.getElementById('bulkDeleteIds');

    function updateSelectedCount() {
        const checkedCount = document.querySelectorAll('.review-checkbox:checked').length;
        selectedCountSpan.textContent = checkedCount;
        
        const selectedIds = Array.from(document.querySelectorAll('.review-checkbox:checked'))
            .map(cb => cb.value);
        bulkApproveForms.value = JSON.stringify(selectedIds);
        bulkRejectForms.value = JSON.stringify(selectedIds);
        bulkDeleteForms.value = JSON.stringify(selectedIds);
    }

    selectAllCheckbox.addEventListener('change', function() {
        reviewCheckboxes.forEach(cb => cb.checked = this.checked);
        updateSelectedCount();
    });

    reviewCheckboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            selectAllCheckbox.checked = Array.from(reviewCheckboxes).every(c => c.checked);
            updateSelectedCount();
        });
    });

    // Handle bulk action form submissions
    ['bulkApproveForm', 'bulkRejectForm', 'bulkDeleteForm'].forEach(formId => {
        const form = document.getElementById(formId);
        const hiddenInput = form.querySelector('input[name="ids"]');
        
        form.addEventListener('submit', function(e) {
            const checkedIds = Array.from(document.querySelectorAll('.review-checkbox:checked'))
                .map(cb => cb.value);
            hiddenInput.value = JSON.stringify(checkedIds);
        });
    });
});
</script>

<style>
    .btn-group-sm .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }
</style>
@endsection
