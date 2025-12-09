@extends('admin.layouts.app')

@section('title', 'Dashboard - Admin Panel')
@section('page-title', 'Dashboard')

@section('content')
<div class="row">
    <!-- Stats Cards -->
    <div class="col-md-4 mb-4">
        <div class="stat-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <h6>Tổng Người Dùng</h6>
            <h3>{{ $totalUsers }}</h3>
            <small style="opacity: 0.9;">
                <i class="bi bi-arrow-up"></i> Người dùng tích cực
            </small>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="stat-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
            <h6>Tổng Tour</h6>
            <h3>{{ $totalTours }}</h3>
            <small style="opacity: 0.9;">
                <i class="bi bi-arrow-up"></i> Tour hạ nhiệt
            </small>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="stat-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
            <h6>Tổng Blog</h6>
            <h3>{{ $totalBlogs }}</h3>
            <small style="opacity: 0.9;">
                <i class="bi bi-arrow-up"></i> Bài viết mới
            </small>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="stat-card" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
            <h6>Tổng Khách Sạn</h6>
            <h3>{{ \App\Models\Hotel::count() }}</h3>
            <small style="opacity: 0.9;">
                <i class="bi bi-arrow-up"></i> <a href="{{ route('admin.hotels.index') }}" style="color: white;">Quản lý</a>
            </small>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="stat-card" style="background: linear-gradient(135deg, #30cfd0 0%, #330867 100%);">
            <h6>Tổng Phòng</h6>
            <h3>{{ \App\Models\Room::count() }}</h3>
            <small style="opacity: 0.9;">
                <i class="bi bi-arrow-up"></i> Phòng có sẵn
            </small>
        </div>
    </div>
</div>

<div class="row mt-4">
    <!-- Recent Users -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Người dùng gần đây</h5>
                <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-primary">Xem tất cả</a>
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Tên</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Ngày tạo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentUsers as $user)
                            <tr>
                                <td>
                                    <strong>{{ $user->name }}</strong>
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    <span class="badge" style="background-color: {{ $user->role === 'admin' ? '#667eea' : '#6c757d' }};">
                                        {{ $user->role }}
                                    </span>
                                </td>
                                <td>
                                    @if($user->created_at)
                                        {{ $user->created_at->format('d/m/Y') }}
                                    @else
                                        <em>N/A</em>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">Chưa có người dùng</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Recent Tours -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Tour gần đây</h5>
                <a href="{{ route('admin.tours.index') }}" class="btn btn-sm btn-primary">Xem tất cả</a>
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Tên</th>
                            <th>Giá</th>
                            <th>Thời gian</th>
                            <th>Rating</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentTours as $tour)
                            <tr>
                                <td><strong>{{ $tour->title }}</strong></td>
                                <td>{{ number_format($tour->price) }} VNĐ</td>
                                <td>{{ $tour->duration }}</td>
                                <td>
                                    <i class="bi bi-star-fill" style="color: #ffc107;"></i>
                                    {{ $tour->rating ?? 'N/A' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">Chưa có tour</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Recent Blogs -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Blog gần đây</h5>
                <a href="{{ route('admin.blogs.index') }}" class="btn btn-sm btn-primary">Xem tất cả</a>
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Tiêu đề</th>
                            <th>Danh mục</th>
                            <th>Tác giả</th>
                            <th>Lượt xem</th>
                            <th>Ngày đăng</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentBlogs as $blog)
                            <tr>
                                <td><strong>{{ $blog->title }}</strong></td>
                                <td>{{ $blog->category }}</td>
                                <td>
                                    @if($blog->author && isset($blog->author['name']))
                                        {{ $blog->author['name'] }}
                                    @else
                                        <em>N/A</em>
                                    @endif
                                </td>
                                <td>{{ $blog->views ?? 0 }}</td>
                                <td>
                                    @if($blog->published_at)
                                        {{ $blog->published_at->format('d/m/Y') }}
                                    @else
                                        <em>Chưa đăng</em>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">Chưa có blog</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
