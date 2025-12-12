<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Hiển thị trang đăng nhập
    public function showLogin()
    {
        if (Auth::check()) {
            if (Auth::user()->role === 'admin') {
                return redirect()->route('admin.dashboard');
            } elseif (Auth::user()->role === 'receptionist') {
                return redirect()->route('receptionist.dashboard');
            }
        }
        return view('admin.auth.login');
    }

    // Xử lý đăng nhập
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return back()->with('error', 'Email hoặc mật khẩu không đúng');
        }

        // Cho phép đăng nhập nếu là admin, tour_manager, hotel_manager hoặc receptionist
        $allowed_roles = ['admin', 'tour_manager', 'hotel_manager', 'receptionist'];
        if (!in_array($user->role, $allowed_roles)) {
            return back()->with('error', 'Bạn không có quyền truy cập trang quản trị');
        }

        Auth::login($user);

        // Redirect based on role
        if ($user->role === 'receptionist') {
            return redirect()->route('receptionist.dashboard')->with('success', 'Đăng nhập thành công');
        }

        return redirect()->route('admin.dashboard')->with('success', 'Đăng nhập thành công');
    }

    // Đăng xuất
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login')->with('success', 'Đã đăng xuất thành công');
    }
}
