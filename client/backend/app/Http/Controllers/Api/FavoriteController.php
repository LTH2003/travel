<?php

namespace App\Http\Controllers\Api;

use App\Models\Favorite;
use App\Models\Hotel;
use App\Models\Tour;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class FavoriteController extends Controller
{
    /**
     * Get user's favorites
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $favorites = Favorite::where('user_id', $user->id)->with('favoritable')->get();

        $hotels = [];
        $tours = [];

        foreach ($favorites as $favorite) {
            if ($favorite->favoritable_type === Hotel::class) {
                $hotels[] = $favorite->favoritable;
            } elseif ($favorite->favoritable_type === Tour::class) {
                $tours[] = $favorite->favoritable;
            }
        }

        return response()->json([
            'hotels' => $hotels,
            'tours' => $tours,
        ]);
    }

    /**
     * Add to favorites
     */
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:hotel,tour',
            'id' => 'required|integer',
        ]);

        $user = $request->user();
        $type = $request->type;
        $id = $request->id;

        if ($type === 'hotel') {
            $model = Hotel::findOrFail($id);
            $modelClass = Hotel::class;
        } else {
            $model = Tour::findOrFail($id);
            $modelClass = Tour::class;
        }

        // Check if already favorited
        $exists = Favorite::where('user_id', $user->id)
            ->where('favoritable_type', $modelClass)
            ->where('favoritable_id', $id)
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Đã thêm vào yêu thích',
            ]);
        }

        Favorite::create([
            'user_id' => $user->id,
            'favoritable_type' => $modelClass,
            'favoritable_id' => $id,
        ]);

        return response()->json([
            'message' => 'Thêm vào yêu thích thành công',
        ]);
    }

    /**
     * Remove from favorites
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'type' => 'required|in:hotel,tour',
            'id' => 'required|integer',
        ]);

        $user = $request->user();
        $type = $request->type;
        $id = $request->id;

        $modelClass = $type === 'hotel' ? Hotel::class : Tour::class;

        $favorite = Favorite::where('user_id', $user->id)
            ->where('favoritable_type', $modelClass)
            ->where('favoritable_id', $id)
            ->first();

        if ($favorite) {
            $favorite->delete();
            return response()->json([
                'message' => 'Xóa khỏi yêu thích thành công',
            ]);
        }

        return response()->json([
            'message' => 'Không tìm thấy mục yêu thích',
        ], 404);
    }

    /**
     * Check if item is favorited
     */
    public function check(Request $request)
    {
        $request->validate([
            'type' => 'required|in:hotel,tour',
            'id' => 'required|integer',
        ]);

        $user = $request->user();
        $type = $request->type;
        $id = $request->id;

        $modelClass = $type === 'hotel' ? Hotel::class : Tour::class;

        $isFavorited = Favorite::where('user_id', $user->id)
            ->where('favoritable_type', $modelClass)
            ->where('favoritable_id', $id)
            ->exists();

        return response()->json([
            'is_favorited' => $isFavorited,
        ]);
    }
}
