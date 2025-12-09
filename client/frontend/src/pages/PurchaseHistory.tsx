import { useEffect, useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { useAuth } from '@/hooks/useAuth';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import Header from '@/components/Header';
import Footer from '@/components/Footer';
import { ArrowLeft } from 'lucide-react';
import bookingAPI from '@/api/booking';

export default function PurchaseHistory() {
  const navigate = useNavigate();
  const { isLoggedIn, loading: authLoading } = useAuth();
  const [bookings, setBookings] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    if (!authLoading && !isLoggedIn) {
      navigate('/login');
      return;
    }

    if (isLoggedIn) {
      loadBookings();
    }
  }, [isLoggedIn, authLoading, navigate]);

  const loadBookings = async () => {
    setLoading(true);
    try {
      const result = await bookingAPI.getAllUserBookings();
      if (result && typeof result === 'object' && 'data' in result) {
        setBookings((result as any).data);
      }
    } catch (error) {
      console.error('Error loading bookings:', error);
    } finally {
      setLoading(false);
    }
  };

  if (authLoading) {
    return <div className="text-center py-10">Loading...</div>;
  }

  const getStatusBadge = (status: string) => {
    switch (status) {
      case 'completed':
        return <Badge className="bg-green-600">✓ Hoàn Tất</Badge>;
      case 'pending':
        return <Badge className="bg-yellow-600">⏳ Đang Xử Lý</Badge>;
      case 'confirmed':
        return <Badge className="bg-blue-600">✓ Đã Xác Nhận</Badge>;
      default:
        return <Badge>{status}</Badge>;
    }
  };

  const formatPrice = (price: number) => {
    return new Intl.NumberFormat('vi-VN', {
      style: 'currency',
      currency: 'VND',
    }).format(price);
  };

  return (
    <div className="min-h-screen flex flex-col bg-gray-50">
      <Header />

      <div className="flex-grow container mx-auto px-4 py-8">
        <div className="flex items-center gap-4 mb-8">
          <Button
            variant="ghost"
            onClick={() => navigate(-1)}
            className="flex items-center gap-2"
          >
            <ArrowLeft className="h-5 w-5" />
            Quay lại
          </Button>
          <h1 className="text-3xl font-bold">Đặt Chỗ Của Tôi</h1>
        </div>

        {loading && (
          <div className="text-center py-10">
            <div className="animate-spin h-8 w-8 border-4 border-blue-600 border-t-transparent rounded-full mx-auto"></div>
            <p className="mt-4 text-gray-600">Đang tải...</p>
          </div>
        )}

        {!loading && bookings.length === 0 && (
          <div className="bg-white rounded-lg shadow p-8 text-center">
            <p className="text-gray-600 mb-4">Bạn chưa có đơn hàng nào</p>
            <Button onClick={() => navigate('/tours')} className="bg-blue-600 hover:bg-blue-700">
              Khám phá tours
            </Button>
          </div>
        )}

        {!loading && bookings.length > 0 && (
          <div className="space-y-4">
            {bookings.map((booking) => (
              <div
                key={booking.id}
                className="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow"
              >
                <div className="flex justify-between items-start mb-4">
                  <div>
                    <h3 className="text-xl font-bold text-gray-900 mb-1">
                      {booking.order_code}
                    </h3>
                    <p className="text-sm text-gray-600">
                      {new Date(booking.created_at).toLocaleDateString('vi-VN', {
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                      })}
                    </p>
                  </div>
                  <div className="text-right">
                    {getStatusBadge(booking.status)}
                  </div>
                </div>

                <div className="border-t pt-4 mt-4">
                  <h4 className="font-semibold text-gray-900 mb-3">Các mục trong đơn hàng:</h4>
                  <div className="space-y-2">
                    {booking.items.map((item: any, idx: number) => (
                      <div key={idx} className="flex justify-between items-center bg-gray-50 p-3 rounded">
                        <div className="flex-1">
                          <p className="font-medium text-gray-900">{item.name}</p>
                          <p className="text-sm text-gray-600">
                            {item.type === 'tour' ? '🎫 Tour' : '🏨 Phòng'} | SL: {item.quantity}
                          </p>
                        </div>
                        <div className="text-right">
                          <p className="font-bold text-orange-600">
                            {formatPrice(item.price * item.quantity)}
                          </p>
                        </div>
                      </div>
                    ))}
                  </div>
                </div>

                <div className="border-t mt-4 pt-4">
                  <div className="flex justify-between items-center">
                    <span className="text-lg font-bold">Tổng cộng:</span>
                    <span className="text-2xl font-bold text-orange-600">
                      {formatPrice(booking.total_amount)}
                    </span>
                  </div>
                </div>

                {booking.status === 'pending' && (
                  <div className="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded">
                    <p className="text-sm text-yellow-800">
                      ⏳ Đơn hàng này đang chờ thanh toán. Vui lòng hoàn tất quá trình thanh toán.
                    </p>
                    <Button
                      onClick={() => navigate(`/payment-qr/${booking.id}`)}
                      className="mt-2 bg-blue-600 hover:bg-blue-700"
                    >
                      Tiếp tục thanh toán
                    </Button>
                  </div>
                )}
              </div>
            ))}
          </div>
        )}
      </div>
      <Footer />
    </div>
  );
}
