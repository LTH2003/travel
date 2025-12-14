<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lịch Sử Check-in - Lễ Tân</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f5f5f5;
        }
        .container-fluid {
            background-color: white;
            min-height: 100vh;
        }
        .card {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
        .table-sm {
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
<div class="container-fluid mt-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h2 mb-0">
                        <i class="bi bi-calendar-event"></i> Lịch Sử Check-in
                    </h1>
                    <p class="text-muted">Xem danh sách khách hàng đã check-in theo ngày</p>
                </div>
                <div>
                    <a href="{{ route('receptionist.dashboard') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Quay Lại
                    </a>
                    <form action="{{ route('admin.logout') }}" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-danger">Đăng xuất</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-funnel"></i> Chọn Ngày
                    </h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('receptionist.history') }}" class="d-flex gap-2">
                        <div class="flex-grow-1">
                            <label for="date" class="form-label">Ngày</label>
                            <input type="date" class="form-control" id="date" name="date" 
                                   value="{{ $selectedDate }}" required>
                        </div>
                        <div class="d-flex align-items-end gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search"></i> Tìm
                            </button>
                            <a href="{{ route('receptionist.history', ['date' => now()->toDateString()]) }}" 
                               class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-clockwise"></i> Hôm Nay
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-file-earmark-pdf"></i> Xuất File
                    </h5>
                </div>
                <div class="card-body">
                    <a href="{{ route('receptionist.exportHistoryPDF', ['date' => $selectedDate]) }}" 
                       class="btn btn-warning w-100">
                        <i class="bi bi-download"></i> Xuất PDF
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card text-center bg-light">
                <div class="card-body">
                    <h6 class="card-title text-muted mb-2">Tổng Check-in</h6>
                    <h2 class="text-success mb-0">{{ $totalCount }}</h2>
                    <small class="text-muted">Ngày {{ \Carbon\Carbon::parse($selectedDate)->format('d/m/Y') }}</small>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card text-center bg-light">
                <div class="card-body">
                    <h6 class="card-title text-muted mb-2">Ngày Chọn</h6>
                    <h5 class="mb-0">{{ \Carbon\Carbon::parse($selectedDate)->format('d/m/Y') }}</h5>
                    <small class="text-muted">{{ \Carbon\Carbon::parse($selectedDate)->format('l', strtotime($selectedDate)) }}</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Check-in List -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="bi bi-list-check"></i> Danh Sách Check-in
            </h5>
        </div>
        <div class="card-body">
            @if($checkedInList->isEmpty())
                <div class="alert alert-info" role="alert">
                    <i class="bi bi-info-circle"></i> Không có khách hàng check-in vào ngày này
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover table-sm">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Mã Đơn Hàng</th>
                                <th>Tên Khách Hàng</th>
                                <th>Điện Thoại</th>
                                <th>Thời Gian Check-in</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($checkedInList as $index => $order)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <code>{{ $order->order_code }}</code>
                                </td>
                                <td>{{ $order->user->name }}</td>
                                <td>{{ $order->user->phone ?? 'N/A' }}</td>
                                <td>{{ $order->checked_in_at->format('H:i:s') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Auto-format date input
    const dateInput = document.getElementById('date');
    if (dateInput) {
        dateInput.addEventListener('change', function() {
            // Date input type="date" handles format automatically
        });
    }
</script>
</body>
</html>
