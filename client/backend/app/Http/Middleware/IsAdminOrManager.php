<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdminOrManager
{
    /**
     * Kiểm tra user có role admin, tour_manager hoặc hotel_manager
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('admin.login');
        }

        $user = auth()->user();
        
        if (!in_array($user->role, ['admin', 'tour_manager', 'hotel_manager'])) {
            abort(403, 'Không có quyền truy cập');
        }

        return $next($request);
    }
}
