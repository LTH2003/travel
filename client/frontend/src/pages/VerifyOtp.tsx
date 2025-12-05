import { useState, useEffect } from 'react';
import { useNavigate, useLocation } from 'react-router-dom';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { toast } from '@/hooks/use-toast';
import { verifyOtp, resendOtp } from '@/api/auth';

export default function VerifyOtpPage() {
  const navigate = useNavigate();
  const location = useLocation();
  
  const [otp, setOtp] = useState('');
  const [loading, setLoading] = useState(false);
  const [timer, setTimer] = useState(600);
  const state = location.state as any;
  const userId = state?.userId;
  const userEmail = state?.userEmail;

  useEffect(() => {
    if (!userId) {
      navigate('/login');
    }
  }, [userId, navigate]);

  useEffect(() => {
    if (timer <= 0) return;
    const interval = setInterval(() => {
      setTimer(t => t - 1);
    }, 1000);
    return () => clearInterval(interval);
  }, [timer]);

  const handleVerifyOtp = async (e: React.FormEvent) => {
    e.preventDefault();
    
    if (otp.length !== 6) {
      toast({
        title: 'Lỗi',
        description: 'Vui lòng nhập đủ 6 chữ số',
        variant: 'destructive',
      });
      return;
    }

    setLoading(true);
    try {
      const response = await verifyOtp({ user_id: userId, code: otp });
      const data = response.data as any;
      
      if (data?.status) {
        localStorage.setItem('token', data.token);
        if (data?.user) {
          localStorage.setItem('user', JSON.stringify(data.user));
        }
        toast({
          title: 'Thành công',
          description: 'Đăng nhập thành công',
        });
        navigate('/');
      }
    } catch (error: any) {
      toast({
        title: 'Lỗi',
        description: error.response?.data?.message || 'Xác thực thất bại',
        variant: 'destructive',
      });
    } finally {
      setLoading(false);
    }
  };

  const handleResendOtp = async () => {
    if (timer > 570) {
      toast({
        title: 'Vui lòng chờ',
        description: 'Vui lòng chờ 30 giây trước khi gửi lại',
        variant: 'destructive',
      });
      return;
    }

    setLoading(true);
    try {
      await resendOtp({ user_id: userId });
      setTimer(600);
      setOtp('');
      toast({
        title: 'Thành công',
        description: 'Mã OTP đã được gửi lại',
      });
    } catch (error: any) {
      toast({
        title: 'Lỗi',
        description: error.response?.data?.message || 'Gửi lại thất bại',
        variant: 'destructive',
      });
    } finally {
      setLoading(false);
    }
  };

  const formatTime = (seconds: number) => {
    const mins = Math.floor(seconds / 60);
    const secs = seconds % 60;
    return `${mins}:${secs.toString().padStart(2, '0')}`;
  };

  return (
    <div className="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 to-indigo-100 py-12 px-4">
      <Card className="w-full max-w-md">
        <CardHeader className="text-center">
          <CardTitle className="text-2xl">Xác thực 2FA</CardTitle>
          <CardDescription>
            Nhập mã OTP được gửi đến {userEmail}
          </CardDescription>
        </CardHeader>
        
        <CardContent>
          <form onSubmit={handleVerifyOtp} className="space-y-6">
            <div className="space-y-2">
              <label className="text-sm font-medium">Mã OTP (6 chữ số)</label>
              <Input
                type="text"
                inputMode="numeric"
                placeholder="000000"
                maxLength={6}
                value={otp}
                onChange={(e) => setOtp(e.target.value.replace(/\D/g, ''))}
                className="text-center text-3xl tracking-widest font-bold"
              />
            </div>

            <div className="text-center text-sm">
              {timer > 0 ? (
                <p>
                  Hết hạn sau:{' '}
                  <span className="font-bold text-red-600">
                    {formatTime(timer)}
                  </span>
                </p>
              ) : (
                <p className="text-red-600 font-bold">Mã đã hết hạn</p>
              )}
            </div>

            <Button
              type="submit"
              disabled={loading || otp.length !== 6}
              className="w-full"
            >
              {loading ? 'Đang xác thực...' : 'Xác thực'}
            </Button>
          </form>

          <div className="mt-4 text-center">
            <Button
              type="button"
              variant="link"
              onClick={handleResendOtp}
              disabled={loading || timer > 570}
              className="text-sm"
            >
              {timer > 570
                ? `Gửi lại sau ${Math.floor((600 - timer) / 60)}:${((600 - timer) % 60).toString().padStart(2, '0')}`
                : 'Gửi lại mã OTP'}
            </Button>
          </div>
        </CardContent>
      </Card>
    </div>
  );
}
