import { useState } from 'react';
import bookingAPI from '@/api/booking';
import { toast } from '@/hooks/use-toast';

export function useBooking() {
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);

  const completeBooking = async (orderId: number) => {
    setLoading(true);
    setError(null);
    try {
      const result = await bookingAPI.completeBooking(orderId) as any;
      if (result?.status) {
        toast({ title: result.message || 'Hoàn tất booking thành công' });
        return result;
      } else {
        const errorMsg = result?.message || 'Lỗi hoàn tất booking';
        setError(errorMsg);
        toast({ title: 'Error', description: errorMsg, variant: 'destructive' });
        return null;
      }
    } catch (err: any) {
      const message = err?.response?.data?.message || 'Lỗi hoàn tất booking';
      setError(message);
      toast({ title: 'Error', description: message, variant: 'destructive' });
      return null;
    } finally {
      setLoading(false);
    }
  };

  const getPurchaseHistory = async () => {
    setLoading(true);
    setError(null);
    try {
      const result = await bookingAPI.getPurchaseHistory() as any;
      if (result?.status || result?.data) {
        return result?.data || result;
      } else {
        const errorMsg = result?.message || 'Lỗi lấy lịch sử mua';
        setError(errorMsg);
        return null;
      }
    } catch (err: any) {
      const message = err?.response?.data?.message || 'Lỗi lấy lịch sử mua';
      setError(message);
      return null;
    } finally {
      setLoading(false);
    }
  };

  const verifyBooking = async (orderId: number) => {
    setLoading(true);
    setError(null);
    try {
      const result = await bookingAPI.verifyBooking(orderId) as any;
      if (result?.status || result?.data) {
        return result?.data || result;
      } else {
        const errorMsg = result?.message || 'Lỗi xác thực booking';
        setError(errorMsg);
        return null;
      }
    } catch (err: any) {
      const message = err?.response?.data?.message || 'Lỗi xác thực booking';
      setError(message);
      return null;
    } finally {
      setLoading(false);
    }
  };

  return {
    loading,
    error,
    completeBooking,
    getPurchaseHistory,
    verifyBooking,
  };
}
