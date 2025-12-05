<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Tour;
use App\Models\Blog;

class DashboardController extends Controller
{
    public function index()
    {
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
