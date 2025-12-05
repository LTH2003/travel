import { useEffect, useState } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import { useBooking } from '@/hooks/useBooking';
import { Button } from '@/components/ui/button';
import Header from '@/components/Header';
import { CheckCircle, Copy, Share2 } from 'lucide-react';

export default function BookingSuccess() {
  const { orderId } = useParams();
  const navigate = useNavigate();
  const { verifyBooking, loading } = useBooking();
  const [bookingData, setBookingData] = useState<any>(null);
  const [copied, setCopied] = useState(false);
  const [qrCode, setQrCode] = useState<string>('');

  useEffect(() => {
    if (orderId) {
      fetchBookingDetails();
    }
  }, [orderId]);

  const fetchBookingDetails = async () => {
    try {
      // Get booking details and QR code from backend
      const verifyResult = await verifyBooking(parseInt(orderId!));
      if (verifyResult) {
        setBookingData(verifyResult);
        // Extract QR code if available
        if (verifyResult.qr_code) {
          setQrCode(verifyResult.qr_code);
        }
      }
    } catch (err) {
      console.error('Error fetching booking:', err);
    }
  };

  const copyToClipboard = () => {
    if (bookingData) {
      navigator.clipboard.writeText(
        `Order: ${bookingData.order_code}\nTotal: ${Number(bookingData.total_amount).toLocaleString('vi-VN')} VNĐ`
      );
      setCopied(true);
      setTimeout(() => setCopied(false), 2000);
    }
  };

  const shareBooking = () => {
    if (navigator.share && bookingData) {
      navigator.share({
        title: 'Booking Confirmation',
        text: `I have booked with TravelVN. Order Code: ${bookingData.order_code}`,
        url: window.location.href,
      });
    }
  };

  if (loading || !bookingData) {
    return (
      <div className="min-h-screen flex flex-col">
        <Header />
        <div className="flex-grow flex items-center justify-center">
          <div className="text-center">
            <div className="animate-spin h-12 w-12 border-4 border-blue-600 border-t-transparent rounded-full mx-auto"></div>
            <p className="mt-4 text-gray-600">Đang tải thông tin booking...</p>
          </div>
        </div>
      </div>
    );
  }

  return (
    <div className="min-h-screen flex flex-col bg-gradient-to-br from-green-50 to-blue-50">
      <Header />

      <div className="flex-grow container mx-auto px-4 py-8">
        <div className="max-w-2xl mx-auto">
          {/* Success Banner */}
          <div className="bg-white rounded-lg shadow-lg p-8 text-center mb-8">
            <CheckCircle className="h-16 w-16 text-green-500 mx-auto mb-4" />
            <h1 className="text-3xl font-bold text-green-600 mb-2">Booking Thành Công!</h1>
            <p className="text-gray-600">
              Đơn hàng của bạn đã được xác nhận. Email xác nhận đã được gửi đến email của bạn.
            </p>
          </div>

          {/* Booking Details */}
          <div className="bg-white rounded-lg shadow-lg p-8 mb-8">
            <h2 className="text-2xl font-bold mb-6 text-gray-900">Chi Tiết Booking</h2>

            <div className="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
              <div>
                <p className="text-gray-600 text-sm">Mã Đơn Hàng</p>
                <p className="text-2xl font-bold text-blue-600">{bookingData.order_code}</p>
              </div>

              <div>
                <p className="text-gray-600 text-sm">Tổng Tiền</p>
                <p className="text-2xl font-bold text-orange-500">
                  {Number(bookingData.total_amount).toLocaleString('vi-VN')} VNĐ
                </p>
              </div>

              <div>
                <p className="text-gray-600 text-sm">Tên Khách Hàng</p>
                <p className="text-lg font-semibold">{bookingData.user_name}</p>
              </div>

              <div>
                <p className="text-gray-600 text-sm">Ngày Xác Nhận</p>
                <p className="text-lg font-semibold">
                  {new Date(bookingData.completed_at).toLocaleDateString('vi-VN')}
                </p>
              </div>
            </div>

            {/* Items */}
            <div className="mb-6">
              <h3 className="text-xl font-bold mb-4">Tours & Khách Sạn Đã Đặt</h3>
              <div className="space-y-3">
                {bookingData.items?.map((item: any, index: number) => (
                  <div
                    key={index}
                    className="bg-gray-50 p-4 rounded-lg border-l-4 border-blue-500"
                  >
                    <p className="font-semibold">{item.name}</p>
                    <p className="text-sm text-gray-600">
                      {item.type === 'App\\Models\\Tour' ? '🎫 Tour' : '🏨 Khách Sạn'} • Số lượng: {item.quantity}
                    </p>
                  </div>
                ))}
              </div>
            </div>

            {/* QR Code */}
            {qrCode && (
              <div className="bg-gray-50 p-6 rounded-lg text-center mb-6">
                <p className="text-sm text-gray-600 mb-4">Mã QR để xác minh</p>
                <img src={qrCode} alt="QR Code" className="h-48 w-48 mx-auto" />
                <p className="text-xs text-gray-500 mt-4">Nhân viên sẽ quét mã này để xác nhận</p>
              </div>
            )}
          </div>

          {/* Action Buttons */}
          <div className="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
            <Button
              onClick={copyToClipboard}
              className="bg-blue-600 hover:bg-blue-700 flex items-center justify-center gap-2"
            >
              <Copy className="h-5 w-5" />
              {copied ? 'Đã Copy' : 'Copy Chi Tiết'}
            </Button>

            <Button
              onClick={shareBooking}
              className="bg-green-600 hover:bg-green-700 flex items-center justify-center gap-2"
            >
              <Share2 className="h-5 w-5" />
              Chia Sẻ
            </Button>

            <Button
              onClick={() => navigate('/purchase-history')}
              className="bg-gray-600 hover:bg-gray-700"
            >
              Xem Lịch Sử Mua
            </Button>
          </div>

          {/* Contact Info */}
          <div className="bg-blue-50 border border-blue-200 rounded-lg p-6 text-center">
            <p className="text-gray-700 mb-2">Có bất kỳ câu hỏi nào?</p>
            <p className="text-blue-600 font-semibold">
              📞 Hotline: 0889421997 | 📧 Email: huyhoahien86@gmail.com
            </p>
            <p className="text-gray-600 text-sm mt-2">Hỗ trợ 24/7</p>
          </div>
        </div>
      </div>
    </div>
  );
}
