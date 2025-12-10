<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\BookingDetail;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    /**
     * Display all bookings with pagination
     */
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 15);
        $search = $request->query('search', '');
        $status = $request->query('status', '');

        $query = Order::with(['user', 'bookingDetails'])
            ->orderBy('created_at', 'desc');

        // Search by order code or customer name/email
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('order_code', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($userQ) use ($search) {
                      $userQ->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by status
        if ($status && in_array($status, ['pending', 'completed'])) {
            $query->where('status', $status);
        }

        $bookings = $query->paginate($perPage);

        // Get statistics
        $stats = [
            'total_bookings' => Order::count(),
            'completed_bookings' => Order::where('status', 'completed')->count(),
            'pending_bookings' => Order::where('status', 'pending')->count(),
            'total_revenue' => Order::where('status', 'completed')->sum('total_amount'),
        ];

        return view('admin.bookings.index', compact('bookings', 'stats'));
    }

    /**
     * Display booking details
     */
    public function show($id)
    {
        $booking = Order::with(['user', 'bookingDetails'])->findOrFail($id);
        return view('admin.bookings.show', compact('booking'));
    }

    /**
     * Update booking status
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'nullable|in:pending,completed',
            'notes' => 'nullable|string|max:1000',
        ]);

        $booking = Order::findOrFail($id);

        $data = [];
        
        if ($request->filled('status')) {
            $data['status'] = $request->status;
            if ($request->status === 'completed') {
                $data['completed_at'] = now();
            }
        }

        if ($request->filled('notes')) {
            $data['notes'] = $request->notes;
        }

        if (!empty($data)) {
            $booking->update($data);
        }

        return back()->with('success', 'Cập nhật thành công');
    }

    /**
     * Delete a booking and restore room availability
     */
    public function destroy($id)
    {
        return DB::transaction(function () use ($id) {
            $booking = Order::findOrFail($id);
            
            // Get all booking details
            $bookingDetails = BookingDetail::where('order_id', $booking->id)->get();
            
            // Restore room availability for each booked room
            foreach ($bookingDetails as $detail) {
                if ($detail->bookable_type === 'App\\Models\\Room') {
                    $room = Room::find($detail->bookable_id);
                    if ($room) {
                        // Restore the available count
                        $room->increment('available', $detail->quantity);
                    }
                }
            }
            
            // Delete the booking
            $booking->delete();
            
            return back()->with('success', 'Xóa booking thành công và hoàn trả phòng');
        });
    }
}
