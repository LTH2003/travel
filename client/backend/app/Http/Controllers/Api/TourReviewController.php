<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tour;
use App\Models\TourReview;
use Illuminate\Http\Request;

class TourReviewController extends Controller
{
    /**
     * Get all reviews for a tour
     */
    public function getReviews($tourId)
    {
        $user = auth('sanctum')->user();
        $userId = $user?->id;

        // Get the tour to get stats
        $tour = Tour::findOrFail($tourId);

        // Get approved reviews + user's own reviews (approved or not)
        $reviews = TourReview::where('tour_id', $tourId)
            ->where(function ($query) use ($userId) {
                $query->where('is_approved', true)
                    ->orWhere('user_id', $userId);
            })
            ->with('user:id,name,avatar')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Get rating distribution (only approved)
        $ratingDistribution = [];
        for ($i = 5; $i >= 1; $i--) {
            $ratingDistribution[$i] = TourReview::where('tour_id', $tourId)
                ->where('is_approved', true)
                ->where('rating', $i)
                ->count();
        }

        // Transform the reviews to add can_edit and can_delete
        $transformedReviews = $reviews->getCollection()->map(function ($review) use ($userId) {
            return [
                'id' => $review->id,
                'tour_id' => $review->tour_id,
                'user_id' => $review->user_id,
                'user' => $review->user,
                'rating' => $review->rating,
                'title' => $review->title,
                'comment' => $review->comment,
                'is_approved' => $review->is_approved,
                'created_at' => $review->created_at,
                'can_edit' => $userId === $review->user_id,
                'can_delete' => $userId === $review->user_id,
            ];
        });
        
        $reviews->setCollection($transformedReviews);

        return response()->json([
            'status' => true,
            'data' => $reviews,
            'stats' => [
                'average_rating' => $tour->rating ?? 0,
                'total_reviews' => $tour->approvedReviewsCount(),
                'rating_distribution' => $ratingDistribution,
            ],
        ]);
    }

    /**
     * Store a new review
     */
    public function store(Request $request, $tourId)
    {
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'Vui lòng đăng nhập để đánh giá',
                ], 401);
            }

            $validated = $request->validate([
                'rating' => 'required|integer|min:1|max:5',
                'title' => 'nullable|string|max:255',
                'comment' => 'nullable|string|max:2000',
            ]);

            // Check if user already reviewed this tour - if so, update it instead
            $existingReview = TourReview::where('tour_id', $tourId)
                ->where('user_id', $user->id)
                ->first();

            if ($existingReview) {
                // Update existing review instead of creating a new one
                $existingReview->update([
                    'rating' => $validated['rating'],
                    'title' => $validated['title'] ?? null,
                    'comment' => $validated['comment'] ?? null,
                    'is_approved' => true,
                ]);
                
                $existingReview->load('user:id,name,avatar');
                
                return response()->json([
                    'status' => true,
                    'message' => 'Cập nhật đánh giá thành công!',
                    'data' => $existingReview,
                ], 201);
            }

            $review = TourReview::create([
                'tour_id' => $tourId,
                'user_id' => $user->id,
                'rating' => $validated['rating'],
                'title' => $validated['title'] ?? null,
                'comment' => $validated['comment'] ?? null,
                'is_approved' => true,
            ]);

            $review->load('user:id,name,avatar');

            return response()->json([
                'status' => true,
                'message' => 'Đánh giá của bạn đã được gửi thành công!',
                'data' => $review,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Update a review
     */
    public function update(Request $request, $reviewId)
    {
        try {
            $user = $request->user();
            $review = TourReview::findOrFail($reviewId);

            if ($review->user_id !== $user->id) {
                return response()->json([
                    'status' => false,
                    'message' => 'Bạn không có quyền chỉnh sửa đánh giá này',
                ], 403);
            }

            $validated = $request->validate([
                'rating' => 'required|integer|min:1|max:5',
                'title' => 'nullable|string|max:255',
                'comment' => 'nullable|string|max:2000',
            ]);

            $review->update([
                'rating' => $validated['rating'],
                'title' => $validated['title'] ?? null,
                'comment' => $validated['comment'] ?? null,
                'is_approved' => true,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Cập nhật đánh giá thành công',
                'data' => $review,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Delete a review
     */
    public function destroy($reviewId)
    {
        try {
            $user = auth()->user();
            $review = TourReview::findOrFail($reviewId);

            if ($review->user_id !== $user->id && $user->role !== 'admin') {
                return response()->json([
                    'status' => false,
                    'message' => 'Bạn không có quyền xóa đánh giá này',
                ], 403);
            }

            $review->delete();

            return response()->json([
                'status' => true,
                'message' => 'Xóa đánh giá thành công',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
