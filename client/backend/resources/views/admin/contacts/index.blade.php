@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0">Quản lý Tin nhắn Liên hệ</h1>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-light">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-0">Tổng tin nhắn</p>
                            <h3 class="mb-0">{{ $stats['total'] ?? 0 }}</h3>
                        </div>
                        <i class="fas fa-envelope fa-3x text-primary opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-light">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-0">Chưa đọc</p>
                            <h3 class="mb-0 text-warning">{{ $stats['new'] ?? 0 }}</h3>
                        </div>
                        <i class="fas fa-inbox fa-3x text-warning opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-light">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-0">Đã đọc</p>
                            <h3 class="mb-0 text-info">{{ $stats['read'] ?? 0 }}</h3>
                        </div>
                        <i class="fas fa-check-circle fa-3x text-info opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-light">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-0">Đã trả lời</p>
                            <h3 class="mb-0 text-success">{{ $stats['replied'] ?? 0 }}</h3>
                        </div>
                        <i class="fas fa-reply-all fa-3x text-success opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.contacts.index') }}" class="row g-3">
                        <div class="col-md-4">
                            <label for="status" class="form-label">Lọc theo trạng thái</label>
                            <select name="status" id="status" class="form-select">
                                <option value="">-- Tất cả --</option>
                                <option value="new" {{ request('status') === 'new' ? 'selected' : '' }}>Chưa đọc</option>
                                <option value="read" {{ request('status') === 'read' ? 'selected' : '' }}>Đã đọc</option>
                                <option value="replied" {{ request('status') === 'replied' ? 'selected' : '' }}>Đã trả lời</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="search" class="form-label">Tìm kiếm</label>
                            <input type="text" name="search" id="search" class="form-control" 
                                   placeholder="Tìm theo tên, email..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-4 d-flex align-items-end gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-2"></i>Tìm kiếm
                            </button>
                            <a href="{{ route('admin.contacts.index') }}" class="btn btn-secondary">
                                <i class="fas fa-redo me-2"></i>Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Contacts Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Trạng thái</th>
                                <th>Tên người gửi</th>
                                <th>Email</th>
                                <th>Số điện thoại</th>
                                <th>Chủ đề</th>
                                <th>Ngày gửi</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($contacts as $contact)
                            <tr class="{{ $contact->status === 'new' ? 'table-warning' : '' }}">
                                <td>
                                    <span class="badge 
                                        @if($contact->status === 'new') bg-warning text-dark
                                        @elseif($contact->status === 'read') bg-info
                                        @elseif($contact->status === 'replied') bg-success
                                        @endif">
                                        @if($contact->status === 'new') Chưa đọc
                                        @elseif($contact->status === 'read') Đã đọc
                                        @elseif($contact->status === 'replied') Đã trả lời
                                        @endif
                                    </span>
                                </td>
                                <td><strong>{{ $contact->name }}</strong></td>
                                <td>
                                    <a href="mailto:{{ $contact->email }}">{{ $contact->email }}</a>
                                    @if($contact->user)
                                        <br><small class="text-muted">ID User: #{{ $contact->user->id }}</small>
                                    @endif
                                </td>
                                <td>
                                    @if($contact->phone)
                                        <a href="tel:{{ $contact->phone }}">{{ $contact->phone }}</a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <span title="{{ $contact->subject }}">
                                        {{ Str::limit($contact->subject, 30) }}
                                    </span>
                                </td>
                                <td>{{ $contact->created_at?->format('d/m/Y H:i') ?? 'N/A' }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.contacts.show', $contact->id) }}" 
                                           class="btn btn-sm btn-outline-primary"
                                           title="Xem chi tiết">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($contact->status !== 'replied')
                                        <button type="button" class="btn btn-sm btn-outline-success" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#updateStatusModal"
                                                onclick="document.getElementById('contactId').value = {{ $contact->id }}"
                                                title="Cập nhật trạng thái">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        @endif
                                        <form method="POST" action="{{ route('admin.contacts.destroy', $contact->id) }}" 
                                              style="display: inline;"
                                              onsubmit="return confirm('Bạn có chắc chắn muốn xóa tin nhắn này?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                                    title="Xóa">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    <i class="fas fa-inbox fa-3x mb-2"></i>
                                    <p class="mt-2">Không có tin nhắn nào</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            @if($contacts->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $contacts->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Update Status Modal -->
<div class="modal fade" id="updateStatusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cập nhật trạng thái tin nhắn</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="updateStatusForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <input type="hidden" id="contactId" name="contact_id">
                    <div class="mb-3">
                        <label for="status" class="form-label">Trạng thái</label>
                        <select name="status" id="status" class="form-select" required>
                            <option value="read">Đã đọc</option>
                            <option value="replied">Đã trả lời</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Cập nhật</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('✅ Contact management script loaded');
    
    // Update status form submission
    const updateStatusForm = document.getElementById('updateStatusForm');
    if (updateStatusForm) {
        updateStatusForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const contactId = document.getElementById('contactId').value;
            const status = document.getElementById('status').value;
            
            console.log('📝 Updating contact:', contactId, 'to status:', status);
            
            // Submit via fetch
            fetch(`/admin/contacts/${contactId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({ status })
            })
            .then(response => response.json())
            .then(data => {
                console.log('✅ Response:', data);
                if (data.success) {
                    location.reload();
                } else {
                    alert('Cập nhật thất bại');
                }
            })
            .catch(err => {
                console.error('❌ Error:', err);
                alert('Có lỗi xảy ra');
            });
        });
    }
});

    // Update status form submission
    const updateStatusForm = document.getElementById('updateStatusForm');
    if (updateStatusForm) {
        updateStatusForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const contactId = document.getElementById('contactId').value;
            const status = document.getElementById('status').value;
            
            // Submit via fetch
            fetch(`/admin/contacts/${contactId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({ status })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Cập nhật thất bại');
                }
            })
            .catch(err => console.error(err));
        });
    }
});
</script>
@endpush
@endsection
