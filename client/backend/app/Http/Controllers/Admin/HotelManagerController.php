<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use Illuminate\Http\Request;

class HotelManagerController extends Controller
{
    /**
     * Dashboard cho Hotel Manager
     */
    public function dashboard()
    {
        $user = auth()->user();
        
        $totalHotels = Hotel::count();
        $recent_hotels = Hotel::latest()->take(10)->get();

        $stats = [
            'total_hotels' => $totalHotels,
            'total_rooms' => Hotel::sum('rooms_count') ?? 0,
            'average_rating' => round(Hotel::avg('rating') ?? 0, 1),
            'available_rooms' => 0, // Sẽ cập nhật khi có logic phòng trống
        ];

        return view('admin.hotel-manager.dashboard', compact('recent_hotels', 'stats', 'user'));
    }

    /**
     * Danh sách khách sạn
     */
    public function index(Request $request)
    {
        $query = Hotel::query();

        // Search
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
        }

        // Sort
        $sort = $request->get('sort', 'latest');
        if ($sort === 'popular') {
            $query->orderByDesc('rating');
        } else {
            $query->latest();
        }

        $hotels = $query->paginate(15);

        return view('admin.hotel-manager.hotels.index', compact('hotels'));
    }

    /**
     * Show hotel detail
     */
    public function show(Hotel $hotel)
    {
        return view('admin.hotel-manager.hotels.show', compact('hotel'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        return view('admin.hotel-manager.hotels.create');
    }

    /**
     * Store new hotel
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|min:20',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'rating' => 'nullable|numeric|min:0|max:5',
            'image' => 'nullable|image|max:2048',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('hotels', 'public');
            $validated['image'] = $path;
        }

        Hotel::create($validated);

        return redirect()->route('admin.hotel-manager.hotels.index')
                       ->with('success', 'Hotel created successfully');
    }

    /**
     * Show edit form
     */
    public function edit(Hotel $hotel)
    {
        return view('admin.hotel-manager.hotels.edit', compact('hotel'));
    }

    /**
     * Update hotel
     */
    public function update(Request $request, Hotel $hotel)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|min:20',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'rating' => 'nullable|numeric|min:0|max:5',
            'image' => 'nullable|image|max:2048',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('hotels', 'public');
            $validated['image'] = $path;
        }

        $hotel->update($validated);

        return redirect()->route('admin.hotel-manager.hotels.index')
                       ->with('success', 'Hotel updated successfully');
    }

    /**
     * Delete hotel
     */
    public function destroy(Hotel $hotel)
    {
        $hotel->delete();

        return redirect()->route('admin.hotel-manager.hotels.index')
                       ->with('success', 'Hotel deleted successfully');
    }
}
