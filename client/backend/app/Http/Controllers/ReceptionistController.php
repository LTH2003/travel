<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\InvoiceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class ReceptionistController extends Controller
{
    /**
     * Show receptionist dashboard
     */
    public function dashboard()
    {
        // Get today's checked-in orders
        $checkedInToday = Order::whereNotNull('checked_in_at')
            ->whereDate('checked_in_at', today())
            ->with('user')
            ->orderBy('checked_in_at', 'desc')
            ->get();

        return view('receptionist.dashboard', [
            'checkedInToday' => $checkedInToday,
            'checkedInCount' => $checkedInToday->count()
        ]);
    }

    /**
     * Check in a customer by QR code or order ID
     */
    public function checkIn(Request $request)
    {
        try {
            $request->validate([
                'qr_code' => 'required|string'
            ]);

            // Find order by QR code or order_code
            $order = Order::where('qr_code', $request->qr_code)
                ->orWhere('order_code', $request->qr_code)
                ->first();

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy đơn hàng với mã QR này'
                ], 404);
            }

            // Check if already checked in
            if ($order->checked_in_at) {
                return response()->json([
                    'success' => false,
                    'message' => 'Khách hàng đã check-in lúc ' . $order->checked_in_at->format('H:i d/m/Y'),
                    'alreadyCheckedIn' => true
                ]);
            }

            // Update checked_in_at timestamp
            $order->checked_in_at = now();
            $order->save();

            // Auto-generate and send invoice
            $invoiceService = new InvoiceService();
            $invoiceResult = $invoiceService->generateAndSendInvoice($order);

            return response()->json([
                'success' => true,
                'message' => 'Check-in thành công cho ' . $order->user->name,
                'invoice' => $invoiceResult,
                'order' => [
                    'id' => $order->id,
                    'order_code' => $order->order_code,
                    'customer_name' => $order->user->name,
                    'checked_in_at' => $order->checked_in_at->format('H:i d/m/Y')
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get today's checked-in list (for table refresh)
     */
    public function getCheckedInList()
    {
        $checkedInToday = Order::whereNotNull('checked_in_at')
            ->whereDate('checked_in_at', today())
            ->with('user')
            ->orderBy('checked_in_at', 'desc')
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'order_code' => $order->order_code,
                    'customer_name' => $order->user->name,
                    'customer_phone' => $order->user->phone ?? 'N/A',
                    'checked_in_at' => $order->checked_in_at->format('H:i'),
                    'checked_in_date' => $order->checked_in_at->format('d/m/Y'),
                    'total_amount' => number_format($order->total_amount, 0, ',', '.') . ' VND'
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $checkedInToday,
            'count' => $checkedInToday->count()
        ]);
    }

    /**
     * Export checked-in list to PDF
     */
    public function exportPDF(Request $request)
    {
        try {
            $date = $request->get('date', today()->format('Y-m-d'));
            
            $checkedInList = Order::whereNotNull('checked_in_at')
                ->whereDate('checked_in_at', $date)
                ->with('user')
                ->orderBy('checked_in_at', 'asc')
                ->get();

            $pdf = Pdf::loadView('receptionist.checkin-pdf', [
                'checkedInList' => $checkedInList,
                'date' => $date,
                'totalCount' => $checkedInList->count(),
                'exportedAt' => now()->format('H:i d/m/Y')
            ]);
            
            // Cấu hình DomPDF để hỗ trợ tiếng Việt
            $pdf->getDomPDF()->getOptions()->set(['enable_utf8' => true, 'isPhpEnabled' => true]);

            return $pdf->download('danh-sach-checkin-' . $date . '.pdf');
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi xuất PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show check-in history page
     */
    public function history(Request $request)
    {
        $date = $request->query('date', today()->toDateString());
        
        $checkedInList = Order::whereNotNull('checked_in_at')
            ->whereDate('checked_in_at', $date)
            ->with('user')
            ->orderBy('checked_in_at', 'desc')
            ->get();

        return view('receptionist.history', [
            'checkedInList' => $checkedInList,
            'selectedDate' => $date,
            'totalCount' => $checkedInList->count()
        ]);
    }

    /**
     * Export check-in history as PDF for selected date
     */
    public function exportHistoryPDF(Request $request)
    {
        try {
            $request->validate([
                'date' => 'required|date_format:Y-m-d'
            ]);

            $date = $request->query('date', today()->toDateString());
            $checkedInList = Order::whereNotNull('checked_in_at')
                ->whereDate('checked_in_at', $date)
                ->with('user')
                ->orderBy('checked_in_at', 'asc')
                ->get();

            $pdf = Pdf::loadView('receptionist.checkin-pdf', [
                'checkedInList' => $checkedInList,
                'date' => $date,
                'totalCount' => $checkedInList->count(),
                'exportedAt' => now()->format('H:i d/m/Y')
            ]);
            
            // Cấu hình DomPDF để hỗ trợ tiếng Việt
            $pdf->getDomPDF()->getOptions()->set(['enable_utf8' => true, 'isPhpEnabled' => true]);

            return $pdf->download('danh-sach-checkin-' . $date . '.pdf');
        } catch (\Exception $e) {
            return back()->withErrors('Lỗi xuất PDF: ' . $e->getMessage());
        }
    }

    /**
     * Get today's checked-in orders for invoice export selection
     */
    public function getCheckedInForInvoice()
    {
        try {
            $checkedInToday = Order::whereNotNull('checked_in_at')
                ->whereDate('checked_in_at', today())
                ->with('user')
                ->orderBy('checked_in_at', 'desc')
                ->get()
                ->map(function ($order) {
                    return [
                        'id' => $order->id,
                        'order_code' => $order->order_code,
                        'customer_name' => $order->user->name,
                        'customer_email' => $order->user->email,
                        'checked_in_at' => $order->checked_in_at->format('H:i d/m/Y'),
                        'total_amount' => number_format($order->total_amount, 0, ',', '.') . ' VND'
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $checkedInToday,
                'count' => $checkedInToday->count()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export invoice for a specific order
     */
    public function exportInvoice(Request $request, $orderId)
    {
        try {
            // Validate orderId from route parameter
            if (!$orderId || !is_numeric($orderId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mã đơn hàng không hợp lệ'
                ], 422);
            }

            $order = Order::with('user', 'bookingDetails')->find($orderId);

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy đơn hàng'
                ], 404);
            }

            // Check if order was checked in today
            if (!$order->checked_in_at || $order->checked_in_at->toDateString() !== today()->toDateString()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Chỉ có thể xuất hóa đơn cho các đơn check-in hôm nay'
                ], 422);
            }

            // Use InvoiceService to generate and send invoice
            $invoiceService = new InvoiceService();
            $result = $invoiceService->generateAndSendInvoice($order);

            return response()->json([
                'success' => $result['success'],
                'pdfGenerated' => $result['pdfGenerated'],
                'emailSent' => $result['emailSent'],
                'message' => $result['message']
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }
}

