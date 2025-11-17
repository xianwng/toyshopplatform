<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SuperAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect('/super-admin/login')->with('error', 'Please login first.');
        }

        // Check if user has super admin role
        if (Auth::user()->role !== 'super_admin') {
            return redirect('/super-admin/login')->with('error', 'Unauthorized access.');
        }

        return $next($request);
    }
}