<?php

namespace App\Services;

use App\Models\Order;
use App\Models\BookingDetail;
use App\Models\PurchaseHistory;
use App\Mail\BookingConfirmationMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

class BookingService
{
    /**
     * Complete booking after QR scan (simulate payment success)
     */
    public function completeBooking(Order $order)
    {
        return DB::transaction(function () use ($order) {
            try {
                // Update order - don't update completed_at again since BookingController already set it
                $order->update([
                    'payment_method' => 'qr_scan',
                ]);

                // Get booking details
                $bookingDetails = BookingDetail::where('order_id', $order->id)->get();

                // Create purchase history & generate QR
                $qrData = $this->createPurchaseHistory($order, $bookingDetails);

                // Generate QR code
                $qrCode = $this->generateQrCode($order);
                $order->update(['qr_code' => $qrCode]);

            // Send confirmation email
            $this->sendBookingEmail($order, $bookingDetails, $qrCode);
            
            // Mark email as sent to prevent OrderObserver from sending duplicate
            $order->update(['email_sent_at' => now()]);                return [
                    'status' => true,
                    'message' => 'Booking confirmed successfully',
                    'order' => $order,
                    'qr_code' => $qrCode,
                ];
            } catch (\Exception $e) {
                return [
                    'status' => false,
                    'message' => 'Error completing booking: ' . $e->getMessage(),
                ];
            }
        });
    }

    /**
     * Create purchase history records
     */
    protected function createPurchaseHistory(Order $order, $bookingDetails)
    {
        foreach ($bookingDetails as $detail) {
            $itemName = 'Unknown';
            if ($detail->booking_info && isset($detail->booking_info['name'])) {
                $itemName = $detail->booking_info['name'];
            }
            
            PurchaseHistory::create([
                'user_id' => $order->user_id,
                'order_id' => $order->id,
                'item_type' => $detail->bookable_type,
                'item_id' => $detail->bookable_id,
                'item_name' => $itemName,
                'amount' => $detail->price,
                'status' => 'confirmed',
                'purchased_at' => now(),
            ]);
        }
    }

    /**
     * Generate QR code containing booking info
     */
    protected function generateQrCode(Order $order): string
    {
        $qrData = json_encode([
            'order_code' => $order->order_code,
            'user_email' => $order->user->email,
            'user_name' => $order->user->name,
            'total_amount' => $order->total_amount,
            'completed_at' => $order->completed_at,
            'verification_url' => url('/api/booking/verify/' . $order->id),
        ]);

        $qrCode = QrCode::create($qrData);
        $writer = new PngWriter();
        $result = $writer->write($qrCode);

        // Convert to base64
        return 'data:image/png;base64,' . base64_encode($result->getString());
    }

    /**
     * Send booking confirmation email
     */
    protected function sendBookingEmail(Order $order, $bookingDetails, $qrCode): void
    {
        try {
            $bookingInfo = [];
            foreach ($bookingDetails as $detail) {
                $itemName = 'Unknown';
                if ($detail->booking_info && isset($detail->booking_info['name'])) {
                    $itemName = $detail->booking_info['name'];
                }
                
                $bookingInfo[] = [
                    'name' => $itemName,
                    'type' => $detail->bookable_type,
                    'price' => $detail->price,
                    'quantity' => $detail->quantity,
                ];
            }

            Mail::to($order->user->email)->send(
                new BookingConfirmationMail($order, $bookingInfo, $qrCode)
            );
            
            // Log successful send
            \Log::info('Booking email sent successfully', [
                'order_id' => $order->id,
                'user_email' => $order->user->email,
            ]);
        } catch (\Exception $e) {
            // Log error but don't fail the booking
            \Log::error('Failed to send booking email', [
                'order_id' => $order->id,
                'user_email' => $order->user->email,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Get user's purchase history
     */
    public function getUserPurchaseHistory($userId)
    {
        return PurchaseHistory::where('user_id', $userId)
            ->with('order')
            ->orderBy('purchased_at', 'desc')
            ->get();
    }

    /**
     * Get booking statistics for admin
     */
    public function getBookingStats()
    {
        return [
            'total_bookings' => Order::where('status', 'completed')->count(),
            'total_revenue' => Order::where('status', 'completed')->sum('total_amount'),
            'pending_bookings' => Order::where('status', 'pending')->count(),
            'total_customers' => Order::distinct('user_id')->count(),
        ];
    }
}
