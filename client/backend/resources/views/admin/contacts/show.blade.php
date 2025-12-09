@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0">Chi tiết Tin nhắn</h1>
                <a href="{{ route('admin.contacts.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Quay lại
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">{{ $contact->subject }}</h5>
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
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <p class="text-muted mb-1">Từ</p>
                            <h6 class="mb-0">{{ $contact->name }}</h6>
                            @if($contact->user)
                                <small class="text-primary">
                                    <a href="{{ route('admin.users.show', $contact->user->id) }}">
                                        Xem hồ sơ người dùng →
                                    </a>
                                </small>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <p class="text-muted mb-1">Ngày gửi</p>
                            <h6 class="mb-0">{{ $contact->created_at?->format('d/m/Y H:i:s') ?? 'N/A' }}</h6>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <p class="text-muted mb-1">Email</p>
                            <a href="mailto:{{ $contact->email }}">{{ $contact->email }}</a>
                        </div>
                        <div class="col-md-6">
                            <p class="text-muted mb-1">Số điện thoại</p>
                            @if($contact->phone)
                                <a href="tel:{{ $contact->phone }}">{{ $contact->phone }}</a>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </div>
                    </div>

                    <hr>

                    <div class="mb-4">
                        <p class="text-muted mb-2"><strong>Nội dung tin nhắn:</strong></p>
                        <div class="alert alert-light border" style="white-space: pre-wrap;">
                            {{ $contact->message }}
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-12">
                            <p class="text-muted mb-3"><strong>Phản hồi:</strong></p>
                            <form method="POST" action="{{ route('admin.contacts.update', $contact->id) }}">
                                @csrf
                                @method('PUT')
                                
                                <div class="mb-3">
                                    <label for="response" class="form-label">Nội dung phản hồi</label>
                                    <textarea class="form-control @error('response') is-invalid @enderror" 
                                              id="response" name="response" rows="4" 
                                              placeholder="Nhập phản hồi cho khách hàng..."
                                              @if($contact->status === 'replied') readonly @endif>{{ old('response', $contact->response) }}</textarea>
                                    @error('response')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="status" class="form-label">Trạng thái</label>
                                    <select class="form-select @error('status') is-invalid @enderror" 
                                            id="status" name="status">
                                        <option value="new" @selected($contact->status === 'new')>Chưa đọc</option>
                                        <option value="read" @selected($contact->status === 'read')>Đã đọc</option>
                                        <option value="replied" @selected($contact->status === 'replied')>Đã trả lời</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="d-flex gap-2">
                                    @if($contact->status !== 'replied')
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Lưu & Cập nhật
                                    </button>
                                    @endif
                                </div>
                            </form>

                            <!-- Delete Form -->
                            <form method="POST" action="{{ route('admin.contacts.destroy', $contact->id) }}"
                                  onsubmit="return confirm('Bạn có chắc chắn muốn xóa tin nhắn này?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger mt-2">
                                    <i class="fas fa-trash me-2"></i>Xóa tin nhắn
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Hành động nhanh</h5>
                </div>
                <div class="card-body">
                    <a href="mailto:{{ $contact->email }}" class="btn btn-primary w-100 mb-2">
                        <i class="fas fa-reply me-2"></i>Trả lời qua Email
                    </a>
                    @if($contact->phone)
                    <a href="tel:{{ $contact->phone }}" class="btn btn-info w-100 mb-2">
                        <i class="fas fa-phone me-2"></i>Gọi điện
                    </a>
                    @endif
                    <a href="{{ route('admin.contacts.index') }}" class="btn btn-secondary w-100">
                        <i class="fas fa-list me-2"></i>Quay lại danh sách
                    </a>
                    
                    <!-- Cancellation Email Button -->
                    <button type="button" class="btn btn-danger w-100 mt-2" data-bs-toggle="modal" data-bs-target="#cancellationModal" 
                            @if($contact->status === 'replied') disabled @endif>
                        <i class="fas fa-envelope me-2"></i>Gửi Email Hủy Đơn
                    </button>
                </div>
            </div>

            <!-- Info Card -->
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Thông tin</h5>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-5">Trạng thái:</dt>
                        <dd class="col-sm-7">
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
                        </dd>

                        <dt class="col-sm-5">ID:</dt>
                        <dd class="col-sm-7">
                            <code>{{ $contact->id }}</code>
                        </dd>

                        <dt class="col-sm-5">Tạo lúc:</dt>
                        <dd class="col-sm-7">{{ $contact->created_at?->format('d/m/Y H:i') ?? 'N/A' }}</dd>

                        <dt class="col-sm-5">Cập nhật:</dt>
                        <dd class="col-sm-7">{{ $contact->updated_at?->format('d/m/Y H:i') ?? 'N/A' }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Cancellation Modal -->
<div class="modal fade" id="cancellationModal" tabindex="-1" aria-labelledby="cancellationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="cancellationModalLabel">
                    <i class="fas fa-trash me-2"></i>Gửi Email Hủy Đơn Hàng
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('admin.contacts.send-cancellation-email', $contact->id) }}">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <strong>Thông tin khách hàng:</strong><br>
                        Tên: <strong>{{ $contact->name }}</strong><br>
                        Email: <strong>{{ $contact->email }}</strong>
                    </div>

                    <div class="mb-3">
                        <label for="refund_amount" class="form-label">
                            <i class="fas fa-money-bill-wave me-2"></i>Số tiền hoàn lại (VND)
                        </label>
                        <input type="number" class="form-control @error('refund_amount') is-invalid @enderror" 
                               id="refund_amount" name="refund_amount" min="0" step="1000" required 
                               placeholder="Ví dụ: 1000000">
                        @error('refund_amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="cancellation_reason" class="form-label">
                            <i class="fas fa-comments me-2"></i>Lý do hủy đơn
                        </label>
                        <textarea class="form-control @error('cancellation_reason') is-invalid @enderror" 
                                  id="cancellation_reason" name="cancellation_reason" rows="4" 
                                  placeholder="Nhập lý do hủy đơn cho khách hàng..." required
                                  minlength="10"></textarea>
                        <small class="form-text text-muted">Tối thiểu 10 ký tự</small>
                        @error('cancellation_reason')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="alert alert-warning">
                        <strong>⚠️ Thông báo:</strong><br>
                        Tiền hoàn sẽ được gửi về tài khoản của khách hàng trong vòng 1-3 ngày làm việc.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-paper-plane me-2"></i>Gửi Email Hủy Đơn
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
