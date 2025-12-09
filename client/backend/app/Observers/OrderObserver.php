<?php

namespace App\Observers;

use App\Models\Order;
use App\Models\BookingDetail;
use App\Models\Room;
use App\Mail\BookingConfirmationMail;
use Illuminate\Support\Facades\Mail;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

class OrderObserver
{
    /**
     * Handle the Order "updated" event.
     * Send email when order status changes to completed
     * Update room availability when order is completed or cancelled
     */
    public function updated(Order $order): void
    {
        \Log::info('OrderObserver.updated() called', ['order_id' => $order->id, 'status' => $order->status]);
        
        if ($order->isDirty('status')) {
            $previousStatus = $order->getOriginal('status');
            $currentStatus = $order->status;

            \Log::info('Status changed', [
                'order_id' => $order->id,
                'from' => $previousStatus,
                'to' => $currentStatus,
            ]);

            // Giảm số phòng khi order được confirmed/completed
            if ($previousStatus !== 'completed' && $currentStatus === 'completed') {
                \Log::info('Calling decreaseAvailableRooms', ['order_id' => $order->id]);
                $this->decreaseAvailableRooms($order);
                
                // Send email khi đơn hàng hoàn tất
                if (!$order->email_sent_at) {
                    $this->sendBookingEmail($order);
                }
            }

            // Cộng lại số phòng khi order bị hủy
            if ($currentStatus === 'cancelled') {
                \Log::info('Calling increaseAvailableRooms', ['order_id' => $order->id]);
                $this->increaseAvailableRooms($order);
            }
        } else {
            \Log::info('Status not dirty, skipping room update', ['order_id' => $order->id]);
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

    /**
     * Giảm số phòng có sẵn khi đơn hàng hoàn tất thanh toán
     */
    private function decreaseAvailableRooms(Order $order): void
    {
        \Log::info('🔵 decreaseAvailableRooms START', ['order_id' => $order->id]);
        
        try {
            $bookingDetails = $order->bookingDetails;
            \Log::info('📦 Found booking details', [
                'order_id' => $order->id,
                'count' => $bookingDetails->count(),
            ]);

            foreach ($bookingDetails as $detail) {
                \Log::info('📋 Processing detail', [
                    'detail_id' => $detail->id,
                    'bookable_type' => $detail->bookable_type,
                    'bookable_id' => $detail->bookable_id,
                ]);

                // Nếu loại bookable là Room
                if ($detail->bookable_type === 'App\Models\Room' || $detail->bookable_type === 'Room') {
                    \Log::info('🏠 Handling Room type', ['detail_id' => $detail->id]);
                    $room = Room::find($detail->bookable_id);
                    
                    if ($room) {
                        // Trừ đi số phòng đã đặt
                        $newAvailable = max(0, $room->available - $detail->quantity);
                        $room->update(['available' => $newAvailable]);
                        
                        \Log::info('✅ Room availability decreased', [
                            'room_id' => $room->id,
                            'order_id' => $order->id,
                            'quantity' => $detail->quantity,
                            'available_before' => $room->getOriginal('available'),
                            'available_after' => $newAvailable,
                        ]);
                    } else {
                        \Log::warning('⚠️ Room not found', [
                            'room_id' => $detail->bookable_id,
                            'detail_id' => $detail->id,
                        ]);
                    }
                }
                // Nếu loại bookable là Hotel → tìm room với tên tương ứng
                elseif ($detail->bookable_type === 'App\Models\Hotel' || $detail->bookable_type === 'Hotel') {
                    \Log::info('🏨 Handling Hotel type', ['detail_id' => $detail->id]);
                    
                    $hotel = \App\Models\Hotel::find($detail->bookable_id);
                    \Log::info('🔍 Hotel lookup', [
                        'hotel_id' => $detail->bookable_id,
                        'hotel_found' => $hotel ? true : false,
                    ]);

                    if ($hotel && $detail->booking_info) {
                        \Log::info('📊 Booking info', [
                            'booking_info' => $detail->booking_info,
                        ]);

                        // Lấy tên phòng từ booking_info
                        $roomName = $detail->booking_info['name'] ?? null;
                        $quantity = $detail->booking_info['quantity'] ?? $detail->quantity;
                        
                        \Log::info('🔎 Room search params', [
                            'hotel_id' => $hotel->id,
                            'room_name' => $roomName,
                            'quantity' => $quantity,
                        ]);

                        if ($roomName) {
                            // Tìm phòng của hotel này theo tên
                            $room = Room::where('hotel_id', $hotel->id)
                                ->where('name', $roomName)
                                ->first();
                            
                            \Log::info('🔍 Room search result', [
                                'room_found' => $room ? true : false,
                                'room_id' => $room?->id,
                                'room_name' => $room?->name,
                                'available_before' => $room?->available,
                            ]);

                            if ($room) {
                                // Trừ đi số phòng đã đặt
                                $newAvailable = max(0, $room->available - $quantity);
                                $room->update(['available' => $newAvailable]);
                                $room->refresh();
                                
                                \Log::info('✅ Hotel room availability DECREASED', [
                                    'hotel_id' => $hotel->id,
                                    'room_id' => $room->id,
                                    'room_name' => $roomName,
                                    'order_id' => $order->id,
                                    'quantity' => $quantity,
                                    'available_before' => $room->getOriginal('available'),
                                    'available_after' => $room->available,
                                ]);
                            } else {
                                \Log::warning('⚠️ Room not found for hotel booking', [
                                    'hotel_id' => $hotel->id,
                                    'room_name' => $roomName,
                                    'order_id' => $order->id,
                                ]);
                            }
                        } else {
                            \Log::warning('⚠️ No room name in booking_info', ['detail_id' => $detail->id]);
                        }
                    } else {
                        \Log::warning('⚠️ Hotel not found or no booking_info', [
                            'hotel_found' => $hotel ? true : false,
                            'has_booking_info' => $detail->booking_info ? true : false,
                        ]);
                    }
                }
            }
        } catch (\Exception $e) {
            \Log::error('❌ Failed to decrease room availability', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
        
        \Log::info('🔵 decreaseAvailableRooms END', ['order_id' => $order->id]);
    }

    /**
     * Cộng lại số phòng khi hủy đơn hàng
     */
    private function increaseAvailableRooms(Order $order): void
    {
        try {
            $bookingDetails = $order->bookingDetails;

            foreach ($bookingDetails as $detail) {
                // Nếu loại bookable là Room
                if ($detail->bookable_type === 'App\Models\Room' || $detail->bookable_type === 'Room') {
                    $room = Room::find($detail->bookable_id);
                    
                    if ($room) {
                        // Cộng lại số phòng
                        $newAvailable = $room->available + $detail->quantity;
                        $room->update(['available' => $newAvailable]);
                        
                        \Log::info('Room availability restored', [
                            'room_id' => $room->id,
                            'order_id' => $order->id,
                            'quantity' => $detail->quantity,
                            'available_after' => $newAvailable,
                        ]);
                    }
                }
                // Nếu loại bookable là Hotel → tìm room với tên tương ứng
                elseif ($detail->bookable_type === 'App\Models\Hotel' || $detail->bookable_type === 'Hotel') {
                    $hotel = \App\Models\Hotel::find($detail->bookable_id);
                    if ($hotel && $detail->booking_info) {
                        // Lấy tên phòng từ booking_info
                        $roomName = $detail->booking_info['name'] ?? null;
                        $quantity = $detail->booking_info['quantity'] ?? $detail->quantity;
                        
                        if ($roomName) {
                            // Tìm phòng của hotel này theo tên
                            $room = Room::where('hotel_id', $hotel->id)
                                ->where('name', $roomName)
                                ->first();
                            
                            if ($room) {
                                // Cộng lại số phòng
                                $newAvailable = $room->available + $quantity;
                                $room->update(['available' => $newAvailable]);
                                
                                \Log::info('Hotel room availability restored', [
                                    'hotel_id' => $hotel->id,
                                    'room_id' => $room->id,
                                    'room_name' => $roomName,
                                    'order_id' => $order->id,
                                    'quantity' => $quantity,
                                    'available_after' => $newAvailable,
                                ]);
                            }
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            \Log::error('Failed to restore room availability', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}
