<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tour;
use Illuminate\Http\Request;

class TourController extends Controller
{
    // Danh sách tour
    public function index()
    {
        $tours = Tour::paginate(15);
        return view('admin.tours.index', compact('tours'));
    }

    // Hiển thị form tạo tour mới
    public function create()
    {
        return view('admin.tours.create');
    }

    // Lưu tour mới
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

        Tour::create($validated);

        return redirect()->route('admin.tours.index')->with('success', 'Tạo tour thành công');
    }

    // Hiển thị chi tiết tour
    public function show(Tour $tour)
    {
        return view('admin.tours.show', compact('tour'));
    }

    // Hiển thị form chỉnh sửa
    public function edit(Tour $tour)
    {
        return view('admin.tours.edit', compact('tour'));
    }

    // Cập nhật tour
    public function update(Request $request, Tour $tour)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|unique:tours,slug,' . $tour->id,
            'destination' => 'required|string',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'duration' => 'required|string',
            'image' => 'nullable|string',
            'rating' => 'nullable|numeric|min:0|max:5',
        ]);

        $tour->update($validated);

        return redirect()->route('admin.tours.index')->with('success', 'Cập nhật tour thành công');
    }

    // Xóa tour
    public function destroy(Tour $tour)
    {
        $tour->delete();
        return redirect()->route('admin.tours.index')->with('success', 'Xóa tour thành công');
    }
}
