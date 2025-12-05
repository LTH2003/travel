<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\PurchaseHistory;
use App\Services\BookingService;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    protected $bookingService;

    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    /**
     * Complete booking after QR scan (simulate payment)
     */
    public function completeBooking(Request $request, $orderId)
    {
        $user = $request->user();
        $order = Order::findOrFail($orderId);

        // Verify order belongs to user
        if ($order->user_id !== $user->id) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        // If order is still pending, mark it as completed (for manual completion/testing)
        if ($order->status === 'pending') {
            $order->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);
        }

        $result = $this->bookingService->completeBooking($order);

        return response()->json($result, $result['status'] ? 200 : 422);
    }

    /**
     * Get user's purchase history
     */
    public function getPurchaseHistory(Request $request)
    {
        $user = $request->user();
        $history = $this->bookingService->getUserPurchaseHistory($user->id);

        return response()->json([
            'status' => true,
            'data' => $history,
        ]);
    }

    /**
     * Get all user bookings (pending + completed)
     * This includes orders that haven't been completed yet
     */
    public function getAllBookings(Request $request)
    {
        $user = $request->user();
        
        // Get all orders for this user with their booking details
        $orders = Order::where('user_id', $user->id)
            ->with('bookingDetails')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($order) {
                return [
                    'id' => $order->id,
                    'order_code' => $order->order_code,
                    'status' => $order->status,
                    'total_amount' => $order->total_amount,
                    'created_at' => $order->created_at,
                    'completed_at' => $order->completed_at,
                    'items' => $order->bookingDetails->map(function($detail) {
                        $itemName = 'Unknown';
                        if ($detail->booking_info && isset($detail->booking_info['name'])) {
                            $itemName = $detail->booking_info['name'];
                        }
                        return [
                            'id' => $detail->id,
                            'name' => $itemName,
                            'type' => $detail->bookable_type,
                            'quantity' => $detail->quantity,
                            'price' => $detail->price,
                        ];
                    })->toArray(),
                ];
            })->toArray();

        return response()->json([
            'status' => true,
            'data' => $orders,
        ]);
    }

    /**
     * Get specific purchase details
     */
    public function getPurchaseDetail($purchaseId)
    {
        $purchase = PurchaseHistory::findOrFail($purchaseId);

        return response()->json([
            'status' => true,
            'data' => $purchase->load(['order', 'item']),
        ]);
    }

    /**
     * Verify booking via QR scan
     */
    public function verifyBooking($orderId)
    {
        $order = Order::with(['user', 'bookingDetails'])->findOrFail($orderId);

        if ($order->status !== 'completed') {
            return response()->json([
                'status' => false,
                'message' => 'Booking not completed',
            ], 422);
        }

        return response()->json([
            'status' => true,
            'message' => 'Booking verified',
            'data' => [
                'order_code' => $order->order_code,
                'user_name' => $order->user->name,
                'user_email' => $order->user->email,
                'user_phone' => $order->user->phone ?? 'N/A',
                'total_amount' => $order->total_amount,
                'completed_at' => $order->completed_at,
                'items' => $order->bookingDetails->map(function($detail) {
                    $itemName = 'Unknown';
                    if ($detail->booking_info && isset($detail->booking_info['name'])) {
                        $itemName = $detail->booking_info['name'];
                    }
                    return [
                        'name' => $itemName,
                        'type' => $detail->bookable_type,
                        'quantity' => $detail->quantity,
                    ];
                }),
            ],
        ]);
    }
}
