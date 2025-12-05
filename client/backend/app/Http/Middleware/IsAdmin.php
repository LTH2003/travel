<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsAdmin
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user || $user->role !== 'admin') {
            // Nếu là JSON request, trả về JSON
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Bạn không có quyền truy cập (chỉ admin).',
                ], 403);
            }
            
            // Nếu là web request, redirect về login với thông báo lỗi
            return redirect()->route('admin.login')
                ->with('error', 'Bạn không có quyền truy cập. Chỉ admin mới có thể vào.');
        }
        return $next($request);
    }
}
