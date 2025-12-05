<?php

namespace App\Http\Controllers\Api;

use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class CartController extends Controller
{
    /**
     * Get user's cart
     */
    public function show(Request $request)
    {
        $user = $request->user();
        $cart = Cart::where('user_id', $user->id)->first();

        if (!$cart) {
            return response()->json([
                'items' => [],
                'total' => 0,
            ]);
        }

        return response()->json($cart);
    }

    /**
     * Save/update user's cart
     */
    public function store(Request $request)
    {
        $request->validate([
            'items' => 'nullable|array',
            'total' => 'nullable|numeric',
        ]);

        $user = $request->user();
        $cart = Cart::updateOrCreate(
            ['user_id' => $user->id],
            [
                'items' => $request->items ?? [],
                'total' => $request->total ?? 0,
            ]
        );

        return response()->json([
            'message' => 'Giỏ hàng đã được lưu',
            'cart' => $cart,
        ]);
    }

    /**
     * Clear user's cart
     */
    public function destroy(Request $request)
    {
        $user = $request->user();
        Cart::where('user_id', $user->id)->delete();

        return response()->json([
            'message' => 'Giỏ hàng đã được xóa',
        ]);
    }
}
