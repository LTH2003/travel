<?php

namespace App\Http\Controllers\Api;

use App\Models\Favorite;
use App\Models\Hotel;
use App\Models\Tour;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class RecommendationController extends Controller
{
    /**
     * Get AI-based recommendations based on user favorites
     */
    public function getRecommendations(Request $request)
    {
        $user = $request->user();
        
        $favorites = Favorite::where('user_id', $user->id)->with('favoritable')->get();
        
        $favoriteHotels = $favorites
            ->filter(fn($f) => $f->favoritable_type === Hotel::class)
            ->map(fn($f) => $f->favoritable)
            ->toArray();
        
        $favoriteTours = $favorites
            ->filter(fn($f) => $f->favoritable_type === Tour::class)
            ->map(fn($f) => $f->favoritable)
            ->toArray();
        
        if (count($favoriteHotels) === 0 && count($favoriteTours) === 0) {
            return response()->json([
                'recommended_hotels' => Hotel::with('rooms')->orderBy('rating', 'desc')->limit(6)->get(),
                'recommended_tours' => Tour::orderBy('rating', 'desc')->limit(6)->get(),
                'reason' => 'Những khách sạn và tour phổ biến nhất'
            ]);
        }
        
        $hotelLocations = collect($favoriteHotels)->pluck('location')->toArray();
        $tourDestinations = collect($favoriteTours)->pluck('destination')->toArray();
        $avgHotelPrice = collect($favoriteHotels)->avg('price') ?? 0;
        $avgTourPrice = collect($favoriteTours)->avg('price') ?? 0;
        
        $allHotels = Hotel::with('rooms')->get();
        $allTours = Tour::all();
        
        $scoredHotels = $allHotels->map(function ($hotel) use ($favoriteHotels, $avgHotelPrice, $hotelLocations) {
            $score = 0;
            
            // 1. giống địa điểm (40 điểm)
            if (in_array($hotel->location, $hotelLocations)) {
                $score += 40;
            } else {
                foreach ($hotelLocations as $favLocation) {
                    if (stripos($hotel->location, explode(' ', $favLocation)[0]) !== false) {
                        $score += 15;
                    }
                }
            }
            
            // 2. giá tương tự (30 points)
            if ($avgHotelPrice > 0) {
                $priceDiff = abs($hotel->price - $avgHotelPrice) / $avgHotelPrice;
                if ($priceDiff < 0.2) {
                    $score += 30;
                } elseif ($priceDiff < 0.5) {
                    $score += 15;
                } elseif ($priceDiff < 1) {
                    $score += 5;
                }
            }
            
            // 3. sô sao danh gia (20 points)
            $score += $hotel->rating * 4;
            
            // 4. loại trừ đi các ks đã thích
            if (in_array($hotel->id, collect($favoriteHotels)->pluck('id')->toArray())) {
                $score = 0;
            }
            
            return [
                'hotel' => $hotel,
                'score' => $score
            ];
        })
        ->sortByDesc('score')
        ->filter(fn($item) => $item['score'] > 0)
        ->take(6)
        ->pluck('hotel')
        ->values();
        
        // Score tours based on similarity
        $scoredTours = $allTours->map(function ($tour) use ($favoriteTours, $avgTourPrice, $tourDestinations) {
            $score = 0;
            
            // 1. Destination similarity (40 points)
            if (in_array($tour->destination, $tourDestinations)) {
                $score += 40;
            } else {
                // Partial match
                foreach ($tourDestinations as $favDestination) {
                    if (stripos($tour->destination, explode(' ', $favDestination)[0]) !== false) {
                        $score += 15;
                    }
                }
            }
            
            // 2. Price range similarity (30 points)
            if ($avgTourPrice > 0) {
                $priceDiff = abs($tour->price - $avgTourPrice) / $avgTourPrice;
                if ($priceDiff < 0.2) {
                    $score += 30;
                } elseif ($priceDiff < 0.5) {
                    $score += 15;
                } elseif ($priceDiff < 1) {
                    $score += 5;
                }
            }
            
            // 3. Rating (20 points)
            $score += ($tour->rating ?? 0) * 4;
            
            // 4. Filter out already favorited
            if (in_array($tour->id, collect($favoriteTours)->pluck('id')->toArray())) {
                $score = 0;
            }
            
            return [
                'tour' => $tour,
                'score' => $score
            ];
        })
        ->sortByDesc('score')
        ->filter(fn($item) => $item['score'] > 0)
        ->take(6)
        ->pluck('tour')
        ->values();
        
        return response()->json([
            'recommended_hotels' => $scoredHotels,
            'recommended_tours' => $scoredTours,
            'reason' => 'Đề xuất dựa trên sở thích của bạn'
        ]);
    }
}
