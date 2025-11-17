<?php

namespace App\Http\Controllers\Security;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller {
    public function showLogin() {
        return view('Security.login');
    }

    public function login(Request $request) {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        $field = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $credentials = [$field => $request->login, 'password' => $request->password];

        if (Auth::attempt($credentials)) {
            return redirect()->intended(route('home'));
        }

        return back()->withErrors(['login' => 'Invalid credentials.'])->withInput();
    }

    public function checkUserExists(Request $request)
    {
        $login = $request->query('login');
        
        if (!$login) {
            return response()->json(['exists' => false]);
        }

        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $exists = User::where($field, $login)->exists();
        
        return response()->json(['exists' => $exists]);
    }

    public function checkCredentials(Request $request)
    {
        $login = $request->input('login');
        $password = $request->input('password');
        
        if (!$login || !$password) {
            return response()->json(['valid' => false]);
        }

        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $user = User::where($field, $login)->first();
        
        if (!$user) {
            return response()->json(['valid' => false]);
        }

        $valid = Hash::check($password, $user->password);
        return response()->json(['valid' => $valid]);
    }

    public function showRegister() {
        return view('Security.register');
    }

    public function register(Request $request) {
        $request->validate([
            'first_name' => 'required|string|min:3|max:255',
            'middle_name' => 'nullable|string|size:1',
            'last_name' => 'required|string|min:3|max:255',
            'username' => 'required|string|unique:users|min:3|max:255',
            'email' => 'required|string|email|unique:users|max:255',
            'password' => 'required|string|confirmed|min:8|max:20',
        ]);

        try {
            $user = User::create([
                'first_name' => $request->first_name,
                'middle_name' => $request->middle_name,
                'last_name' => $request->last_name,
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'email_verified_at' => now(),
            ]);

            return redirect()->route('login')->with('success', 'Account created successfully! Please login to continue.');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Registration failed. Please try again.'])->withInput();
        }
    }

    public function checkUsername(Request $request)
    {
        $username = $request->query('username');
        
        if (!$username) {
            return response()->json(['exists' => false]);
        }

        $exists = User::where('username', $username)->exists();
        return response()->json(['exists' => $exists]);
    }

    public function checkEmailExists(Request $request)
    {
        $email = $request->query('email');
        
        if (!$email) {
            return response()->json(['exists' => false]);
        }

        $exists = User::where('email', $email)->exists();
        return response()->json(['exists' => $exists]);
    }

    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

    public function showForgotPassword() {
        return view('Security.forgot-password');
    }

    public function sendResetLink(Request $request) {
        $request->validate(['email' => 'required|email']);
        $status = Password::sendResetLink($request->only('email'));
        return $status === Password::RESET_LINK_SENT ? back()->with('status', __($status)) : back()->withErrors(['email' => __($status)]);
    }

    public function redirectToGoogle() {
        try {
            $redirectUrl = Socialite::driver('google')->redirect()->getTargetUrl();
            
            if (strpos($redirectUrl, '?') !== false) {
                $redirectUrl .= '&prompt=select_account';
            } else {
                $redirectUrl .= '?prompt=select_account';
            }
            
            return redirect()->away($redirectUrl);
            
        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Unable to connect to Google authentication.');
        }
    }

    public function handleGoogleCallback() {
        try {
            $googleUser = Socialite::driver('google')->user();

            if (!$googleUser->getEmail()) {
                throw new \Exception('Google account email not provided');
            }

            $nameParts = explode(' ', $googleUser->getName(), 2);
            $firstName = $nameParts[0] ?? $googleUser->getName();
            $lastName = $nameParts[1] ?? '';

            $baseUsername = str_replace(['@gmail.com', '@google.com'], '', $googleUser->getEmail());
            $baseUsername = preg_replace('/[^a-zA-Z0-9]/', '', $baseUsername);
            $username = $baseUsername;
            $counter = 1;
            
            while (User::where('username', $username)->exists()) {
                $username = $baseUsername . $counter;
                $counter++;
                if ($counter > 10) {
                    $username = $baseUsername . '_' . Str::random(4);
                    break;
                }
            }

            // ✅ FIXED: Added 'google_id' and 'avatar' fields
            $user = User::updateOrCreate(
                [
                    'email' => $googleUser->getEmail()
                ], 
                [
                    'google_id' => $googleUser->getId(), // ✅ CRITICAL FIX
                    'username' => $username,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'password' => Hash::make(Str::random(16)),
                    'email_verified_at' => now(),
                    'avatar' => $googleUser->getAvatar(), // ✅ Store Google profile picture
                ]
            );

            Auth::login($user, true);
            return redirect()->route('home')->with('status', 'Successfully logged in with Google!');

        } catch (\Exception $e) {
            \Log::error('Google OAuth Error: ' . $e->getMessage());
            return redirect('/login')->with('error', 'Google authentication failed: ' . $e->getMessage());
        }
    }

    public function showProfile() {
        return view('profile', ['user' => Auth::user()]);
    }
}