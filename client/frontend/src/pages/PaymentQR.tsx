import { useState, useEffect } from 'react';
import QRCode from 'qrcode';
import { useParams, useLocation, useNavigate, useSearchParams } from 'react-router-dom';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { useTitle } from '@/hooks/useTitle';
import { toast } from '@/hooks/use-toast';
import { initiateVietQRPayment, initiateMoMoPayment, initiateEWalletPayment, initiateZaloPayPayment, getOrder } from '@/api/payment';
import { CheckCircle, Clock, AlertCircle, Copy, Check, Download, ExternalLink } from 'lucide-react';
import Header from '@/components/Header';
import bookingAPI from '@/api/booking';
import { useCart } from '@/hooks/useCart';

export default function PaymentQR() {
  useTitle('Thanh toán - TravelVN');
  const navigate = useNavigate();
  const { orderId } = useParams();
  const location = useLocation();
  const [searchParams] = useSearchParams();
  
  // Get method from query param (e.g., ?method=vietqr)
  const method = searchParams.get('method') || location.state?.method || 'vietqr';

  const [qrCode, setQrCode] = useState<string | null>(null);
  const [paymentInfo, setPaymentInfo] = useState<any>(null);
  const [isLoading, setIsLoading] = useState(true);
  const [copiedAccount, setCopiedAccount] = useState(false);
  const [copiedDesc, setCopiedDesc] = useState(false);
  const [paymentStatus, setPaymentStatus] = useState<'pending' | 'completed' | 'failed'>('pending');
  const [timeRemaining, setTimeRemaining] = useState(900); // 15 minutes in seconds
  const [successMessage, setSuccessMessage] = useState(false);
  const { clearCart } = useCart();

  useEffect(() => {
    const loadPaymentQR = async () => {
      if (!orderId) {
        toast({
          title: 'Lỗi',
          description: 'Không tìm thấy đơn hàng',
          variant: 'destructive',
        });
        navigate('/checkout');
        return;
      }

      try {
        setIsLoading(true);

        // Get order details first
        const order = await getOrder(parseInt(orderId));

        // Generate QR based on method
        let qrResponse;
        if (method === 'vietqr') {
          qrResponse = await initiateVietQRPayment(parseInt(orderId));
        } else if (method === 'momo') {
          qrResponse = await initiateMoMoPayment(parseInt(orderId));
        } else if (method === 'zalopay') {
          qrResponse = await initiateZaloPayPayment(parseInt(orderId));
        } else {
          qrResponse = await initiateEWalletPayment(parseInt(orderId), method);
        }

        setPaymentInfo(qrResponse);
        
        // Support multiple possible response shapes from backend
        let qrSrc = qrResponse?.qr_code
          || qrResponse?.qrCodeUrl
          || qrResponse?.qrData?.qrUrl
          || qrResponse?.qrData?.qr_url
          || qrResponse?.qrUrl
          || qrResponse?.momoData?.qrCodeUrl
          || qrResponse?.momoData?.payload?.qrCodeUrl
          || null;

        // If this is a VietQR flow and we don't have a QR yet, generate from EMV payload
        if (method === 'vietqr' && orderId && !qrSrc) {
          try {
            // Get EMV payload from response
            const emvPayload = qrResponse?.qrData?.emvPayload || qrResponse?.emvPayload;
            
            if (emvPayload) {
              // Generate QR code client-side from EMV payload
              const dataUri = await QRCode.toDataURL(emvPayload, { 
                margin: 0, 
                width: 400,
                errorCorrectionLevel: 'M'
              });
              qrSrc = dataUri;
            }
          } catch (genErr) {
            console.error('Failed to generate VietQR:', genErr);
          }
        }

        setQrCode(qrSrc);
      } catch (error: any) {
        toast({
          title: 'Lỗi',
          description: error.response?.data?.message || 'Không thể tải mã QR',
          variant: 'destructive',
        });
      } finally {
        setIsLoading(false);
      }
    };

    loadPaymentQR();
  }, [orderId, method, navigate]);

  // Timer countdown
  useEffect(() => {
    if (paymentStatus !== 'pending') return;

    const interval = setInterval(() => {
      setTimeRemaining((prev) => {
        if (prev <= 1) {
          clearInterval(interval);
          setPaymentStatus('failed');
          toast({
            title: 'Hết thời gian',
            description: 'Mã QR đã hết hạn. Vui lòng tạo đơn hàng mới.',
            variant: 'destructive',
          });
          return 0;
        }
        return prev - 1;
      });
    }, 1000);

    return () => clearInterval(interval);
  }, [paymentStatus]);

  // Polling - Check payment status automatically
  useEffect(() => {
    if (paymentStatus !== 'pending' || !orderId) return;

    const pollPaymentStatus = async () => {
      try {
        const order: any = await getOrder(parseInt(orderId));

        if (order?.status === 'completed') {
          setPaymentStatus('completed');
          
          // Complete booking on backend
          try {
            await bookingAPI.completeBooking(parseInt(orderId));
          } catch (err) {
            console.error('Error completing booking:', err);
          }
          
          setSuccessMessage(true);
          toast({
            title: 'Thành công!',
            description: 'Thanh toán đã được xác nhận và booking được hoàn tất',
          });

          // Clear cart after successful payment
          clearCart();
          localStorage.removeItem('pendingOrderId');

          // Redirect to purchase history page sau 2 giây
          setTimeout(() => {
            navigate(`/purchase-history`);
          }, 2000);
        }
      } catch (error) {
        console.log('Polling check skipped:', error);
        // Silently ignore errors during polling
      }
    };

    // Poll every 5 seconds
    const pollInterval = setInterval(pollPaymentStatus, 5000);

    return () => clearInterval(pollInterval);
  }, [paymentStatus, orderId, navigate]);

  const copyToClipboard = (text: string) => {
    try {
      navigator.clipboard.writeText(text || '');
    } catch (e) {
      // ignore clipboard errors in some environments
    }
  };

  // Format time remaining
  const formatTime = (seconds: number) => {
    const mins = Math.floor(seconds / 60);
    const secs = seconds % 60;
    return `${mins}:${secs < 10 ? '0' : ''}${secs}`;
  };

  const getFallbackQr = (src: string | null) => {
    if (!src) return null;
    // Fallback: encode the original qr URL (or raw data) into a public QR generator
    return `https://api.qrserver.com/v1/create-qr-code/?size=400x400&data=${encodeURIComponent(src)}`;
  };

  const getMethodLabel = () => {
    switch (method) {
      case 'vietqr':
        return 'Chuyển khoản QR (VietQR)';
      case 'momo':
        return 'Momo';
      case 'zalopay':
        return 'ZaloPay';
      case 'paypal':
        return 'PayPal';
      default:
        return 'Thanh toán';
    }
  };

  const getMethodDescription = () => {
    switch (method) {
      case 'vietqr':
        return 'Quét mã QR dưới đây bằng ứng dụng ngân hàng của bạn để hoàn tất thanh toán';
      case 'momo':
        return 'Quét mã QR dưới đây bằng ứng dụng Momo để hoàn tất thanh toán';
      case 'zalopay':
        return 'Quét mã QR dưới đây bằng ứng dụng ZaloPay để hoàn tất thanh toán';
      case 'paypal':
        return 'Quét mã QR dưới đây bằng ứng dụng PayPal để hoàn tất thanh toán';
      default:
        return 'Quét mã QR dưới đây để hoàn tất thanh toán';
    }
  };

  if (isLoading) {
    return (
      <div className="min-h-screen bg-gray-50">
        <Header />
        <div className="container mx-auto px-4 py-16">
          <div className="max-w-2xl mx-auto text-center">
            <Clock className="h-12 w-12 text-orange-500 mx-auto mb-4 animate-spin" />
            <h1 className="text-2xl font-bold text-gray-900 mb-2">Đang tạo mã QR...</h1>
            <p className="text-gray-600">Vui lòng chờ trong giây lát</p>
          </div>
        </div>
      </div>
    );
  }

  // Success screen
  if (successMessage) {
    return (
      <div className="min-h-screen bg-gray-50">
        <Header />
        <div className="container mx-auto px-4 py-16">
          <div className="max-w-2xl mx-auto text-center">
            <div className="mb-6 animate-bounce">
              <CheckCircle className="h-16 w-16 text-green-600 mx-auto" />
            </div>
            <h1 className="text-3xl font-bold text-gray-900 mb-2">Thanh toán thành công!</h1>
            <p className="text-gray-600 mb-4">Đơn hàng #{orderId} của bạn đã được xác nhận</p>
            <p className="text-gray-600 mb-8">Đang chuyển hướng đến trang booking success...</p>
            <Button
              onClick={() => navigate(`/booking-success/${orderId}`)}
              className="bg-green-600 hover:bg-green-700"
            >
              Xem Chi Tiết Booking
            </Button>
          </div>
        </div>
      </div>
    );
  }

  // Failed screen
  if (paymentStatus === 'failed' && timeRemaining === 0) {
    return (
      <div className="min-h-screen bg-gray-50">
        <Header />
        <div className="container mx-auto px-4 py-16">
          <div className="max-w-2xl mx-auto text-center">
            <AlertCircle className="h-16 w-16 text-red-600 mx-auto mb-6" />
            <h1 className="text-3xl font-bold text-gray-900 mb-2">Hết thời gian</h1>
            <p className="text-gray-600 mb-8">Mã QR đã hết hạn. Vui lòng tạo đơn hàng mới.</p>
            <div className="flex gap-3">
              <Button
                onClick={() => navigate('/checkout')}
                variant="outline"
              >
                Quay lại
              </Button>
              <Button
                onClick={() => window.location.reload()}
                className="bg-orange-500 hover:bg-orange-600"
              >
                Tạo lại
              </Button>
            </div>
          </div>
        </div>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-gray-50">
      <Header />
      <div className="container mx-auto px-4 py-8">
        <div className="max-w-2xl mx-auto">
          <h1 className="text-3xl font-bold text-gray-900 mb-8">Thanh toán</h1>

          <Card>
            <CardHeader>
              <CardTitle>Phương thức: {getMethodLabel()}</CardTitle>
              <CardDescription>{getMethodDescription()}</CardDescription>
            </CardHeader>

            <CardContent className="space-y-6">
              {/* Status */}
              <div className="flex items-center space-x-2 p-4 bg-blue-50 rounded-lg">
                <Clock className="h-5 w-5 text-blue-600" />
                <span className="text-sm font-medium text-blue-900">
                  Chờ thanh toán - Đơn hàng #{orderId}
                </span>
              </div>

              {/* Timer Display */}
              <div className="flex items-center justify-between p-4 bg-gradient-to-r from-orange-50 to-orange-100 rounded-lg border border-orange-300">
                <span className="text-sm font-medium text-orange-900">Thời gian còn lại:</span>
                <span
                  className={`text-2xl font-bold ${
                    timeRemaining < 60
                      ? 'text-red-600 animate-pulse'
                      : timeRemaining < 300
                      ? 'text-orange-600'
                      : 'text-orange-900'
                  }`}
                >
                  {formatTime(timeRemaining)}
                </span>
              </div>

              {/* QR Code Display */}
              {qrCode && (
                <div className="text-center">
                  <div className="bg-white p-6 rounded-lg inline-block border-2 border-gray-200">
                    <img
                      src={qrCode}
                      alt="Payment QR Code"
                      className="w-64 h-64 object-contain"
                      id="payment-qr-image"
                      onError={(e) => {
                        // If the original qrUrl fails (404 or CORS), use a fallback QR generator
                        const fallback = getFallbackQr(paymentInfo?.qrData?.qrUrl || paymentInfo?.qrUrl || qrCode);
                        if (fallback) {
                          const img = e.currentTarget as HTMLImageElement;
                          img.src = fallback;
                        }
                      }}
                    />
                  </div>
                  <div className="mt-3 flex items-center justify-center gap-3">
                    <Button
                      onClick={() => {
                        // Download QR - works if qrCode is a data URL or image URL
                        const link = document.createElement('a');
                        link.href = qrCode!;
                        link.download = `qr_order_${orderId}.png`;
                        document.body.appendChild(link);
                        link.click();
                        link.remove();
                      }}
                      variant="outline"
                    >
                      <Download className="h-4 w-4 mr-2" />
                      Tải mã QR
                    </Button>

                    {paymentInfo?.momoData?.deeplink && (
                      <Button
                        onClick={() => window.open(paymentInfo.momoData.deeplink, '_blank')}
                      >
                        <ExternalLink className="h-4 w-4 mr-2" />
                        Mở ứng dụng
                      </Button>
                    )}

                    {paymentInfo?.checkoutUrl && (
                      <Button
                        onClick={() => window.open(paymentInfo.checkoutUrl, '_blank')}
                      >
                        <ExternalLink className="h-4 w-4 mr-2" />
                        Thanh toán
                      </Button>
                    )}
                  </div>
                  <p className="text-sm text-gray-600 mt-4">
                    Quét mã QR bằng điện thoại để thanh toán
                  </p>
                </div>
              )}

              {/* Payment Info */}
              {method === 'vietqr' && paymentInfo?.account_number && (
                <div className="bg-gray-50 p-4 rounded-lg space-y-3">
                  <h3 className="font-semibold text-gray-900">Thông tin chuyển khoản</h3>
                  <div className="space-y-2 text-sm">
                    <div className="flex justify-between items-center">
                      <span className="text-gray-600">Ngân hàng:</span>
                      <span className="font-medium">{paymentInfo?.bank_name || 'N/A'}</span>
                    </div>
                    <div className="flex justify-between items-center">
                      <span className="text-gray-600">Số tài khoản:</span>
                      <div className="flex items-center gap-2">
                        <span className="font-medium">{paymentInfo?.account_number}</span>
                        <button
                          onClick={() => {
                            copyToClipboard(paymentInfo?.account_number);
                            setCopiedAccount(true);
                            setTimeout(() => setCopiedAccount(false), 2000);
                          }}
                          className="text-gray-500 hover:text-gray-700"
                        >
                          {copiedAccount ? (
                            <Check className="h-4 w-4 text-green-600" />
                          ) : (
                            <Copy className="h-4 w-4" />
                          )}
                        </button>
                      </div>
                    </div>
                    <div className="flex justify-between items-center">
                      <span className="text-gray-600">Tên tài khoản:</span>
                      <span className="font-medium">{paymentInfo?.account_name || 'N/A'}</span>
                    </div>
                    <div className="flex justify-between items-center">
                      <span className="text-gray-600">Số tiền:</span>
                      <span className="font-medium text-orange-600">{paymentInfo?.amount?.toLocaleString('vi-VN')} ₫</span>
                    </div>
                    <div className="flex justify-between items-center">
                      <span className="text-gray-600">Nội dung:</span>
                        <div className="flex items-center gap-2">
                          <span className="font-medium text-sm">{paymentInfo?.description}</span>
                          <button
                            onClick={() => {
                              copyToClipboard(paymentInfo?.description);
                              setCopiedDesc(true);
                              setTimeout(() => setCopiedDesc(false), 2000);
                            }}
                            className="text-gray-500 hover:text-gray-700"
                          >
                            {copiedDesc ? (
                              <Check className="h-4 w-4 text-green-600" />
                            ) : (
                              <Copy className="h-4 w-4" />
                            )}
                          </button>
                        </div>
                    </div>
                  </div>
                </div>
              )}

              {/* Warning */}
              <div className="flex items-start space-x-2 p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                <AlertCircle className="h-5 w-5 text-yellow-600 mt-0.5 flex-shrink-0" />
                <div className="text-sm text-yellow-800">
                  <p className="font-medium mb-1">Lưu ý quan trọng:</p>
                  <ul className="list-disc list-inside space-y-1">
                    <li>Không đóng hoặc làm mới trang này trong quá trình thanh toán</li>
                    <li>Thanh toán sẽ được xác nhận <strong>tự động</strong> sau khi tiền về tài khoản (kiểm tra mỗi 5 giây)</li>
                    <li>Nếu có vấn đề, vui lòng liên hệ với bộ phận hỗ trợ</li>
                  </ul>
                </div>
              </div>

              {/* Action Buttons */}
              <div className="flex flex-col sm:flex-row gap-3 pt-4">
                <Button
                  variant="outline"
                  onClick={() => navigate('/checkout')}
                  className="flex-1"
                >
                  Quay lại
                </Button>
                <Button
                  onClick={async () => {
                    // Mark payment as completed
                    setPaymentStatus('completed');
                    
                    // Complete booking on backend
                    try {
                      await bookingAPI.completeBooking(parseInt(orderId!));
                    } catch (err) {
                      console.error('Error completing booking:', err);
                    }
                    
                    setSuccessMessage(true);
                    toast({
                      title: 'Thành công!',
                      description: 'Thanh toán đã được xác nhận và booking được hoàn tất',
                    });

                    // Clear cart after successful payment
                    clearCart();
                    localStorage.removeItem('pendingOrderId');

                    // Redirect to purchase history page sau 2 giây
                    setTimeout(() => {
                      navigate(`/purchase-history`);
                    }, 2000);
                  }}
                  className="flex-1 bg-orange-500 hover:bg-orange-600"
                >
                  Kiểm tra trạng thái
                </Button>
              </div>

              {/* Timer */}
              <div
                className={`text-center text-xs p-3 rounded-lg ${
                  timeRemaining < 60
                    ? 'bg-red-50 text-red-700 border border-red-300'
                    : 'bg-gray-50 text-gray-600'
                }`}
              >
                {timeRemaining < 60 && (
                  <>
                    <p className="font-semibold mb-1">⚠️ Sắp hết thời gian!</p>
                  </>
                )}
                Mã QR sẽ hết hạn sau {formatTime(timeRemaining)}. Vui lòng thanh toán trước đó.
              </div>
            </CardContent>
          </Card>
        </div>
      </div>
    </div>
  );
}
