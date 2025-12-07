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
        $user = auth()->user();
        $query = Tour::query();

        // Nếu là tour_manager, chỉ hiển thị tour do họ tạo
        if ($user->role === 'tour_manager') {
            $query->where('created_by', $user->id);
        }

        $tours = $query->paginate(15);
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
            'title' => 'required|string|max:255',
            'destination' => 'required|string',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0|max:99999999.99',
            'original_price' => 'nullable|numeric|min:0|max:99999999.99',
            'duration' => 'required|string',
            'category' => 'required|in:trong_nuoc,quoc_te',
            'max_guests' => 'nullable|integer|min:1',
            'image' => 'nullable|string',
            'rating' => 'nullable|numeric|min:0|max:5',
        ]);

        // Gán created_by cho user hiện tại
        $validated['created_by'] = auth()->id();
        
        Tour::create($validated);

        return redirect()->route('admin.tours.index')->with('success', 'Tạo tour thành công');
    }

    // Hiển thị chi tiết tour
    public function show(Tour $tour)
    {
        // Kiểm tra quyền - tour_manager chỉ xem tour của họ
        if (auth()->user()->role === 'tour_manager' && $tour->created_by !== auth()->id()) {
            abort(403, 'Không có quyền xem tour này');
        }

        return view('admin.tours.show', compact('tour'));
    }

    // Hiển thị form chỉnh sửa
    public function edit(Tour $tour)
    {
        // Kiểm tra quyền - tour_manager chỉ sửa tour của họ
        if (auth()->user()->role === 'tour_manager' && $tour->created_by !== auth()->id()) {
            abort(403, 'Không có quyền sửa tour này');
        }

        return view('admin.tours.edit', compact('tour'));
    }

    // Cập nhật tour
    public function update(Request $request, Tour $tour)
    {
        // Kiểm tra quyền - tour_manager chỉ sửa tour của họ
        if (auth()->user()->role === 'tour_manager' && $tour->created_by !== auth()->id()) {
            abort(403, 'Không có quyền sửa tour này');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'destination' => 'required|string',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0|max:99999999.99',
            'original_price' => 'nullable|numeric|min:0|max:99999999.99',
            'duration' => 'required|string',
            'category' => 'required|in:trong_nuoc,quoc_te',
            'max_guests' => 'nullable|integer|min:1',
            'image' => 'nullable|string',
            'rating' => 'nullable|numeric|min:0|max:5',
        ]);

        $tour->update($validated);

        return redirect()->route('admin.tours.index')->with('success', 'Cập nhật tour thành công');
    }

    // Xóa tour
    public function destroy(Tour $tour)
    {
        // Kiểm tra quyền - tour_manager chỉ xóa tour của họ
        if (auth()->user()->role === 'tour_manager' && $tour->created_by !== auth()->id()) {
            abort(403, 'Không có quyền xóa tour này');
        }

        $tour->delete();
        return redirect()->route('admin.tours.index')->with('success', 'Xóa tour thành công');
    }
}
