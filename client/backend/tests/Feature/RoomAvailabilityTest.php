<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Room;
use App\Models\Hotel;
use App\Models\User;
use App\Models\BookingDetail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoomAvailabilityTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that room availability decreases when order is completed
     */
    public function test_room_availability_decreases_on_order_completion(): void
    {
        // Create test data
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'role' => 'user',
        ]);
        $hotel = Hotel::create([
            'name' => 'Test Hotel',
            'location' => 'Test Location',
            'price' => 100,
            'original_price' => 150,
            'rating' => 4.5,
            'reviews' => 10,
        ]);
        $room = Room::create([
            'hotel_id' => $hotel->id,
            'name' => 'Test Room',
            'size' => 20,
            'capacity' => 2,
            'beds' => 1,
            'price' => 100,
            'original_price' => 150,
            'available' => 5,
        ]);

        // Create an order
        $order = Order::create([
            'user_id' => $user->id,
            'order_code' => 'TEST-001',
            'total_amount' => 200,
            'status' => 'pending',
            'items' => json_encode([]),
        ]);

        // Create a booking detail for 2 rooms
        BookingDetail::create([
            'order_id' => $order->id,
            'bookable_type' => 'App\Models\Room',
            'bookable_id' => $room->id,
            'quantity' => 2,
            'price' => 100,
        ]);

        // Verify initial room count
        $this->assertEquals(5, $room->fresh()->available);

        // Update order status to completed
        $order->update(['status' => 'completed']);

        // Verify room count decreased by 2
        $this->assertEquals(3, $room->fresh()->available);
    }

    /**
     * Test that room availability is restored when order is cancelled
     */
    public function test_room_availability_restored_on_order_cancellation(): void
    {
        // Create test data
        $user = User::create([
            'name' => 'Test User 2',
            'email' => 'test2@example.com',
            'password' => bcrypt('password'),
            'role' => 'user',
        ]);
        $hotel = Hotel::create([
            'name' => 'Test Hotel 2',
            'location' => 'Test Location 2',
            'price' => 100,
            'original_price' => 150,
            'rating' => 4.5,
            'reviews' => 10,
        ]);
        $room = Room::create([
            'hotel_id' => $hotel->id,
            'name' => 'Test Room 2',
            'size' => 20,
            'capacity' => 2,
            'beds' => 1,
            'price' => 100,
            'original_price' => 150,
            'available' => 3,
        ]);

        // Create a completed order
        $order = Order::create([
            'user_id' => $user->id,
            'order_code' => 'TEST-002',
            'total_amount' => 200,
            'status' => 'completed',
            'items' => json_encode([]),
        ]);

        // Create a booking detail for 2 rooms
        BookingDetail::create([
            'order_id' => $order->id,
            'bookable_type' => 'App\Models\Room',
            'bookable_id' => $room->id,
            'quantity' => 2,
            'price' => 100,
        ]);

        // Verify room count after order completion (should be 3 as it was already completed)
        // Note: Observer only reduces when status changes to completed
        // So we need to reset to 3 first
        $room->update(['available' => 3]);

        // Cancel the order
        $order->update(['status' => 'cancelled']);

        // Verify room count increased by 2
        $this->assertEquals(5, $room->fresh()->available);
    }

    /**
     * Test that multiple rooms in one order are handled correctly
     */
    public function test_multiple_rooms_in_single_order(): void
    {
        // Create test data
        $user = User::create([
            'name' => 'Test User 3',
            'email' => 'test3@example.com',
            'password' => bcrypt('password'),
            'role' => 'user',
        ]);
        $hotel = Hotel::create([
            'name' => 'Test Hotel 3',
            'location' => 'Test Location 3',
            'price' => 100,
            'original_price' => 150,
            'rating' => 4.5,
            'reviews' => 10,
        ]);
        $room1 = Room::create([
            'hotel_id' => $hotel->id,
            'name' => 'Test Room 1',
            'size' => 20,
            'capacity' => 2,
            'beds' => 1,
            'price' => 100,
            'original_price' => 150,
            'available' => 10,
        ]);
        $room2 = Room::create([
            'hotel_id' => $hotel->id,
            'name' => 'Test Room 2',
            'size' => 30,
            'capacity' => 3,
            'beds' => 2,
            'price' => 150,
            'original_price' => 200,
            'available' => 8,
        ]);

        // Create an order
        $order = Order::create([
            'user_id' => $user->id,
            'order_code' => 'TEST-003',
            'total_amount' => 500,
            'status' => 'pending',
            'items' => json_encode([]),
        ]);

        // Create booking details for both rooms
        BookingDetail::create([
            'order_id' => $order->id,
            'bookable_type' => 'App\Models\Room',
            'bookable_id' => $room1->id,
            'quantity' => 3,
            'price' => 100,
        ]);

        BookingDetail::create([
            'order_id' => $order->id,
            'bookable_type' => 'App\Models\Room',
            'bookable_id' => $room2->id,
            'quantity' => 2,
            'price' => 100,
        ]);

        // Update order status to completed
        $order->update(['status' => 'completed']);

        // Verify both rooms decreased
        $this->assertEquals(7, $room1->fresh()->available); // 10 - 3
        $this->assertEquals(6, $room2->fresh()->available); // 8 - 2
    }

    /**
     * Test that room availability doesn't go negative
     */
    public function test_room_availability_never_goes_negative(): void
    {
        // Create test data
        $user = User::create([
            'name' => 'Test User 4',
            'email' => 'test4@example.com',
            'password' => bcrypt('password'),
            'role' => 'user',
        ]);
        $hotel = Hotel::create([
            'name' => 'Test Hotel 4',
            'location' => 'Test Location 4',
            'price' => 100,
            'original_price' => 150,
            'rating' => 4.5,
            'reviews' => 10,
        ]);
        $room = Room::create([
            'hotel_id' => $hotel->id,
            'name' => 'Test Room 4',
            'size' => 20,
            'capacity' => 2,
            'beds' => 1,
            'price' => 100,
            'original_price' => 150,
            'available' => 2,
        ]);

        // Create an order
        $order = Order::create([
            'user_id' => $user->id,
            'order_code' => 'TEST-004',
            'total_amount' => 500,
            'status' => 'pending',
            'items' => json_encode([]),
        ]);

        // Create a booking detail for 5 rooms (more than available)
        BookingDetail::create([
            'order_id' => $order->id,
            'bookable_type' => 'App\Models\Room',
            'bookable_id' => $room->id,
            'quantity' => 5,
            'price' => 100,
        ]);

        // Update order status to completed
        $order->update(['status' => 'completed']);

        // Verify room count is 0 (not negative)
        $this->assertEquals(0, $room->fresh()->available);
    }
}
