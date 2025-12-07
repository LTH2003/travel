<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel') - Travel App</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- NiceAdmin CSS -->
    <link href="https://demos.adminkit.io/css/adminkit.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        :root {
            --bs-primary: #4680ff;
        }

        body {
            background-color: #f6f9fc;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 250px;
            background: #fff;
            border-right: 1px solid #e9ecef;
            overflow-y: auto;
            z-index: 1000;
        }

        .sidebar-nav {
            padding: 20px 0;
        }

        .sidebar-nav .nav-item {
            margin-bottom: 0;
        }

        .sidebar-nav .nav-link {
            color: #495057;
            padding: 12px 20px;
            border-left: 3px solid transparent;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sidebar-nav .nav-link:hover {
            background-color: #f6f9fc;
            border-left-color: #4680ff;
            color: #4680ff;
        }

        .sidebar-nav .nav-link.active {
            background-color: #f6f9fc;
            border-left-color: #4680ff;
            color: #4680ff;
            font-weight: 600;
        }

        .sidebar-nav .nav-link i {
            width: 20px;
            text-align: center;
        }

        main {
            margin-left: 250px;
            padding-top: 60px;
        }

        .topbar {
            position: fixed;
            top: 0;
            left: 250px;
            right: 0;
            height: 60px;
            background: #fff;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            align-items: center;
            padding: 0 30px;
            z-index: 999;
        }

        .topbar-left {
            flex: 1;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .content {
            padding: 30px;
        }

        .card {
            border: none;
            box-shadow: 0 0.46875rem 2.1875rem rgba(4, 9, 25, 0.03), 0 0.9375rem 1.40625rem rgba(4, 9, 25, 0.13);
            border-radius: 8px;
            margin-bottom: 30px;
        }

        .card-header {
            background-color: #fff;
            border-bottom: 1px solid #e9ecef;
            padding: 20px;
            border-radius: 8px 8px 0 0;
        }

        .card-body {
            padding: 20px;
        }

        .btn-primary {
            background-color: #4680ff;
            border-color: #4680ff;
        }

        .btn-primary:hover {
            background-color: #2a5cdb;
            border-color: #2a5cdb;
        }

        .table-responsive {
            border-radius: 8px;
        }

        .table {
            margin-bottom: 0;
        }

        .table thead th {
            background-color: #f6f9fc;
            border: none;
            color: #495057;
            font-weight: 600;
            padding: 15px 20px;
        }

        .table tbody td {
            padding: 15px 20px;
            border-color: #e9ecef;
            vertical-align: middle;
        }

        .badge {
            padding: 6px 12px;
            border-radius: 4px;
            font-weight: 500;
        }

        .alert {
            border-radius: 8px;
            border: none;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
        }

        .alert-warning {
            background-color: #fff3cd;
            color: #856404;
        }

        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0.46875rem 2.1875rem rgba(4, 9, 25, 0.03);
        }

        .stat-card h6 {
            opacity: 0.9;
            margin-bottom: 10px;
            font-size: 13px;
        }

        .stat-card h3 {
            margin: 0;
            font-size: 28px;
            font-weight: 700;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }

            main {
                margin-left: 0;
            }

            .topbar {
                left: 0;
            }
        }
    </style>

    @yield('extra-css')
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="p-4">
            <h4 class="mb-4" style="color: #4680ff; font-weight: 700;">
                <i class="bi bi-speedometer2"></i> Travel Admin
            </h4>
        </div>

        <nav class="sidebar-nav">
            <ul class="nav flex-column">
                <!-- Dashboard - Visible to all roles -->
                <li class="nav-item">
                    <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                </li>

                <!-- Users - Admin only -->
                @if(auth()->user()->role === 'admin')
                    <li class="nav-item">
                        <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                            <i class="bi bi-people"></i> Người dùng
                        </a>
                    </li>
                @endif

                <!-- Tour - Admin & Tour Manager -->
                @if(in_array(auth()->user()->role, ['admin', 'tour_manager']))
                    <li class="nav-item">
                        <a href="{{ route('admin.tours.index') }}" class="nav-link {{ request()->routeIs('admin.tours.*') ? 'active' : '' }}">
                            <i class="bi bi-pin-map"></i> Tour
                        </a>
                    </li>
                @endif

                <!-- Hotels - Admin & Hotel Manager -->
                @if(in_array(auth()->user()->role, ['admin', 'hotel_manager']))
                    <li class="nav-item">
                        <a href="{{ route('admin.hotels.index') }}" class="nav-link {{ request()->routeIs('admin.hotels.*') || request()->routeIs('admin.hotels.rooms.*') ? 'active' : '' }}">
                            <i class="bi bi-building"></i> Khách sạn
                        </a>
                    </li>
                @endif

                <!-- Blog - Admin only -->
                @if(auth()->user()->role === 'admin')
                    <li class="nav-item">
                        <a href="{{ route('admin.blogs.index') }}" class="nav-link {{ request()->routeIs('admin.blogs.*') ? 'active' : '' }}">
                            <i class="bi bi-newspaper"></i> Blog
                        </a>
                    </li>
                @endif

                <!-- Bookings - Admin only -->
                @if(auth()->user()->role === 'admin')
                    <li class="nav-item">
                        <a href="{{ route('admin.bookings.index') }}" class="nav-link {{ request()->routeIs('admin.bookings.*') ? 'active' : '' }}">
                            <i class="bi bi-cart-check"></i> Đơn hàng
                        </a>
                    </li>
                @endif

                <!-- Contacts - Admin only -->
                @if(auth()->user()->role === 'admin')
                    <li class="nav-item">
                        <a href="{{ route('admin.contacts.index') }}" class="nav-link {{ request()->routeIs('admin.contacts.*') ? 'active' : '' }}">
                            <i class="bi bi-envelope"></i> Tin nhắn
                        </a>
                    </li>
                @endif
            </ul>
        </nav>

        <div class="p-4 mt-5" style="border-top: 1px solid #e9ecef;">
            <form action="{{ route('admin.logout') }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-primary w-100">
                    <i class="bi bi-box-arrow-right"></i> Đăng xuất
                </button>
            </form>
        </div>
    </div>

    <!-- Topbar -->
    <div class="topbar">
        <div class="topbar-left">
            <h5 class="mb-0">@yield('page-title', 'Admin Panel')</h5>
        </div>
        <div class="topbar-right">
            <div class="dropdown">
                <button class="btn btn-sm btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-person-circle"></i> {{ auth()->user()->name }}
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="#"><i class="bi bi-person"></i> Hồ sơ</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form action="{{ route('admin.logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="bi bi-box-arrow-right"></i> Đăng xuất
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main>
        <div class="content">
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Lỗi!</strong>
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    
    @yield('extra-js')
</body>
</html>
