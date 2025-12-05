<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\PurchaseHistory;
use Illuminate\Http\Request;
use App\Services\BookingService;

class BookingManagementController extends Controller
{
    protected $bookingService;

    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    /**
     * Get all bookings (admin)
     */
    public function getAllBookings(Request $request)
    {
        $query = Order::with(['user', 'bookingDetails.bookable'])
            ->orderBy('completed_at', 'desc');

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->has('date_from') && $request->has('date_to')) {
            $query->whereBetween('completed_at', [
                $request->date_from,
                $request->date_to,
            ]);
        }

        // Search by order code or user email
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_code', 'like', "%$search%")
                  ->orWhereHas('user', fn($u) => $u->where('email', 'like', "%$search%"));
            });
        }

        $bookings = $query->paginate(20);

        return response()->json([
            'status' => true,
            'data' => $bookings,
        ]);
    }

    /**
     * Get bookings by specific tour or hotel
     */
    public function getBookingsByItem(Request $request, $itemType, $itemId)
    {
        $purchases = PurchaseHistory::where('item_type', $itemType)
            ->where('item_id', $itemId)
            ->with(['user', 'order'])
            ->orderBy('purchased_at', 'desc')
            ->paginate(15);

        return response()->json([
            'status' => true,
            'item_type' => $itemType,
            'item_id' => $itemId,
            'total_bookings' => $purchases->total(),
            'data' => $purchases,
        ]);
    }

    /**
     * Get booking statistics
     */
    public function getStats()
    {
        $stats = $this->bookingService->getBookingStats();

        // Additional stats
        $stats['bookings_by_type'] = PurchaseHistory::selectRaw('item_type, COUNT(*) as count')
            ->groupBy('item_type')
            ->get();

        $stats['top_items'] = PurchaseHistory::selectRaw('item_type, item_id, item_name, COUNT(*) as bookings, SUM(amount) as revenue')
            ->groupBy('item_type', 'item_id', 'item_name')
            ->orderBy('bookings', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'status' => true,
            'data' => $stats,
        ]);
    }

    /**
     * Export bookings to CSV
     */
    public function exportBookings(Request $request)
    {
        $query = Order::with(['user', 'bookingDetails.bookable'])
            ->where('status', 'completed');

        if ($request->has('date_from') && $request->has('date_to')) {
            $query->whereBetween('completed_at', [
                $request->date_from,
                $request->date_to,
            ]);
        }

        $bookings = $query->get();

        $csv = "Order Code,User Name,Email,Phone,Total Amount,Items,Booking Date\n";
        foreach ($bookings as $booking) {
            $items = $booking->bookingDetails->map(fn($d) => $d->bookable->name)->implode(', ');
            $csv .= "\"{$booking->order_code}\",\"{$booking->user->name}\",\"{$booking->user->email}\",\"{$booking->user->phone}\",{$booking->total_amount},\"{$items}\",\"{$booking->completed_at}\"\n";
        }

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="bookings-' . date('Y-m-d') . '.csv"',
        ]);
    }
}
