<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use Illuminate\Http\Request;

class HotelController extends Controller
{
    public function index()
    {
        return response()->json(Hotel::with('rooms')->get());
    }

    public function show($id)
    {
        return response()->json(
            Hotel::with('rooms')->findOrFail($id)
        );
    }

    // Admin: Create hotel
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'original_price' => 'nullable|numeric|min:0',
            'rating' => 'nullable|numeric|min:0|max:5',
            'reviews' => 'nullable|integer|min:0',
            'image' => 'nullable|string',
            'images' => 'nullable|array',
            'amenities' => 'nullable|array',
            'check_in' => 'nullable|string',
            'check_out' => 'nullable|string',
            'cancellation' => 'nullable|string',
            'children' => 'nullable|string',
        ]);

        $hotel = Hotel::create($validated);
        return response()->json($hotel, 201);
    }

    // Admin: Update hotel
    public function update(Request $request, $id)
    {
        $hotel = Hotel::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'location' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'sometimes|required|numeric|min:0',
            'original_price' => 'nullable|numeric|min:0',
            'rating' => 'nullable|numeric|min:0|max:5',
            'reviews' => 'nullable|integer|min:0',
            'image' => 'nullable|string',
            'images' => 'nullable|array',
            'amenities' => 'nullable|array',
            'check_in' => 'nullable|string',
            'check_out' => 'nullable|string',
            'cancellation' => 'nullable|string',
            'children' => 'nullable|string',
        ]);

        $hotel->update($validated);
        return response()->json($hotel);
    }

    // Admin: Delete hotel
    public function destroy($id)
    {
        $hotel = Hotel::findOrFail($id);
        $hotel->delete();
        return response()->json(['message' => 'Hotel deleted successfully']);
    }
}
