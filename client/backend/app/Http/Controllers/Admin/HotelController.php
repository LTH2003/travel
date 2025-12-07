<?php

namespace App\Http\Controllers\Admin;

use App\Models\Hotel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HotelController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $query = Hotel::query();

        // Nếu là hotel_manager, chỉ hiển thị hotel do họ tạo
        if ($user->role === 'hotel_manager') {
            $query->where('created_by', $user->id);
        }

        $hotels = $query->with('rooms')->paginate(10);
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
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
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
            'amenities' => 'nullable|string',
        ]);

        // Gán created_by cho user hiện tại
        $validated['created_by'] = auth()->id();

        Hotel::create($validated);
        return redirect('/admin/hotels')->with('success', 'Hotel created successfully');
    }

    public function show(Hotel $hotel)
    {
        // Kiểm tra quyền - hotel_manager chỉ xem hotel của họ
        if (auth()->user()->role === 'hotel_manager' && $hotel->created_by !== auth()->id()) {
            abort(403, 'Không có quyền xem khách sạn này');
        }

        return view('admin.hotels.show', compact('hotel'));
    }

    public function edit(Hotel $hotel)
    {
        // Kiểm tra quyền - hotel_manager chỉ sửa hotel của họ
        if (auth()->user()->role === 'hotel_manager' && $hotel->created_by !== auth()->id()) {
            abort(403, 'Không có quyền sửa khách sạn này');
        }

        return view('admin.hotels.edit', compact('hotel'));
    }

    public function update(Request $request, Hotel $hotel)
    {
        // Kiểm tra quyền - hotel_manager chỉ sửa hotel của họ
        if (auth()->user()->role === 'hotel_manager' && $hotel->created_by !== auth()->id()) {
            abort(403, 'Không có quyền sửa khách sạn này');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
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
            'amenities' => 'nullable|string',
        ]);

        $hotel->update($validated);
        return redirect('/admin/hotels')->with('success', 'Hotel updated successfully');
    }

    public function destroy(Hotel $hotel)
    {
        // Kiểm tra quyền - hotel_manager chỉ xóa hotel của họ
        if (auth()->user()->role === 'hotel_manager' && $hotel->created_by !== auth()->id()) {
            abort(403, 'Không có quyền xóa khách sạn này');
        }

        $hotel->delete();
        return redirect('/admin/hotels')->with('success', 'Hotel deleted successfully');
    }
}
