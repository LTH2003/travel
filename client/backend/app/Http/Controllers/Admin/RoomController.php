<?php

namespace App\Http\Controllers\Admin;

use App\Models\Hotel;
use App\Models\Room;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RoomController extends Controller
{
    public function index(Hotel $hotel)
    {
        $rooms = $hotel->rooms()->paginate(10);
        $hotels = Hotel::all();
        return view('admin.rooms.index', compact('hotel', 'rooms', 'hotels'));
    }

    public function create(Hotel $hotel)
    {
        $hotels = Hotel::all();
        return view('admin.rooms.create', compact('hotel', 'hotels'));
    }

    public function store(Request $request, Hotel $hotel)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'capacity' => 'required|integer|min:1',
            'image' => 'nullable|string',
            'amenities' => 'nullable|string',
            'description' => 'nullable|string',
            'bed_type' => 'nullable|string',
            'bathroom' => 'nullable|string',
            'area' => 'nullable|integer',
        ]);

        $hotel->rooms()->create($validated);
        return redirect("/admin/hotels/{$hotel->id}/rooms")->with('success', 'Room created successfully');
    }

    public function edit(Hotel $hotel, Room $room)
    {
        $hotels = Hotel::all();
        return view('admin.rooms.edit', compact('hotel', 'room', 'hotels'));
    }

    public function update(Request $request, Hotel $hotel, Room $room)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'capacity' => 'required|integer|min:1',
            'image' => 'nullable|string',
            'amenities' => 'nullable|string',
            'description' => 'nullable|string',
            'bed_type' => 'nullable|string',
            'bathroom' => 'nullable|string',
            'area' => 'nullable|integer',
        ]);

        $room->update($validated);
        return redirect("/admin/hotels/{$hotel->id}/rooms")->with('success', 'Room updated successfully');
    }

    public function destroy(Hotel $hotel, Room $room)
    {
        $room->delete();
        return redirect("/admin/hotels/{$hotel->id}/rooms")->with('success', 'Room deleted successfully');
    }
}
