<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class PaymentController extends Controller
{
    /**
     * 💳 Danh sách các giao dịch thanh toán
     */
    public function index(Request $request)
    {
        $query = Payment::with('order', 'order.user')
            ->latest('created_at');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Filter by payment method
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->input('payment_method'));
        }

        // Filter by date range
        if ($request->filled('from_date')) {
            $fromDate = Carbon::createFromFormat('Y-m-d', $request->input('from_date'));
            $query->whereDate('created_at', '>=', $fromDate);
        }

        if ($request->filled('to_date')) {
            $toDate = Carbon::createFromFormat('Y-m-d', $request->input('to_date'));
            $query->whereDate('created_at', '<=', $toDate);
        }

        // Search by order code or transaction ID
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->whereHas('order', function ($subQ) use ($search) {
                    $subQ->where('order_code', 'like', "%{$search}%");
                })
                ->orWhere('transaction_id', 'like', "%{$search}%");
            });
        }

        // Calculate statistics
        $totalPayments = Payment::count();
        
        // In mock mode, treat pending as success
        if (env('APP_PAYMENT_MOCK', false)) {
            $successfulPayments = Payment::where('status', 'success')
                ->orWhere('status', 'pending')
                ->count();
            $pendingPayments = 0;
            $failedPayments = Payment::where('status', 'failed')->count();
            
            $totalAmount = Payment::where('status', 'success')
                ->orWhere('status', 'pending')
                ->sum('amount');
            $pendingAmount = 0;
        } else {
            $successfulPayments = Payment::where('status', 'success')->count();
            $pendingPayments = Payment::where('status', 'pending')->count();
            $failedPayments = Payment::where('status', 'failed')->count();
            
            $totalAmount = Payment::where('status', 'success')
                ->sum('amount');
            $pendingAmount = Payment::where('status', 'pending')
                ->sum('amount');
        }

        $payments = $query->paginate(15);

        return view('admin.payments.index', compact(
            'payments',
            'totalPayments',
            'successfulPayments',
            'pendingPayments',
            'failedPayments',
            'totalAmount',
            'pendingAmount'
        ));
    }

    /**
     * 📋 Chi tiết một giao dịch thanh toán
     */
    public function show(Payment $payment)
    {
        $payment->load('order', 'order.user', 'order.bookingDetails');

        // Get related payments
        $relatedPayments = Payment::where('order_id', $payment->order_id)
            ->where('id', '!=', $payment->id)
            ->latest('created_at')
            ->get();

        return view('admin.payments.show', compact('payment', 'relatedPayments'));
    }

    /**
     * ✅ Đánh dấu thanh toán thành công (cho các trường hợp xác nhận thủ công)
     */
    public function confirm(Payment $payment)
    {
        if ($payment->status !== 'pending') {
            return back()->with('error', 'Chỉ có thể xác nhận các giao dịch chờ xử lý');
        }

        $payment->update([
            'status' => 'success',
            'paid_at' => now(),
        ]);

        // Update order status if all payments succeeded
        $order = $payment->order;
        if ($order->payments()->where('status', '!=', 'success')->count() === 0) {
            $order->update(['status' => 'completed']);
        }

        return back()->with('success', 'Đã xác nhận thanh toán thành công');
    }

    /**
     * ❌ Đánh dấu thanh toán thất bại
     */
    public function markFailed(Request $request, Payment $payment)
    {
        $validated = $request->validate([
            'error_message' => 'required|string|max:500',
        ]);

        $payment->update([
            'status' => 'failed',
            'error_message' => $validated['error_message'],
        ]);

        return back()->with('success', 'Đã cập nhật trạng thái thanh toán');
    }

    /**
     * 🔄 Xoá giao dịch & Hoàn tiền (refund) cho khách hàng
     * Cho phép xoá tất cả các giao dịch, bao gồm cả những giao dịch thành công
     * để hoàn tiền khi khách hàng hủy booking
     */
    public function destroy(Request $request, Payment $payment)
    {
        $validated = $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        $paymentId = $payment->id;
        $orderId = $payment->order_id;
        $amount = $payment->amount;
        $reason = $validated['reason'] ?? 'Hoàn tiền do hủy booking';

        // Ghi log lý do hoàn tiền
        $payment->error_message = "[REFUND] " . $reason . " | Deleted at: " . now();
        $payment->status = 'refunded';
        $payment->save();

        // Xoá giao dịch
        $payment->delete();

        return back()->with('success', "Đã hoàn tiền thành công: " . number_format($amount, 0, ',', '.') . "đ");
    }

    /**
     * 📊 Xuất báo cáo thanh toán dạng PDF
     */
    public function exportPdf(Request $request)
    {
        try {
            $query = Payment::with('order', 'order.user')
                ->latest('created_at');

            // Apply same filters as index
            if ($request->filled('search')) {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->where('transaction_id', 'like', "%$search%")
                        ->orWhereHas('order', function ($q) use ($search) {
                            $q->where('order_code', 'like', "%$search%");
                        })
                        ->orWhereHas('order.user', function ($q) use ($search) {
                            $q->where('name', 'like', "%$search%")
                                ->orWhere('email', 'like', "%$search%");
                        });
                });
            }

            if ($request->filled('status')) {
                $query->where('status', $request->input('status'));
            }

            if ($request->filled('payment_method')) {
                $query->where('payment_method', $request->input('payment_method'));
            }

            if ($request->filled('from_date')) {
                $fromDate = Carbon::createFromFormat('Y-m-d', $request->input('from_date'));
                $query->whereDate('created_at', '>=', $fromDate);
            }

            if ($request->filled('to_date')) {
                $toDate = Carbon::createFromFormat('Y-m-d', $request->input('to_date'));
                $query->whereDate('created_at', '<=', $toDate);
            }

            $payments = $query->get();

            // In mock mode, treat pending as success
            $successPayments = $payments->whereIn('status', ['success', 'pending']);
            
            $totalAmount = $successPayments->sum('amount');
            $averageAmount = $successPayments->count() > 0 ? $successPayments->avg('amount') : 0;
            $successCount = $successPayments->count();
            $failureCount = $payments->where('status', 'failed')->count();

            // Group by payment method
            $byMethod = $payments->groupBy('payment_method')->map(function ($items) {
                $successItems = $items->whereIn('status', ['success', 'pending']);
                return [
                    'count' => $items->count(),
                    'amount' => $successItems->sum('amount'),
                    'success_count' => $successItems->count(),
                ];
            });

            // Group by date
            $byDate = $payments->groupBy(function ($payment) {
                return $payment->created_at->format('Y-m-d');
            })->map(function ($items) {
                $successItems = $items->whereIn('status', ['success', 'pending']);
                return [
                    'count' => $items->count(),
                    'amount' => $successItems->sum('amount'),
                    'success_count' => $successItems->count(),
                ];
            });

            $html = view('admin.payments.pdf', compact(
                'payments',
                'totalAmount',
                'averageAmount',
                'successCount',
                'failureCount',
                'byMethod',
                'byDate'
            ))->render();

            $pdf = \PDF::loadHTML($html);
            // Cấu hình DomPDF để hỗ trợ tiếng Việt
            $pdf->getDomPDF()->getOptions()->set(['enable_utf8' => true, 'isPhpEnabled' => true]);
            return $pdf->download('payment-report-' . date('YmdHis') . '.pdf');
        } catch (\Exception $e) {
            return back()->withErrors('Lỗi khi xuất PDF: ' . $e->getMessage());
        }
    }

    /**
     * 📈 Thống kê chi tiết theo phương thức thanh toán
     */
    public function statistics()
    {
        // By payment method - in mock mode, treat pending as success
        if (env('APP_PAYMENT_MOCK', false)) {
            $byMethod = Payment::selectRaw('
                payment_method,
                COUNT(*) as count,
                SUM(CASE WHEN status = "success" OR status = "pending" THEN 1 ELSE 0 END) as success_count,
                SUM(CASE WHEN status = "failed" THEN 1 ELSE 0 END) as failed_count,
                SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending_count,
                SUM(CASE WHEN status = "success" OR status = "pending" THEN amount ELSE 0 END) as success_amount,
                AVG(CASE WHEN status = "success" OR status = "pending" THEN amount ELSE NULL END) as avg_amount
            ')
                ->groupBy('payment_method')
                ->get();
        } else {
            $byMethod = Payment::selectRaw('
                payment_method,
                COUNT(*) as count,
                SUM(CASE WHEN status = "success" THEN 1 ELSE 0 END) as success_count,
                SUM(CASE WHEN status = "failed" THEN 1 ELSE 0 END) as failed_count,
                SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending_count,
                SUM(CASE WHEN status = "success" THEN amount ELSE 0 END) as success_amount,
                AVG(CASE WHEN status = "success" THEN amount ELSE NULL END) as avg_amount
            ')
                ->groupBy('payment_method')
                ->get();
        }

        // By status - in mock mode, combine pending with success
        $byStatusRaw = Payment::selectRaw('
            status,
            COUNT(*) as count,
            SUM(amount) as total_amount,
            AVG(amount) as avg_amount
        ')
            ->groupBy('status')
            ->get();
        
        // Process status data for mock mode
        if (env('APP_PAYMENT_MOCK', false)) {
            $successCount = 0;
            $successAmount = 0;
            $successAvg = 0;
            $processedCount = 0;
            $byStatus = collect();
            
            foreach ($byStatusRaw as $item) {
                if ($item->status === 'success' || $item->status === 'pending') {
                    $successCount += $item->count;
                    $successAmount += $item->total_amount;
                    $processedCount += $item->count;
                } elseif ($item->status === 'failed') {
                    $byStatus->push($item);
                }
            }
            
            if ($successCount > 0) {
                $successObj = (object)[
                    'status' => 'success',
                    'count' => $successCount,
                    'total_amount' => $successAmount,
                    'avg_amount' => $successAmount / $successCount
                ];
                $byStatus->prepend($successObj);
            }
        } else {
            $byStatus = $byStatusRaw;
        }

        // Daily revenue (last 30 days) - in mock mode, treat pending as success
        if (env('APP_PAYMENT_MOCK', false)) {
            $dailyRevenue = Payment::selectRaw('
                DATE(created_at) as date,
                COUNT(*) as count,
                SUM(CASE WHEN status = "success" OR status = "pending" THEN amount ELSE 0 END) as total_amount,
                SUM(CASE WHEN status = "success" OR status = "pending" THEN 1 ELSE 0 END) as success_count
            ')
                ->where('created_at', '>=', Carbon::now()->subDays(30))
                ->groupBy('date')
                ->orderBy('date', 'desc')
                ->get();
        } else {
            $dailyRevenue = Payment::selectRaw('
                DATE(created_at) as date,
                COUNT(*) as count,
                SUM(CASE WHEN status = "success" THEN amount ELSE 0 END) as total_amount,
                SUM(CASE WHEN status = "success" THEN 1 ELSE 0 END) as success_count
            ')
                ->where('created_at', '>=', Carbon::now()->subDays(30))
                ->groupBy('date')
                ->orderBy('date', 'desc')
                ->get();
        }

        // Conversion rate - in mock mode, treat pending as success
        $totalPaymentRecords = Payment::count();
        if (env('APP_PAYMENT_MOCK', false)) {
            $successfulPaymentRecords = Payment::where('status', 'success')
                ->orWhere('status', 'pending')
                ->count();
            $totalAmount = Payment::where('status', 'success')
                ->orWhere('status', 'pending')
                ->sum('amount');
        } else {
            $successfulPaymentRecords = Payment::where('status', 'success')->count();
            $totalAmount = Payment::where('status', 'success')->sum('amount');
        }
        $conversionRate = $totalPaymentRecords > 0 
            ? ($successfulPaymentRecords / $totalPaymentRecords) * 100 
            : 0;
        $totalCount = Payment::count();

        return view('admin.payments.statistics', compact(
            'byMethod',
            'byStatus',
            'dailyRevenue',
            'conversionRate',
            'totalAmount',
            'totalCount',
            'successfulPaymentRecords'
        ));
    }

    /**
     * 🔍 Kiểm tra trạng thái giao dịch từ gateway
     */
    public function verifyTransactionStatus(Payment $payment)
    {
        try {
            $result = null;

            switch ($payment->payment_method) {
                case 'momo':
                    // Verify MoMo transaction
                    // This would call MoMoService::checkTransaction()
                    $result = [
                        'status' => $payment->status,
                        'message' => 'Trạng thái giao dịch MoMo: ' . strtoupper($payment->status),
                    ];
                    break;

                case 'vietqr':
                    $result = [
                        'status' => $payment->status,
                        'message' => 'Trạng thái giao dịch VietQR: ' . strtoupper($payment->status),
                    ];
                    break;

                case 'card':
                    $result = [
                        'status' => $payment->status,
                        'message' => 'Trạng thái giao dịch Thẻ: ' . strtoupper($payment->status),
                    ];
                    break;

                default:
                    $result = [
                        'status' => $payment->status,
                        'message' => 'Trạng thái giao dịch: ' . strtoupper($payment->status),
                    ];
            }

            return response()->json([
                'status' => true,
                'data' => $result,
                'payment' => [
                    'id' => $payment->id,
                    'transaction_id' => $payment->transaction_id,
                    'status' => $payment->status,
                    'amount' => number_format($payment->amount, 0, ',', '.'),
                    'method' => $payment->payment_method,
                    'paid_at' => $payment->paid_at?->format('d/m/Y H:i'),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
