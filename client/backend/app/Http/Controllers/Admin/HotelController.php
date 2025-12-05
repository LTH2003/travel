<?php

namespace App\Http\Controllers\Admin;

use App\Models\Hotel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HotelController extends Controller
{
    public function index()
    {
        $hotels = Hotel::with('rooms')->paginate(10);
        return view('admin.hotels.index', compact('hotels'));
    }

    public function create()
    {
        return view('admin.hotels.create');
    }

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
            'check_in' => 'nullable|string',
            'check_out' => 'nullable|string',
            'cancellation' => 'nullable|string',
            'children' => 'nullable|string',
        ]);

        Hotel::create($validated);
        return redirect('/admin/hotels')->with('success', 'Hotel created successfully');
    }

    public function edit(Hotel $hotel)
    {
        return view('admin.hotels.edit', compact('hotel'));
    }

    public function update(Request $request, Hotel $hotel)
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
            'check_in' => 'nullable|string',
            'check_out' => 'nullable|string',
            'cancellation' => 'nullable|string',
            'children' => 'nullable|string',
        ]);

        $hotel->update($validated);
        return redirect('/admin/hotels')->with('success', 'Hotel updated successfully');
    }

    public function destroy(Hotel $hotel)
    {
        $hotel->delete();
        return redirect('/admin/hotels')->with('success', 'Hotel deleted successfully');
    }
}
