<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SuperAdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->role === 'admin') {
            return $next($request);
        }

        return redirect()->route('admin.dashboard')->with('error', 'Hanya Admin yang dapat mengakses halaman ini.');
    }
}
