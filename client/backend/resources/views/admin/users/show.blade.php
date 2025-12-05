@extends('admin.layouts.app')

@section('title', 'Chi tiết Người Dùng')
@section('page-title', 'Chi tiết Người Dùng')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Chi tiết: {{ $user->name }}</h5>
                <div class="gap-2 d-flex">
                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-warning btn-sm">
                        <i class="bi bi-pencil"></i> Chỉnh sửa
                    </a>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-sm">
                        <i class="bi bi-arrow-left"></i> Quay lại
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Tên</label>
                    <p class="form-control-plaintext">{{ $user->name }}</p>
                </div>

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <p class="form-control-plaintext">{{ $user->email }}</p>
                </div>

                <div class="mb-3">
                    <label class="form-label">Role</label>
                    <p class="form-control-plaintext">
                        <span class="badge" style="background-color: {{ $user->role === 'admin' ? '#667eea' : '#6c757d' }};">
                            {{ ucfirst($user->role) }}
                        </span>
                    </p>
                </div>

                <div class="mb-3">
                    <label class="form-label">Email Verified</label>
                    <p class="form-control-plaintext">
                        @if($user->email_verified_at)
                            <span class="badge bg-success">Đã xác nhận</span> 
                            ({{ $user->email_verified_at->format('d/m/Y H:i') }})
                        @else
                            <span class="badge bg-warning">Chưa xác nhận</span>
                        @endif
                    </p>
                </div>

                <div class="mb-3">
                    <label class="form-label">Ngày tạo</label>
                    <p class="form-control-plaintext">
                        @if($user->created_at)
                            {{ $user->created_at->format('d/m/Y H:i:s') }}
                        @else
                            <em>N/A</em>
                        @endif
                    </p>
                </div>

                <div class="mb-3">
                    <label class="form-label">Cập nhật lần cuối</label>
                    <p class="form-control-plaintext">
                        @if($user->updated_at)
                            {{ $user->updated_at->format('d/m/Y H:i:s') }}
                        @else
                            <em>N/A</em>
                        @endif
                    </p>
                </div>

                <div class="d-flex gap-2">
                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary">
                        <i class="bi bi-pencil"></i> Chỉnh sửa
                    </a>
                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" style="display: inline;" onsubmit="return confirm('Bạn chắc chắn muốn xóa người dùng này?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-trash"></i> Xóa
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
