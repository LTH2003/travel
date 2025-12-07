import { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import { useAuth } from '@/hooks/useAuth';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { CreditCard, Smartphone, Landmark, CheckCircle, Plane, Hotel, Check } from 'lucide-react';
import Header from '@/components/Header';
import { useCart, HotelCartItem, TourCartItem } from '@/hooks/useCart';
import { useTitle } from '@/hooks/useTitle';
import { toast } from '@/hooks/use-toast';
import { format } from 'date-fns';
import { vi } from 'date-fns/locale';
import { createOrder, initiateVietQRPayment, initiateMoMoPayment, initiateEWalletPayment } from '@/api/payment';

export default function Checkout() {
  useTitle('Thanh toán - TravelVN');
  const navigate = useNavigate();
  const { user } = useAuth();
  const { items, getTotal, clearCart } = useCart();
  const [paymentMethod, setPaymentMethod] = useState('bank');
  const [selectedWallet, setSelectedWallet] = useState('momo');
  const [isProcessing, setIsProcessing] = useState(false);
  const [orderPlaced, setOrderPlaced] = useState(false);
  const [guestInfo, setGuestInfo] = useState({
    firstName: '',
    lastName: '',
    email: '',
    phone: '',
    address: '',
  });

  // Cập nhật thông tin khách hàng khi user thay đổi
  useEffect(() => {
    if (user) {
      const nameParts = user.name ? user.name.split(' ') : [];
      setGuestInfo({
        firstName: nameParts[0] || '',
        lastName: nameParts.slice(1).join(' ') || '',
        email: user.email || '',
        phone: user.phone || '',
        address: user.address || '',
      });
    }
  }, [user]);

  const formatPrice = (price: number) => {
    return new Intl.NumberFormat('vi-VN', {
      style: 'currency',
      currency: 'VND',
    }).format(price);
  };

  const calculateNights = (checkIn: Date, checkOut: Date) => {
    const diffTime = Math.abs(checkOut.getTime() - checkIn.getTime());
    return Math.ceil(diffTime / (1000 * 60 * 60 * 24));
  };

  const handleInputChange = (e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement>) => {
    const { name, value } = e.target;
    setGuestInfo((prev) => ({
      ...prev,
      [name]: value,
    }));
  };

  const handlePlaceOrder = async (e: React.FormEvent) => {
    e.preventDefault();

    // Validate
    if (!guestInfo.firstName || !guestInfo.lastName || !guestInfo.email || !guestInfo.phone) {
      toast({
        title: 'Lỗi',
        description: 'Vui lòng điền đầy đủ thông tin khách hàng',
        variant: 'destructive',
      });
      return;
    }

    setIsProcessing(true);

    try {
      // Create order first
      const orderData = {
        items: items.map(item => ({
          id: item.type === 'hotel' ? (item as HotelCartItem).hotelId : (item as TourCartItem).tourId,
          type: item.type,
          name: item.type === 'hotel' ? (item as HotelCartItem).roomName : (item as TourCartItem).tourName,
          quantity: item.quantity,
          price: item.type === 'hotel' ? (item as HotelCartItem).roomPrice : (item as TourCartItem).tourPrice,
          totalPrice: item.totalPrice,
        })),
        total_amount: getTotal(),
      };

      console.log('Creating order with data:', orderData);
      const orderResponse = await createOrder(orderData);
      console.log('Order response:', orderResponse);

      const orderId = (orderResponse as any)?.id || (orderResponse as any)?.data?.id || (orderResponse as any)?.order?.id;

      if (!orderId) {
        console.error('No order ID found in response:', orderResponse);
        throw new Error('Không thể tạo đơn hàng');
      }

      console.log('Order ID:', orderId, 'Payment method:', paymentMethod);

      // Don't clear cart yet - only clear after payment is confirmed
      // Store orderId to track pending payment
      localStorage.setItem('pendingOrderId', orderId.toString());

      // Route to payment page based on method
      if (paymentMethod === 'bank') {
        // VietQR payment
        console.log('Navigating to VietQR payment');
        navigate(`/payment-qr/${orderId}?method=vietqr`);
      } else if (paymentMethod === 'ewallet') {
        // E-wallet payment
        console.log('Navigating to E-wallet payment:', selectedWallet);
        navigate(`/payment-qr/${orderId}?method=${selectedWallet}`);
      } else if (paymentMethod === 'card') {
        // Card payment - you can implement Stripe integration
        console.log('Navigating to Card payment');
        navigate(`/payment-card/${orderId}`);
      } else {
        console.warn('Unknown payment method:', paymentMethod);
        throw new Error(`Phương thức thanh toán không hợp lệ: ${paymentMethod}`);
      }
    } catch (error: any) {
      console.error('Order creation error:', error);
      toast({
        title: 'Lỗi',
        description: error.message || 'Không thể tạo đơn hàng, vui lòng thử lại',
        variant: 'destructive',
      });
      setIsProcessing(false);
    }
  };

  if (items.length === 0) {
    return (
      <div className="min-h-screen bg-gray-50">
        <Header />
        <div className="container mx-auto px-4 py-16">
          <div className="max-w-2xl mx-auto text-center">
            <h1 className="text-2xl font-bold text-gray-900 mb-4">Giỏ hàng trống</h1>
            <p className="text-gray-600 mb-8">Không có đơn hàng nào để thanh toán</p>
            <Button onClick={() => navigate('/hotels')} className="bg-orange-500 hover:bg-orange-600">
              Quay lại chọn phòng
            </Button>
          </div>
        </div>
      </div>
    );
  }

  if (orderPlaced) {
    return (
      <div className="min-h-screen bg-gray-50">
        <Header />
        <div className="container mx-auto px-4 py-16">
          <div className="max-w-2xl mx-auto">
            <Card className="border-green-200 bg-green-50">
              <CardContent className="p-8 text-center">
                <CheckCircle className="h-16 w-16 text-green-600 mx-auto mb-4" />
                <h1 className="text-2xl font-bold text-green-900 mb-2">Đặt phòng thành công!</h1>
                <p className="text-green-700 mb-6">
                  Cảm ơn bạn đã đặt phòng. Email xác nhận sẽ được gửi đến {guestInfo.email}
                </p>
                <p className="text-sm text-green-600 mb-6">Đang chuyển hướng...</p>
                <Button onClick={() => navigate('/booking')} className="bg-green-600 hover:bg-green-700">
                  Xem đơn hàng của tôi
                </Button>
              </CardContent>
            </Card>
          </div>
        </div>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-gray-50">
      <Header />
      <div className="container mx-auto px-4 py-8">
        <h1 className="text-3xl font-bold text-gray-900 mb-8">Thanh toán</h1>

        <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
          {/* Checkout Form */}
          <div className="lg:col-span-2 space-y-6">
            {/* Guest Information */}
            <Card>
              <CardHeader>
                <CardTitle>Thông tin khách hàng</CardTitle>
              </CardHeader>
              <CardContent className="space-y-4">
                <div className="grid grid-cols-2 gap-4">
                  <div>
                    <label className="text-sm font-medium mb-1 block">Họ *</label>
                    <p className="text-sm text-gray-900 p-2 bg-gray-50 rounded">
                      {guestInfo.firstName || '-'}
                    </p>
                  </div>
                  <div>
                    <label className="text-sm font-medium mb-1 block">Tên *</label>
                    <p className="text-sm text-gray-900 p-2 bg-gray-50 rounded">
                      {guestInfo.lastName || '-'}
                    </p>
                  </div>
                </div>

                <div>
                  <label className="text-sm font-medium mb-1 block">Email *</label>
                  <p className="text-sm text-gray-900 p-2 bg-gray-50 rounded">
                    {guestInfo.email || '-'}
                  </p>
                </div>

                <div>
                  <label className="text-sm font-medium mb-1 block">Số điện thoại *</label>
                  <p className="text-sm text-gray-900 p-2 bg-gray-50 rounded">
                    {guestInfo.phone || '-'}
                  </p>
                </div>

                <div>
                  <label className="text-sm font-medium mb-1 block">Địa chỉ</label>
                  <p className="text-sm text-gray-900 p-2 bg-gray-50 rounded">
                    {guestInfo.address || '-'}
                  </p>
                </div>
              </CardContent>
            </Card>

            {/* Payment Method */}
            <Card>
              <CardHeader>
                <CardTitle>Phương thức thanh toán</CardTitle>
                <CardDescription>Chọn phương thức thanh toán của bạn</CardDescription>
              </CardHeader>
              <CardContent>
                <Tabs value={paymentMethod} onValueChange={setPaymentMethod}>
                  <TabsList className="grid w-full grid-cols-3 mb-6">
                    <TabsTrigger value="card" disabled={isProcessing}>
                      <CreditCard className="h-4 w-4 mr-2" />
                      Thẻ
                    </TabsTrigger>
                    <TabsTrigger value="bank" disabled={isProcessing}>
                      <Landmark className="h-4 w-4 mr-2" />
                      Chuyển khoản
                    </TabsTrigger>
                    <TabsTrigger value="ewallet" disabled={isProcessing}>
                      <Smartphone className="h-4 w-4 mr-2" />
                      E-Wallet
                    </TabsTrigger>
                  </TabsList>

                  <TabsContent value="card" className="space-y-4">
                    <div>
                      <label className="text-sm font-medium mb-1 block">Số thẻ</label>
                      <Input
                        placeholder="1234 5678 9012 3456"
                        disabled={isProcessing}
                      />
                    </div>
                    <div className="grid grid-cols-2 gap-4">
                      <div>
                        <label className="text-sm font-medium mb-1 block">Hạn sử dụng</label>
                        <Input
                          placeholder="MM/YY"
                          disabled={isProcessing}
                        />
                      </div>
                      <div>
                        <label className="text-sm font-medium mb-1 block">CVV</label>
                        <Input
                          placeholder="123"
                          disabled={isProcessing}
                        />
                      </div>
                    </div>
                  </TabsContent>

                  <TabsContent value="bank" className="space-y-4">
                    <div className="bg-blue-50 p-4 rounded-lg">
                      <h4 className="font-medium mb-2">Thông tin chuyển khoản</h4>
                      <div className="space-y-1 text-sm text-gray-700">
                        <p><span className="font-medium">Ngân hàng:</span> Vietinbank</p>
                        <p><span className="font-medium">Tên TK:</span> Lê Thanh Huy</p>
                        <p><span className="font-medium">Số TK:</span> 107875593153</p>
                        <p><span className="font-medium">Nội dung:</span> Booking {format(new Date(), 'ddMMyyyy')}</p>
                      </div>
                    </div>
                  </TabsContent>

                  <TabsContent value="ewallet" className="space-y-4">
                    <div className="space-y-2">
                      <label className="flex items-center space-x-2">
                        <input 
                          type="radio" 
                          name="ewallet" 
                          value="momo" 
                          checked={selectedWallet === 'momo'}
                          onChange={(e) => setSelectedWallet(e.target.value)}
                          disabled={isProcessing} 
                        />
                        <span>Momo</span>
                      </label>
                      <label className="flex items-center space-x-2">
                        <input 
                          type="radio" 
                          name="ewallet" 
                          value="zalopay"
                          checked={selectedWallet === 'zalopay'}
                          onChange={(e) => setSelectedWallet(e.target.value)}
                          disabled={isProcessing} 
                        />
                        <span>ZaloPay</span>
                      </label>
                      <label className="flex items-center space-x-2">
                        <input 
                          type="radio" 
                          name="ewallet" 
                          value="paypal"
                          checked={selectedWallet === 'paypal'}
                          onChange={(e) => setSelectedWallet(e.target.value)}
                          disabled={isProcessing} 
                        />
                        <span>PayPal</span>
                      </label>
                    </div>
                  </TabsContent>
                </Tabs>
              </CardContent>
            </Card>

          </div>

          {/* Order Summary */}
          <div className="lg:col-span-1">
            <Card className="sticky top-24">
              <CardHeader>
                <CardTitle>Tóm tắt đơn hàng</CardTitle>
              </CardHeader>
              <CardContent className="space-y-4">
                {/* Bookings List */}
                <div className="space-y-3 border-b pb-4 max-h-64 overflow-y-auto">
                  {items.map((item, index) => (
                    <div key={item.id} className="text-sm">
                      <div className="flex justify-between mb-1 items-start">
                        <div className="flex items-center gap-2">
                          {item.type === 'hotel' ? (
                            <Hotel className="h-4 w-4 text-orange-600 mt-0.5" />
                          ) : (
                            <Plane className="h-4 w-4 text-blue-600 mt-0.5" />
                          )}
                          <span className="font-medium">
                            {item.type === 'hotel' ? (item as HotelCartItem).roomName : (item as TourCartItem).tourName}
                          </span>
                        </div>
                        <span className="font-medium">{formatPrice(item.totalPrice)}</span>
                      </div>
                      <div className="text-xs text-gray-600 ml-6">
                        {item.type === 'hotel' ? (
                          <>
                            <p>{(item as HotelCartItem).hotelName}</p>
                            <p>
                              {format((item as HotelCartItem).checkIn, 'dd/MM', { locale: vi })} -{' '}
                              {format((item as HotelCartItem).checkOut, 'dd/MM', { locale: vi })} ({calculateNights((item as HotelCartItem).checkIn, (item as HotelCartItem).checkOut)} đêm)
                            </p>
                            <p>{item.quantity} phòng × {formatPrice((item as HotelCartItem).roomPrice)}/đêm</p>
                          </>
                        ) : (
                          <>
                            <p>{(item as TourCartItem).tourName}</p>
                            <p>{(item as TourCartItem).duration} ngày</p>
                            <p>{item.quantity} × {formatPrice((item as TourCartItem).tourPrice)}/khách</p>
                          </>
                        )}
                      </div>
                      {index < items.length - 1 && <div className="border-t my-2" />}
                    </div>
                  ))}
                </div>

                {/* Cost Breakdown */}
                <div className="space-y-2">
                  <div className="flex justify-between">
                    <span className="text-gray-600">Tổng tiền phòng</span>
                    <span>{formatPrice(getTotal())}</span>
                  </div>
                  <div className="flex justify-between">
                    <span className="text-gray-600">Phí dịch vụ</span>
                    <span>Miễn phí</span>
                  </div>
                  <div className="border-t pt-2 flex justify-between text-lg font-bold text-orange-600">
                    <span>Tổng cộng</span>
                    <span>{formatPrice(getTotal())}</span>
                  </div>
                </div>

                {/* Place Order Button */}
                <Button
                  onClick={handlePlaceOrder}
                  disabled={isProcessing}
                  className="w-full bg-orange-500 hover:bg-orange-600 mt-6"
                  size="lg"
                >
                  {isProcessing ? 'Đang xử lý...' : (
                    <>
                      <Check className="h-4 w-4 mr-2" />
                      Xác nhận và thanh toán
                    </>
                  )}
                </Button>
              </CardContent>
            </Card>
          </div>
        </div>
      </div>
    </div>
  );
}
