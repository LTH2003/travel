import { useState, useRef, useEffect } from 'react';
import { Button } from '@/components/ui/button';
import { AlertCircle, X, Camera } from 'lucide-react';
// @ts-ignore - jsQR doesn't have types
import jsQR from 'jsqr';

interface QRScannerProps {
  onScanSuccess: (data: string) => void;
  onClose: () => void;
  isOpen: boolean;
}

export default function QRScanner({ onScanSuccess, onClose, isOpen }: QRScannerProps) {
  const videoRef = useRef<HTMLVideoElement>(null);
  const canvasRef = useRef<HTMLCanvasElement>(null);
  const [error, setError] = useState<string | null>(null);
  const [isScanning, setIsScanning] = useState(false);
  const [facingMode, setFacingMode] = useState<'environment' | 'user'>('environment');
  const scanIntervalRef = useRef<NodeJS.Timeout | null>(null);

  // Bắt đầu camera
  const startCamera = async () => {
    try {
      setError(null);
      setIsScanning(true);

      const constraints = {
        video: {
          facingMode,
          width: { ideal: 1280 },
          height: { ideal: 720 },
        },
        audio: false,
      };

      const stream = await navigator.mediaDevices.getUserMedia(constraints);

      if (videoRef.current) {
        videoRef.current.srcObject = stream;
        videoRef.current.onloadedmetadata = () => {
          videoRef.current?.play();
          startScanning();
        };
      }
    } catch (err: any) {
      setError(
        err.name === 'NotAllowedError'
          ? 'Bạn cần cấp quyền truy cập camera'
          : 'Không thể truy cập camera: ' + err.message
      );
      setIsScanning(false);
    }
  };

  // Dừng camera
  const stopCamera = () => {
    setIsScanning(false);
    if (scanIntervalRef.current) {
      clearInterval(scanIntervalRef.current);
    }
    if (videoRef.current && videoRef.current.srcObject) {
      const tracks = (videoRef.current.srcObject as MediaStream).getTracks();
      tracks.forEach((track) => track.stop());
    }
  };

  // Quét QR code
  const startScanning = () => {
    if (scanIntervalRef.current) {
      clearInterval(scanIntervalRef.current);
    }

    scanIntervalRef.current = setInterval(() => {
      if (videoRef.current && canvasRef.current) {
        const canvas = canvasRef.current;
        const video = videoRef.current;

        // Đặt kích thước canvas bằng video
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;

        const ctx = canvas.getContext('2d');
        if (ctx) {
          // Vẽ video lên canvas
          ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

          // Lấy dữ liệu ảnh
          const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);

          // Quét QR code
          const code = jsQR(imageData.data, canvas.width, canvas.height, {
            inversionAttempts: 'dontInvert',
          });

          if (code) {
            // QR code được phát hiện
            console.log('QR Code detected:', code.data);
            onScanSuccess(code.data);
            stopCamera();
          }
        }
      }
    }, 100); // Quét mỗi 100ms
  };

  // Chuyển đổi camera (trước/sau)
  const toggleCamera = async () => {
    stopCamera();
    setFacingMode((prev) => (prev === 'environment' ? 'user' : 'environment'));

    // Khởi động lại camera với facing mode mới
    setTimeout(() => {
      startCamera();
    }, 500);
  };

  // Xử lý mở/đóng modal
  useEffect(() => {
    if (isOpen) {
      startCamera();
    } else {
      stopCamera();
    }

    return () => {
      stopCamera();
    };
  }, [isOpen, facingMode]);

  if (!isOpen) {
    return null;
  }

  return (
    <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div className="bg-white rounded-lg max-w-md w-full shadow-xl">
        {/* Header */}
        <div className="flex items-center justify-between p-6 border-b">
          <div className="flex items-center gap-2">
            <Camera className="h-5 w-5 text-blue-600" />
            <h2 className="text-lg font-bold text-gray-900">Quét mã QR</h2>
          </div>
          <button
            onClick={onClose}
            className="text-gray-400 hover:text-gray-600"
          >
            <X className="h-5 w-5" />
          </button>
        </div>

        {/* Content */}
        <div className="p-6 space-y-4">
          {/* Video Container */}
          <div className="relative bg-gray-900 rounded-lg overflow-hidden aspect-square">
            <video
              ref={videoRef}
              className="w-full h-full object-cover"
              playsInline
              autoPlay
              muted
            />

            {/* Overlay Grid */}
            {isScanning && (
              <div className="absolute inset-0 flex items-center justify-center">
                <div className="relative w-64 h-64">
                  {/* Corner brackets */}
                  <div className="absolute top-0 left-0 w-8 h-8 border-t-2 border-l-2 border-green-500" />
                  <div className="absolute top-0 right-0 w-8 h-8 border-t-2 border-r-2 border-green-500" />
                  <div className="absolute bottom-0 left-0 w-8 h-8 border-b-2 border-l-2 border-green-500" />
                  <div className="absolute bottom-0 right-0 w-8 h-8 border-b-2 border-r-2 border-green-500" />

                  {/* Center dot */}
                  <div className="absolute inset-0 flex items-center justify-center">
                    <div className="w-2 h-2 bg-green-500 rounded-full animate-pulse" />
                  </div>
                </div>
              </div>
            )}

            {/* Loading overlay */}
            {!isScanning && (
              <div className="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center">
                <div className="text-center">
                  <div className="w-12 h-12 rounded-full border-4 border-blue-300 border-t-blue-600 animate-spin mx-auto mb-3" />
                  <p className="text-white text-sm">Đang khởi động camera...</p>
                </div>
              </div>
            )}
          </div>

          {/* Canvas (hidden - dùng cho xử lý) */}
          <canvas ref={canvasRef} className="hidden" />

          {/* Error Message */}
          {error && (
            <div className="flex items-start gap-3 p-3 bg-red-50 rounded-lg border border-red-200">
              <AlertCircle className="h-5 w-5 text-red-600 flex-shrink-0 mt-0.5" />
              <div>
                <p className="text-sm font-medium text-red-900">Lỗi:</p>
                <p className="text-sm text-red-700">{error}</p>
              </div>
            </div>
          )}

          {/* Instructions */}
          {!error && (
            <div className="p-3 bg-blue-50 rounded-lg border border-blue-200">
              <p className="text-sm text-blue-900">
                ✓ Hướng camera về mã QR và đặt nó vào khung để quét
              </p>
            </div>
          )}

          {/* Buttons */}
          <div className="flex gap-3 pt-2">
            <Button
              onClick={toggleCamera}
              disabled={!isScanning}
              variant="outline"
              className="flex-1"
            >
              Chuyển Camera
            </Button>
            <Button
              onClick={onClose}
              variant="outline"
              className="flex-1"
            >
              Hủy
            </Button>
          </div>

          {/* Tips */}
          <div className="text-xs text-gray-500 text-center">
            💡 Đảm bảo ánh sáng đủ để quét chính xác
          </div>
        </div>
      </div>
    </div>
  );
}
