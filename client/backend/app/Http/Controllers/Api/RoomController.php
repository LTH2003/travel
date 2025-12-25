<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\Hotel;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function show($id)
    {
        return response()->json(Room::findOrFail($id));
    }

    
    public function indexByHotel($hotelId)
    {
        $hotel = Hotel::findOrFail($hotelId);
        return response()->json($hotel->rooms);
    }

    
    public function store(Request $request, $hotelId)
    {
        $hotel = Hotel::findOrFail($hotelId);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'size' => 'nullable|numeric|min:0',
            'capacity' => 'nullable|integer|min:1',
            'beds' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'original_price' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'amenities' => 'nullable|array',
            'images' => 'nullable|array',
            'image' => 'nullable|string',
            'available' => 'nullable|integer|min:0',
        ]);

        $validated['hotel_id'] = $hotelId;
        $room = Room::create($validated);
        return response()->json($room, 201);
    }

    
    public function update(Request $request, $hotelId, $roomId)
    {
        $hotel = Hotel::findOrFail($hotelId);
        $room = Room::where('hotel_id', $hotelId)->findOrFail($roomId);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'size' => 'nullable|numeric|min:0',
            'capacity' => 'nullable|integer|min:1',
            'beds' => 'nullable|string',
            'price' => 'sometimes|required|numeric|min:0',
            'original_price' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'amenities' => 'nullable|array',
            'images' => 'nullable|array',
            'image' => 'nullable|string',
            'available' => 'nullable|integer|min:0',
        ]);

        $room->update($validated);
        return response()->json($room);
    }

    
    public function destroy($hotelId, $roomId)
    {
        $hotel = Hotel::findOrFail($hotelId);
        $room = Room::where('hotel_id', $hotelId)->findOrFail($roomId);
        $room->delete();
        return response()->json(['message' => 'Room deleted successfully']);
    }
}
