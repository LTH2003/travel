<?php

namespace App\Http\Controllers\Api;

use App\Models\Tour;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TourController extends Controller
{
    // Lấy tất cả tour (sắp xếp theo số lượng order và rating)
    public function index()
    {
        $tours = Tour::withCount('bookingDetails as order_count')
            ->orderByDesc('order_count')
            ->orderByDesc('rating')
            ->get()
            ->map(function($tour) {
                // Normalize category values
                $normalizedCategory = $tour->category;
                if (in_array(strtolower($tour->category), ['trong_nuoc', 'trong nước'])) {
                    $normalizedCategory = 'Trong nước';
                } elseif (in_array(strtolower($tour->category), ['quoc_te', 'quốc tế'])) {
                    $normalizedCategory = 'Quốc tế';
                }
                $tour->category = $normalizedCategory;
                return $tour;
            });
        
        return response()->json($tours);
    }

    // Lấy tour theo ID
    public function show($id)
    {
        $tour = Tour::find($id);

        if (!$tour) {
            return response()->json(['message' => 'Tour không tồn tại'], 404);
        }

        return response()->json($tour);
    }

    // Thêm tour mới
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|unique:tours',
            'destination' => 'required|string',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'duration' => 'required|string',
            'image' => 'nullable|string',
            'rating' => 'nullable|numeric|min:0|max:5',
        ]);

        $tour = Tour::create($validated);
        return response()->json($tour, 201);
    }

    // Cập nhật tour
    public function update(Request $request, $id)
    {
        $tour = Tour::find($id);

        if (!$tour) {
            return response()->json(['message' => 'Tour không tồn tại'], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'slug' => 'sometimes|string|unique:tours,slug,' . $id,
            'destination' => 'sometimes|string',
            'description' => 'sometimes|string',
            'price' => 'sometimes|numeric|min:0',
            'duration' => 'sometimes|string',
            'image' => 'nullable|string',
            'rating' => 'nullable|numeric|min:0|max:5',
        ]);

        $tour->update($validated);
        return response()->json($tour);
    }

    // Xóa tour
    public function destroy($id)
    {
        $tour = Tour::find($id);

        if (!$tour) {
            return response()->json(['message' => 'Tour không tồn tại'], 404);
        }

        $tour->delete();
        return response()->json(['message' => 'Tour đã được xóa thành công']);
    }
}
