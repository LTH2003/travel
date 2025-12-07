<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ManagerAuthController extends Controller
{
    /**
     * Hiển thị trang đăng nhập cho Tour Manager
     */
    public function showTourManagerLogin()
    {
        if (Auth::check() && Auth::user()->role === 'tour_manager') {
            return redirect()->route('admin.tour-manager.dashboard');
        }
        return view('admin.auth.tour-manager-login');
    }

    /**
     * Xử lý đăng nhập Tour Manager
     */
    public function loginTourManager(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return back()->with('error', 'Email hoặc mật khẩu không đúng');
        }

        if ($user->role !== 'tour_manager') {
            return back()->with('error', 'Bạn không có quyền truy cập');
        }

        Auth::login($user);
        return redirect()->route('admin.tour-manager.dashboard')->with('success', 'Đăng nhập thành công');
    }

    /**
     * Hiển thị trang đăng nhập cho Hotel Manager
     */
    public function showHotelManagerLogin()
    {
        if (Auth::check() && Auth::user()->role === 'hotel_manager') {
            return redirect()->route('admin.hotel-manager.dashboard');
        }
        return view('admin.auth.hotel-manager-login');
    }

    /**
     * Xử lý đăng nhập Hotel Manager
     */
    public function loginHotelManager(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return back()->with('error', 'Email hoặc mật khẩu không đúng');
        }

        if ($user->role !== 'hotel_manager') {
            return back()->with('error', 'Bạn không có quyền truy cập');
        }

        Auth::login($user);
        return redirect()->route('admin.hotel-manager.dashboard')->with('success', 'Đăng nhập thành công');
    }

    /**
     * Đăng xuất
     */
    public function logout()
    {
        Auth::logout();
        return redirect('/')->with('success', 'Đã đăng xuất');
    }
}
