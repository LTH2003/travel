<?php

namespace App\Observers;

use App\Models\Order;
use App\Models\BookingDetail;
use App\Mail\BookingConfirmationMail;
use Illuminate\Support\Facades\Mail;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

class OrderObserver
{
    /**
     * Handle the Order "updated" event.
     * Send email when order status changes to completed
     */
    public function updated(Order $order): void
    {
        // Only send email when status transitions to completed and hasn't been sent yet
        if ($order->isDirty('status') && $order->status === 'completed' && !$order->email_sent_at) {
            $this->sendBookingEmail($order);
        }
    }

    /**
     * Send booking confirmation email
     */
    private function sendBookingEmail(Order $order): void
    {
        try {
            // Get booking details
            $bookingDetails = BookingDetail::where('order_id', $order->id)->get();
            
            if ($bookingDetails->isEmpty()) {
                \Log::warning('No booking details found for order', ['order_id' => $order->id]);
                return;
            }

            // Generate QR code
            $qrCode = $this->generateQrCode($order);

            // Prepare booking information
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

            // Send email
            Mail::to($order->user->email)->send(
                new BookingConfirmationMail($order, $bookingInfo, $qrCode)
            );

            // Update email_sent_at timestamp
            $order->update(['email_sent_at' => now()]);

            // Log success
            \Log::info('Booking confirmation email sent', [
                'order_id' => $order->id,
                'user_email' => $order->user->email,
            ]);
        } catch (\Exception $e) {
            // Log error but don't fail the order
            \Log::error('Failed to send booking confirmation email', [
                'order_id' => $order->id,
                'user_email' => $order->user->email ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * Generate QR code containing booking info
     */
    private function generateQrCode(Order $order): string
    {
        try {
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
        } catch (\Exception $e) {
            \Log::error('Failed to generate QR code', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
            return '';
        }
    }
}
