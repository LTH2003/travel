<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TourReview;
use App\Models\Tour;
use Illuminate\Http\Request;

class TourReviewController extends Controller
{
    /**
     * Display a listing of tour reviews
     */
    public function index(Request $request)
    {
        $search = $request->query('search', '');
        $status = $request->query('status', '');
        $perPage = $request->query('per_page', 15);

        $query = TourReview::with(['tour', 'user'])
            ->orderBy('created_at', 'desc');

        // Search by tour name or user name
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('tour', function ($tourQ) use ($search) {
                    $tourQ->where('title', 'like', "%{$search}%");
                })
                ->orWhereHas('user', function ($userQ) use ($search) {
                    $userQ->where('name', 'like', "%{$search}%");
                });
            });
        }

        // Filter by approval status
        if ($status === 'approved') {
            $query->where('is_approved', true);
        } elseif ($status === 'pending') {
            $query->where('is_approved', false);
        }

        $reviews = $query->paginate($perPage);

        // Get statistics
        $stats = [
            'total_reviews' => TourReview::count(),
            'approved_reviews' => TourReview::where('is_approved', true)->count(),
            'pending_reviews' => TourReview::where('is_approved', false)->count(),
        ];

        return view('admin.tour-reviews.index', compact('reviews', 'stats', 'search', 'status'));
    }

    /**
     * Show tour review details
     */
    public function show(TourReview $tourReview)
    {
        $tourReview->load(['tour', 'user']);
        return view('admin.tour-reviews.show', compact('tourReview'));
    }

    /**
     * Approve a review
     */
    public function approve(TourReview $tourReview)
    {
        $tourReview->update(['is_approved' => true]);
        
        // Update tour rating when review is approved
        $tourReview->tour->updateRating();

        return back()->with('success', 'Đánh giá đã được duyệt');
    }

    /**
     * Reject a review (mark as not approved)
     */
    public function reject(TourReview $tourReview)
    {
        $tourReview->update(['is_approved' => false]);
        
        // Update tour rating when review is rejected
        $tourReview->tour->updateRating();

        return back()->with('success', 'Đánh giá đã bị từ chối');
    }

    /**
     * Delete a review
     */
    public function destroy(TourReview $tourReview)
    {
        $tour = $tourReview->tour;
        $tourReview->delete();
        
        // Update tour rating when review is deleted
        $tour->updateRating();

        return back()->with('success', 'Đánh giá đã bị xóa');
    }

    /**
     * Bulk approve reviews
     */
    public function approveBulk(Request $request)
    {
        $ids = $request->input('ids', []);
        
        if (empty($ids)) {
            return back()->with('error', 'Vui lòng chọn ít nhất một đánh giá');
        }

        $reviews = TourReview::whereIn('id', $ids)->get();
        
        foreach ($reviews as $review) {
            $review->update(['is_approved' => true]);
            $review->tour->updateRating();
        }

        return back()->with('success', 'Đã duyệt ' . count($reviews) . ' đánh giá');
    }

    /**
     * Bulk reject reviews
     */
    public function rejectBulk(Request $request)
    {
        $ids = $request->input('ids', []);
        
        if (empty($ids)) {
            return back()->with('error', 'Vui lòng chọn ít nhất một đánh giá');
        }

        $reviews = TourReview::whereIn('id', $ids)->get();
        
        foreach ($reviews as $review) {
            $review->update(['is_approved' => false]);
            $review->tour->updateRating();
        }

        return back()->with('success', 'Đã từ chối ' . count($reviews) . ' đánh giá');
    }

    /**
     * Bulk delete reviews
     */
    public function deleteBulk(Request $request)
    {
        $ids = $request->input('ids', []);
        
        if (empty($ids)) {
            return back()->with('error', 'Vui lòng chọn ít nhất một đánh giá');
        }

        $reviews = TourReview::whereIn('id', $ids)->get();
        $toursToUpdate = [];
        
        foreach ($reviews as $review) {
            $toursToUpdate[$review->tour_id] = $review->tour;
            $review->delete();
        }

        // Update ratings for affected tours
        foreach ($toursToUpdate as $tour) {
            $tour->updateRating();
        }

        return back()->with('success', 'Đã xóa ' . count($reviews) . ' đánh giá');
    }
}
