<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsReceptionist
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->role === 'receptionist') {
            return $next($request);
        }

        return redirect('/')->with('error', 'Bạn không có quyền truy cập trang này');
    }
}
