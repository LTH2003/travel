<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang Lễ Tân - Check-in Khách</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <style>
        #checkInList {
            font-family: 'Courier New', monospace;
        }
        
        code {
            background-color: #f8f9fa;
            padding: 2px 6px;
            border-radius: 3px;
        }

        .table-sm {
            font-size: 0.9rem;
        }

        .card {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }

        #qrCode {
            font-size: 1.1rem;
            padding: 0.75rem;
            letter-spacing: 1px;
        }

        body {
            background-color: #f5f5f5;
        }

        .container-fluid {
            background-color: white;
            min-height: 100vh;
        }
    </style>
</head>
<body>
<div class="container-fluid mt-4">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h2 mb-0">
                    <i class="bi bi-qr-code"></i> Quét QR Code Check-in
                </h1>
                <div>
                    <span class="badge bg-primary me-2">{{ Auth::user()->name }}</span>
                    <form action="{{ route('admin.logout') }}" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-danger">Đăng xuất</button>
                    </form>
                </div>
            </div>
            <p class="text-muted">{{ now()->format('H:i - d/m/Y') }}</p>
        </div>
    </div>

    <div class="row">
        <!-- QR Code Input Section -->
        <div class="col-lg-4">
            <div class="card border-primary h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-qr-code-scan"></i> Quét QR Code
                    </h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <small>Quét mã QR trên vé hoặc nhập mã đơn hàng để thực hiện check-in</small>
                    </div>

                    <form id="checkInForm">
                        <div class="mb-3">
                            <label for="qrCode" class="form-label">Mã QR / Mã Đơn Hàng</label>
                            <div class="input-group">
                                <input type="text" class="form-control form-control-lg" id="qrCode" 
                                       placeholder="Quét QR hoặc nhập mã..." autofocus>
                                <button type="button" class="btn btn-outline-secondary" id="scanQrBtn" title="Quét QR Code từ Camera">
                                    <i class="bi bi-camera"></i>
                                </button>
                                <button type="button" class="btn btn-outline-secondary" id="uploadImageBtn" title="Tải ảnh lên để quét">
                                    <i class="bi bi-image"></i>
                                </button>
                            </div>
                            <input type="file" id="imageInput" accept="image/*" style="display: none;">
                        </div>

                        <button type="submit" class="btn btn-primary w-100" disabled id="submitBtn">
                            <i class="bi bi-check-circle"></i> Check-in
                        </button>
                    </form>

                    <!-- QR Scanner Modal -->
                    <div class="modal fade" id="qrScannerModal" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">
                                        <i class="bi bi-camera"></i> Quét QR Code
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div id="qrScanner" style="width: 100%; height: 300px; background-color: #000; display: flex; align-items: center; justify-content: center;">
                                        <video id="cameraVideo" style="width: 100%; height: 100%; object-fit: cover;"></video>
                                    </div>
                                    <canvas id="canvas" style="display: none;"></canvas>
                                    <p class="text-muted text-center mt-3 mb-0">
                                        <small>Hướng camera vào mã QR code để quét</small>
                                    </p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Result Message -->
                    <div id="resultMessage" class="mt-3"></div>
                </div>
            </div>
        </div>

        <!-- Statistics Section -->
        <div class="col-lg-8">
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card text-center bg-light">
                        <div class="card-body">
                            <h6 class="card-title text-muted mb-2">Đã Check-in Hôm Nay</h6>
                            <h2 class="text-success mb-0">{{ $checkedInCount }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card text-center bg-light">
                        <div class="card-body">
                            <h6 class="card-title text-muted mb-2">Thời Gian Cập Nhật</h6>
                            <small class="text-muted" id="lastUpdate">Vừa cập nhật</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Export Button -->
            <div class="mb-3">
                <button type="button" class="btn btn-warning" id="exportPdfBtn">
                    <i class="bi bi-file-pdf"></i> Xuất PDF
                </button>
            </div>

            <!-- Check-in List -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-list-check"></i> Danh Sách Check-in Hôm Nay
                    </h5>
                </div>
                <div class="card-body" style="max-height: 500px; overflow-y: auto;">
                    <div id="checkInList">
                        @if($checkedInToday->isEmpty())
                            <p class="text-muted text-center">Chưa có ai check-in hôm nay</p>
                        @else
                            <table class="table table-sm table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Mã Đơn</th>
                                        <th>Khách Hàng</th>
                                        <th>Thời Gian</th>
                                    </tr>
                                </thead>
                                <tbody id="checkInTable">
                                    @foreach($checkedInToday as $order)
                                    <tr>
                                        <td>
                                            <code>{{ $order->order_code }}</code>
                                        </td>
                                        <td>{{ $order->user->name }}</td>
                                        <td>{{ $order->checked_in_at->format('H:i') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js"></script>
<script>
let scannerModal;
let cameraStream;
let isScanning = false;

document.addEventListener('DOMContentLoaded', function() {
    const qrInput = document.getElementById('qrCode');
    const submitBtn = document.getElementById('submitBtn');
    const resultMessage = document.getElementById('resultMessage');
    const checkInForm = document.getElementById('checkInForm');
    const exportPdfBtn = document.getElementById('exportPdfBtn');
    const scanQrBtn = document.getElementById('scanQrBtn');
    const uploadImageBtn = document.getElementById('uploadImageBtn');
    const imageInput = document.getElementById('imageInput');
    const cameraVideo = document.getElementById('cameraVideo');
    const canvas = document.getElementById('canvas');

    scannerModal = new bootstrap.Modal(document.getElementById('qrScannerModal'));

    // Open QR Scanner (Camera)
    scanQrBtn.addEventListener('click', openQRScanner);

    // Upload Image Button
    uploadImageBtn.addEventListener('click', function() {
        imageInput.click();
    });

    // Handle Image Upload
    imageInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            scanQRFromImage(file);
        }
    });

    // Handle modal close
    document.getElementById('qrScannerModal').addEventListener('hidden.bs.modal', function() {
        stopCameraStream();
    });

    // Enable submit button when input has value
    qrInput.addEventListener('input', function() {
        submitBtn.disabled = this.value.trim().length === 0;
    });

    // Handle form submission
    checkInForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        const qrCode = qrInput.value.trim();

        if (!qrCode) {
            showMessage('Vui lòng nhập mã QR hoặc mã đơn hàng', 'warning');
            return;
        }

        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Đang xử lý...';

        try {
            const response = await fetch('{{ route("receptionist.checkIn") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ qr_code: qrCode })
            });

            const data = await response.json();

            if (data.success) {
                showMessage(data.message, 'success');
                qrInput.value = '';
                qrInput.focus();
                refreshCheckInList();
            } else {
                showMessage(data.message, 'danger');
            }
        } catch (error) {
            showMessage('Lỗi kết nối: ' + error.message, 'danger');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="bi bi-check-circle"></i> Check-in';
        }
    });

    // Scan QR Code from Image
    function scanQRFromImage(file) {
        const reader = new FileReader();
        reader.onload = function(event) {
            const img = new Image();
            img.onload = function() {
                const ctx = canvas.getContext('2d');
                canvas.width = img.width;
                canvas.height = img.height;
                ctx.drawImage(img, 0, 0);

                const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                const code = jsQR(imageData.data, imageData.width, imageData.height, {
                    inversionAttempts: 'dontInvert'
                });

                if (code) {
                    const qrData = extractQRData(code.data);
                    qrInput.value = qrData;
                    qrInput.dispatchEvent(new Event('input', { bubbles: true }));
                    submitBtn.disabled = false;
                    showMessage('✅ Quét ảnh thành công! Nhấn Check-in để xác nhận.', 'success');
                    qrInput.focus();
                } else {
                    showMessage('❌ Không tìm thấy mã QR trong ảnh. Vui lòng thử ảnh khác.', 'warning');
                }

                // Reset file input
                imageInput.value = '';
            };
            img.src = event.target.result;
        };
        reader.readAsDataURL(file);
    }

    // Open QR Scanner (Camera)
    async function openQRScanner() {
        scannerModal.show();
        
        try {
            const constraints = {
                video: {
                    facingMode: 'environment',
                    width: { ideal: 1280 },
                    height: { ideal: 720 }
                }
            };

            cameraStream = await navigator.mediaDevices.getUserMedia(constraints);
            cameraVideo.srcObject = cameraStream;
            cameraVideo.setAttribute('playsinline', true);
            cameraVideo.play();

            isScanning = true;
            scanQRCode();
        } catch (err) {
            showMessage('Không thể truy cập camera: ' + err.message, 'danger');
            scannerModal.hide();
        }
    }

    // Stop Camera Stream
    function stopCameraStream() {
        isScanning = false;
        if (cameraStream) {
            cameraStream.getTracks().forEach(track => track.stop());
        }
    }

    // Scan QR Code from Camera
    function scanQRCode() {
        const ctx = canvas.getContext('2d');

        function tick() {
            if (cameraVideo.readyState === cameraVideo.HAVE_ENOUGH_DATA) {
                canvas.width = cameraVideo.videoWidth;
                canvas.height = cameraVideo.videoHeight;
                ctx.drawImage(cameraVideo, 0, 0, canvas.width, canvas.height);

                const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                const code = jsQR(imageData.data, imageData.width, imageData.height, {
                    inversionAttempts: 'dontInvert'
                });

                if (code) {
                    // QR Code found
                    const qrData = extractQRData(code.data);
                    qrInput.value = qrData;
                    qrInput.dispatchEvent(new Event('input', { bubbles: true }));
                    submitBtn.disabled = false;
                    stopCameraStream();
                    scannerModal.hide();
                    showMessage('📱 Quét thành công! Nhấn Check-in để xác nhận.', 'info');
                    qrInput.focus();
                    isScanning = false;
                    return;
                }
            }

            if (isScanning) {
                requestAnimationFrame(tick);
            }
        }

        tick();
    }

    // Extract QR Data - Handle JSON or plain text
    function extractQRData(data) {
        try {
            // Try to parse as JSON
            const json = JSON.parse(data);
            // If it has order_code, return it
            if (json.order_code) {
                return json.order_code;
            }
            // If it has qr_code, return it
            if (json.qr_code) {
                return json.qr_code;
            }
            // If it has id, return it
            if (json.id) {
                return json.id;
            }
            // Otherwise return the whole JSON stringified
            return data;
        } catch (e) {
            // Not JSON, return as is
            return data;
        }
    }

    // Show message function
    function showMessage(message, type) {
        resultMessage.innerHTML = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        setTimeout(() => {
            resultMessage.innerHTML = '';
        }, 5000);
    }

    // Refresh check-in list
    async function refreshCheckInList() {
        try {
            const response = await fetch('{{ route("receptionist.getCheckedInList") }}');
            const data = await response.json();

            if (data.success) {
                const tbody = document.getElementById('checkInTable');
                if (tbody) {
                    tbody.innerHTML = data.data.map(item => `
                        <tr>
                            <td><code>${item.order_code}</code></td>
                            <td>${item.customer_name}</td>
                            <td>${item.checked_in_at}</td>
                        </tr>
                    `).join('');
                }

                // Update count
                const countEl = document.querySelector('.text-success');
                if (countEl) {
                    countEl.textContent = data.count;
                }

                // Update last refresh time
                document.getElementById('lastUpdate').textContent = 'Vừa cập nhật';
            }
        } catch (error) {
            console.error('Lỗi cập nhật danh sách:', error);
        }
    }

    // Export PDF
    exportPdfBtn.addEventListener('click', function() {
        const today = new Date().toISOString().split('T')[0];
        window.location.href = '{{ route("receptionist.exportPDF") }}?date=' + today;
    });

    // Auto-refresh list every 30 seconds
    setInterval(refreshCheckInList, 30000);
});
</script>
</body>
</html>
