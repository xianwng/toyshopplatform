<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Show super admin login form
     */
    public function showLogin()
    {
        return view('super-admin.auth.login');
    }

    /**
     * Handle super admin login request
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            /** @var \App\Models\User $user */
            $user = Auth::user();
            
            // Record login time
            $user->recordLogin();
            
            if ($user->isSuperAdmin()) {
                return redirect()->route('super-admin.dashboard');
            }
            
            // If not super admin, show error message
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            return back()->withErrors([
                'email' => 'Only Super Admin accounts can access this system.',
            ])->onlyInput('email');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Handle super admin logout request
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/super-admin/login');
    }
}