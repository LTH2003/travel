<?php

namespace App\Http\Controllers\Admin;

use App\Models\Hotel;
use App\Models\Room;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RoomController extends Controller
{
    /**
     * Kiểm tra xem user có quyền truy cập khách sạn không
     */
    private function checkHotelAccess(Hotel $hotel)
    {
        $user = auth()->user();
        // Nếu là hotel_manager, chỉ có thể truy cập hotel của họ tạo
        if ($user->role === 'hotel_manager' && $hotel->created_by !== $user->id) {
            abort(403, 'Bạn không có quyền truy cập khách sạn này. Chỉ admin mới có thể vào.');
        }
    }

    /**
     * Parse JSON fields from form input
     */
    private function parseJsonFields($data)
    {
        // Parse images - nếu là string, thử parse thành JSON array
        if (isset($data['images']) && is_string($data['images'])) {
            try {
                $images = json_decode($data['images'], true);
                if (is_array($images)) {
                    $data['images'] = $images;
                } else {
                    $data['images'] = [$data['images']];
                }
            } catch (\Exception $e) {
                $data['images'] = [$data['images']];
            }
        }

        // Parse amenities - nếu là string, thử parse thành JSON array
        if (isset($data['amenities']) && is_string($data['amenities'])) {
            try {
                $amenities = json_decode($data['amenities'], true);
                if (is_array($amenities)) {
                    $data['amenities'] = $amenities;
                } else {
                    $data['amenities'] = [$data['amenities']];
                }
            } catch (\Exception $e) {
                $data['amenities'] = [$data['amenities']];
            }
        }

        return $data;
    }

    public function index(Hotel $hotel)
    {
        $this->checkHotelAccess($hotel);
        
        $rooms = $hotel->rooms()->paginate(10);
        $hotels = Hotel::all();
        return view('admin.rooms.index', compact('hotel', 'rooms', 'hotels'));
    }

    public function create(Hotel $hotel)
    {
        $this->checkHotelAccess($hotel);
        
        $hotels = Hotel::all();
        return view('admin.rooms.create', compact('hotel', 'hotels'));
    }

    public function show(Hotel $hotel, Room $room)
    {
        $this->checkHotelAccess($hotel);
        
        return view('admin.rooms.show', compact('hotel', 'room'));
    }

    public function store(Request $request, Hotel $hotel)
    {
        $this->checkHotelAccess($hotel);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'original_price' => 'nullable|numeric|min:0',
            'capacity' => 'required|integer|min:1',
            'amenities' => 'nullable|string',
            'description' => 'nullable|string',
            'size' => 'nullable|numeric|min:0',
            'beds' => 'nullable|string',
            'images' => 'nullable|string',
            'available' => 'required|integer|min:0',
        ]);

        // Parse JSON fields
        $validated = $this->parseJsonFields($validated);

        $hotel->rooms()->create($validated);
        return redirect("/admin/hotels/{$hotel->id}/rooms")->with('success', 'Room created successfully');
    }

    public function edit(Hotel $hotel, Room $room)
    {
        $this->checkHotelAccess($hotel);
        
        $hotels = Hotel::all();
        return view('admin.rooms.edit', compact('hotel', 'room', 'hotels'));
    }

    public function update(Request $request, Hotel $hotel, Room $room)
    {
        $this->checkHotelAccess($hotel);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'original_price' => 'nullable|numeric|min:0',
            'capacity' => 'required|integer|min:1',
            'amenities' => 'nullable|string',
            'description' => 'nullable|string',
            'size' => 'nullable|numeric|min:0',
            'beds' => 'nullable|string',
            'images' => 'nullable|string',
            'available' => 'required|integer|min:0',
        ]);

        // Parse JSON fields
        $validated = $this->parseJsonFields($validated);

        $room->update($validated);
        return redirect("/admin/hotels/{$hotel->id}/rooms")->with('success', 'Room updated successfully');
    }

    public function destroy(Hotel $hotel, Room $room)
    {
        $this->checkHotelAccess($hotel);
        
        $room->delete();
        return redirect("/admin/hotels/{$hotel->id}/rooms")->with('success', 'Room deleted successfully');
    }
}
