<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Tour;
use App\Models\Blog;
use App\Models\Hotel;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Nếu là tour_manager, hiển thị dashboard tours
        if ($user->role === 'tour_manager') {
            $totalTours = Tour::where('created_by', $user->id)->count();
            $recentTours = Tour::where('created_by', $user->id)->latest()->limit(10)->get();
            
            $stats = [
                'total_tours' => $totalTours,
                'total_views' => 0,
                'avg_rating' => round(Tour::where('created_by', $user->id)->avg('rating') ?? 0, 1),
            ];
            
            return view('admin.tour-manager-dashboard', compact('recentTours', 'stats', 'user'));
        }
        
        // Nếu là hotel_manager, hiển thị dashboard hotels
        if ($user->role === 'hotel_manager') {
            $totalHotels = Hotel::where('created_by', $user->id)->count();
            $recentHotels = Hotel::where('created_by', $user->id)->latest()->limit(10)->get();
            
            $stats = [
                'total_hotels' => $totalHotels,
                'average_rating' => round(Hotel::where('created_by', $user->id)->avg('rating') ?? 0, 1),
                'total_reviews' => Hotel::where('created_by', $user->id)->sum('reviews') ?? 0,
            ];
            
            return view('admin.hotel-manager-dashboard', compact('recentHotels', 'stats', 'user'));
        }
        
        // Nếu là admin, hiển thị dashboard admin đầy đủ
        $totalUsers = User::count();
        $totalTours = Tour::count();
        $totalBlogs = Blog::count();
        $recentUsers = User::latest()->limit(5)->get();
        $recentTours = Tour::latest()->limit(5)->get();
        $recentBlogs = Blog::latest()->limit(5)->get();

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalTours',
            'totalBlogs',
            'recentUsers',
            'recentTours',
            'recentBlogs'
        ));
    }
}
