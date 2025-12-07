<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tour;
use Illuminate\Http\Request;

class TourManagerController extends Controller
{
    /**
     * Dashboard cho Tour Manager
     */
    public function dashboard()
    {
        $user = auth()->user();
        
        // Tour manager chỉ thấy tours của họ (nếu cần)
        $totalTours = Tour::count();
        $recentTours = Tour::latest()->take(10)->get();

        $stats = [
            'total_tours' => $totalTours,
            'total_views' => 0, // Views column doesn't exist yet
            'avg_rating' => round(Tour::avg('rating') ?? 0, 1),
        ];

        return view('admin.tour-manager.dashboard', compact('recentTours', 'stats', 'user'));
    }

    /**
     * Hiện danh sách tours
     */
    public function index(Request $request)
    {
        $query = Tour::query();

        // Search
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('destination', 'like', "%{$search}%");
        }

        // Sort
        $sort = $request->get('sort', 'latest');
        if ($sort === 'popular') {
            $query->orderByDesc('rating');
        } else {
            $query->latest();
        }

        $tours = $query->paginate(15);

        return view('admin.tour-manager.tours.index', compact('tours'));
    }

    /**
     * Show tour detail
     */
    public function show(Tour $tour)
    {
        return view('admin.tour-manager.tours.show', compact('tour'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        return view('admin.tour-manager.tours.create');
    }

    /**
     * Store new tour
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'destination' => 'required|string|max:255',
            'description' => 'required|string|min:20',
            'price' => 'required|numeric|min:0',
            'duration' => 'required|integer|min:1',
            'image' => 'nullable|image|max:2048',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('tours', 'public');
            $validated['image'] = $path;
        }

        Tour::create($validated);

        return redirect()->route('admin.tour-manager.tours.index')
                       ->with('success', 'Tour created successfully');
    }

    /**
     * Show edit form
     */
    public function edit(Tour $tour)
    {
        return view('admin.tour-manager.tours.edit', compact('tour'));
    }

    /**
     * Update tour
     */
    public function update(Request $request, Tour $tour)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'destination' => 'required|string|max:255',
            'description' => 'required|string|min:20',
            'price' => 'required|numeric|min:0',
            'duration' => 'required|integer|min:1',
            'image' => 'nullable|image|max:2048',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('tours', 'public');
            $validated['image'] = $path;
        }

        $tour->update($validated);

        return redirect()->route('admin.tour-manager.tours.index')
                       ->with('success', 'Tour updated successfully');
    }

    /**
     * Delete tour
     */
    public function destroy(Tour $tour)
    {
        $tour->delete();

        return redirect()->route('admin.tour-manager.tours.index')
                       ->with('success', 'Tour deleted successfully');
    }
}
