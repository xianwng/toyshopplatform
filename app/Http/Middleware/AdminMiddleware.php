<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect('/admin/login')->with('error', 'Please login to access the admin area.');
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Check if user has admin role
        if ($user->role !== 'admin') {
            Auth::logout();
            return redirect('/admin/login')->with('error', 'Unauthorized access. Admin accounts only.');
        }

        // Optional: Add account status check (if you have this functionality)
        // if (!$user->isActive()) {
        //     Auth::logout();
        //     return redirect('/admin/login')->with('error', 'Your account has been deactivated.');
        // }

        return $next($request);
    }
}