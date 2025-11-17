<?php

namespace App\Http\Controllers\Security;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Http\Controllers\Controller;

class ProfileController extends Controller
{
    /**
     * Show the edit profile page
     */
    public function edit()
    {
        return view('Security.editprofile');
    }

    public function update(Request $request)
    {
        $userId = Auth::id();
        if (!$userId) {
            return back()->withErrors(['error' => 'Not authenticated.']);
        }

        $currentUser = DB::table('users')->where('id', $userId)->first();
        if (!$currentUser) {
            return back()->withErrors(['error' => 'User not found.']);
        }

        // Debug logging
        Log::info('=== PROFILE UPDATE START ===');
        Log::info('User ID: ' . $userId);
        Log::info('Request data:', $request->all());
        Log::info('Current user data:', [
            'first_name' => $currentUser->first_name,
            'last_name' => $currentUser->last_name,
            'username' => $currentUser->username,
            'contact_number' => $currentUser->contact_number,
            'name_updated_at' => $currentUser->name_updated_at,
            'username_updated_at' => $currentUser->username_updated_at
        ]);

        // Check if username changed and validate cooldown
        if ($request->username !== $currentUser->username) {
            Log::info('Username change detected: ' . $currentUser->username . ' -> ' . $request->username);
            
            // Check username cooldown
            if (isset($currentUser->username_updated_at) && $currentUser->username_updated_at) {
                $nextUsernameChangeDate = Carbon::parse($currentUser->username_updated_at)->addDays(90);
                Log::info('Next username change date: ' . $nextUsernameChangeDate);
                
                if ($nextUsernameChangeDate->isFuture()) {
                    Log::info('Username cooldown active - cannot change yet');
                    return back()->withErrors([
                        'username' => 'You can only change your username once every 90 days. Next available: ' . $nextUsernameChangeDate->format('M d, Y')
                    ])->withInput();
                }
            }

            // Check if username is taken
            $usernameTaken = DB::table('users')
                ->where('username', $request->username)
                ->where('id', '!=', $userId)
                ->exists();

            if ($usernameTaken) {
                Log::info('Username already taken: ' . $request->username);
                return back()->withErrors(['username' => 'Username already taken.'])->withInput();
            }
        }

        // Check 90-day name change cooldown
        $nameChanged = ($request->first_name !== $currentUser->first_name || 
                       $request->last_name !== $currentUser->last_name ||
                       $request->middle_name !== $currentUser->middle_name);
        
        if ($nameChanged) {
            Log::info('Name change detected');
            Log::info('First name: ' . $currentUser->first_name . ' -> ' . $request->first_name);
            Log::info('Last name: ' . $currentUser->last_name . ' -> ' . $request->last_name);
            Log::info('Middle name: ' . $currentUser->middle_name . ' -> ' . $request->middle_name);
            
            if (isset($currentUser->name_updated_at) && $currentUser->name_updated_at) {
                $nextChangeDate = Carbon::parse($currentUser->name_updated_at)->addDays(90);
                Log::info('Next name change date: ' . $nextChangeDate);
                
                if ($nextChangeDate->isFuture()) {
                    Log::info('Name cooldown active - cannot change yet');
                    return back()->withErrors([
                        'first_name' => 'You can only change your name once every 90 days. Next available: ' . $nextChangeDate->format('M d, Y')
                    ])->withInput();
                }
            }
        }

        // Enhanced validation - only validate fields that exist in the form
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|min:3|max:20|regex:/^[A-Za-z\s]+$/',
            'middle_name' => 'nullable|string|max:1|regex:/^[A-Za-z]$/',
            'last_name' => 'required|string|min:3|max:20|regex:/^[A-Za-z\s]+$/',
            'username' => 'required|string|min:6|max:20|regex:/^[a-zA-Z0-9_]+$/',
            'contact_number' => 'required|string|regex:/^(09)\d{9}$/|max:11',
        ], [
            'first_name.min' => 'First name must be at least 3 characters.',
            'first_name.max' => 'First name may not be greater than 20 characters.',
            'first_name.regex' => 'First name can only contain letters and spaces.',
            'last_name.min' => 'Last name must be at least 3 characters.',
            'last_name.max' => 'Last name may not be greater than 20 characters.',
            'last_name.regex' => 'Last name can only contain letters and spaces.',
            'middle_name.max' => 'Middle name should be one character only.',
            'middle_name.regex' => 'Middle name can only contain letters.',
            'username.regex' => 'Username can only contain letters, numbers, and underscores.',
            'username.min' => 'Username must be at least 6 characters.',
            'username.max' => 'Username may not be greater than 20 characters.',
            'contact_number.regex' => 'Please enter a valid Philippine phone number (09XXXXXXXXX).',
        ]);

        if ($validator->fails()) {
            Log::info('Validation failed:', $validator->errors()->toArray());
            return back()->withErrors($validator)->withInput();
        }

        // Update data - only fields that exist in the form
        $updateData = [
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'username' => $request->username,
            'contact_number' => $request->contact_number,
            'updated_at' => now(),
        ];

        // Update username timestamp if changed
        if ($request->username !== $currentUser->username) {
            $updateData['username_updated_at'] = now();
            Log::info('Setting username_updated_at to now');
        }

        // Update name timestamp if changed
        if ($nameChanged) {
            $updateData['name_updated_at'] = now();
            Log::info('Setting name_updated_at to now');
        }

        Log::info('Final update data:', $updateData);

        try {
            $result = DB::table('users')->where('id', $userId)->update($updateData);
            Log::info('Database update result: ' . $result);
            
            if ($result) {
                Log::info('=== PROFILE UPDATE SUCCESS ===');
                return redirect()->route('my_profile')->with('success', 'Profile updated successfully!');
            } else {
                Log::error('=== PROFILE UPDATE FAILED - No rows affected ===');
                return back()->withErrors(['error' => 'Failed to update profile. No changes were made.']);
            }
        } catch (\Exception $e) {
            Log::error('Database update error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Database error: ' . $e->getMessage()]);
        }
    }

    public function updatePicture(Request $request)
    {
        $userId = Auth::id();
        if (!$userId) {
            return back()->withErrors(['error' => 'Not authenticated.']);
        }

        $validator = Validator::make($request->all(), [
            'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $currentUser = DB::table('users')->where('id', $userId)->first();

        // Delete old picture
        if ($currentUser->profile_picture) {
            Storage::disk('public')->delete($currentUser->profile_picture);
        }

        // Store new picture
        $path = $request->file('profile_picture')->store('profile-pictures', 'public');

        DB::table('users')->where('id', $userId)->update([
            'profile_picture' => $path,
            'updated_at' => now()
        ]);

        return back()->with('success', 'Profile picture updated!');
    }

    public function removePicture()
    {
        $userId = Auth::id();
        if (!$userId) {
            return back()->withErrors(['error' => 'Not authenticated.']);
        }

        $currentUser = DB::table('users')->where('id', $userId)->first();

        if ($currentUser->profile_picture) {
            Storage::disk('public')->delete($currentUser->profile_picture);
        }

        DB::table('users')->where('id', $userId)->update([
            'profile_picture' => null,
            'updated_at' => now()
        ]);

        return back()->with('success', 'Profile picture removed!');
    }

    public function checkUsername(Request $request)
    {
        $username = $request->query('username');
        $currentUsername = $request->query('current');
        $userId = Auth::id();
        
        if (!$username) {
            return response()->json(['available' => false, 'message' => 'Username is required']);
        }
        
        // Validate username format
        if (!preg_match('/^[a-zA-Z0-9_]{6,20}$/', $username)) {
            return response()->json([
                'available' => false, 
                'message' => 'Username must be 6-20 characters (letters, numbers, underscores only)'
            ]);
        }
        
        if ($username === $currentUsername) {
            return response()->json(['available' => true, 'message' => 'Current username']);
        }
        
        $exists = DB::table('users')
            ->where('username', $username)
            ->where('id', '!=', $userId)
            ->exists();
        
        return response()->json([
            'available' => !$exists, 
            'message' => $exists ? 'Username already taken' : 'Available'
        ]);
    }

    public function sendOtp(Request $request)
    {
        $userId = Auth::id();
        if (!$userId) {
            return response()->json(['success' => false, 'message' => 'Not authenticated.']);
        }

        $validator = Validator::make($request->all(), [
            'contact_number' => 'required|string|regex:/^(09)\d{9}$/'
        ], [
            'contact_number.regex' => 'Please enter a valid Philippine phone number (09XXXXXXXXX).'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ]);
        }

        // Generate random 6-digit OTP
        $otp = rand(100000, 999999);
        
        // Store OTP in database (hashed for security)
        $updateData = [
            'otp_code' => Hash::make($otp),
            'otp_expires_at' => Carbon::now()->addMinutes(10),
            'updated_at' => now()
        ];

        DB::table('users')->where('id', $userId)->update($updateData);

        // In development, log the OTP. In production, integrate with SMS service like Twilio
        Log::info("OTP for user {$userId}: {$otp}");

        return response()->json([
            'success' => true,
            'message' => 'OTP sent successfully. For development, check laravel.log file for OTP: ' . $otp
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $userId = Auth::id();
        if (!$userId) {
            return response()->json(['success' => false, 'message' => 'Not authenticated.']);
        }

        $validator = Validator::make($request->all(), [
            'otp_code' => 'required|string|size:6|regex:/^[0-9]{6}$/'
        ], [
            'otp_code.regex' => 'OTP must be a 6-digit number.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ]);
        }

        $user = DB::table('users')->where('id', $userId)->first();

        // Check if OTP columns exist and have values
        if (!isset($user->otp_code) || !$user->otp_code || !isset($user->otp_expires_at) || !$user->otp_expires_at) {
            return response()->json([
                'success' => false,
                'message' => 'No OTP request found. Please request a new OTP.'
            ]);
        }

        if (Carbon::now()->gt(Carbon::parse($user->otp_expires_at))) {
            return response()->json([
                'success' => false,
                'message' => 'OTP has expired. Please request a new OTP.'
            ]);
        }

        if (Hash::check($request->otp_code, $user->otp_code)) {
            $updateData = [
                'contact_number_verified_at' => now(),
                'otp_code' => null,
                'otp_expires_at' => null,
                'updated_at' => now()
            ];

            DB::table('users')->where('id', $userId)->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Contact number verified successfully!'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid OTP code. Please try again.'
        ]);
    }

    public function sendEmailVerification(Request $request)
    {
        $userId = Auth::id();
        if (!$userId) {
            return response()->json(['success' => false, 'message' => 'Not authenticated.']);
        }

        $user = DB::table('users')->where('id', $userId)->first();

        // Check if email is already verified
        if ($user->email_verified_at) {
            return response()->json(['success' => false, 'message' => 'Email is already verified.']);
        }

        // Check if user signed in with Google (assuming Google users have verified emails)
        if ($user->google_id) {
            return response()->json(['success' => false, 'message' => 'Google signed-in users have automatically verified emails.']);
        }

        // Generate verification token
        $token = Str::random(60);

        $updateData = [
            'email_verification_token' => $token,
            'email_verification_sent_at' => now(),
            'updated_at' => now()
        ];

        DB::table('users')->where('id', $userId)->update($updateData);

        // Send verification email
        try {
            Mail::send('emails.email-verification', ['token' => $token, 'user' => $user], function ($message) use ($user) {
                $message->to($user->email);
                $message->subject('Verify Your Email Address - Toy Collectible Platform');
            });

            return response()->json([
                'success' => true,
                'message' => 'Verification email sent! Please check your inbox.'
            ]);
        } catch (\Exception $e) {
            Log::error('Email verification send failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to send verification email. Please try again.'
            ]);
        }
    }

    public function verifyEmail($token)
    {
        $user = DB::table('users')
            ->where('email_verification_token', $token)
            ->first();

        if (!$user) {
            return redirect('/profile/edit')->with('error', 'Invalid verification token.');
        }

        // Check if token is expired (24 hours)
        if (Carbon::parse($user->email_verification_sent_at)->addHours(24)->isPast()) {
            return redirect('/profile/edit')->with('error', 'Verification token has expired. Please request a new one.');
        }

        DB::table('users')->where('id', $user->id)->update([
            'email_verified_at' => now(),
            'email_verification_token' => null,
            'email_verification_sent_at' => null,
            'updated_at' => now()
        ]);

        return redirect('/profile/edit')->with('success', 'Email verified successfully!');
    }

    // Real-time validation methods
    public function validateNameRealTime(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|min:3|max:20|regex:/^[A-Za-z\s]+$/',
            'last_name' => 'required|string|min:3|max:20|regex:/^[A-Za-z\s]+$/',
        ], [
            'first_name.min' => 'First name must be at least 3 characters.',
            'first_name.max' => 'First name may not be greater than 20 characters.',
            'first_name.regex' => 'First name can only contain letters and spaces.',
            'last_name.min' => 'Last name must be at least 3 characters.',
            'last_name.max' => 'Last name may not be greater than 20 characters.',
            'last_name.regex' => 'Last name can only contain letters and spaces.',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $firstError = $errors->first();
            
            return response()->json([
                'valid' => false,
                'message' => $firstError
            ]);
        }

        return response()->json(['valid' => true, 'message' => 'Name is valid']);
    }

    public function validateAgeRealTime(Request $request)
    {
        // Age validation no longer needed since age field is removed
        return response()->json(['valid' => true, 'message' => 'Age validation not required']);
    }

    public function validatePhoneRealTime(Request $request)
    {
        $phone = $request->input('phone');
        
        if (empty($phone)) {
            return response()->json([
                'valid' => false,
                'message' => 'Phone number is required.'
            ]);
        }
        
        $phoneRegex = '/^(09)\d{9}$/';
        
        if (!preg_match($phoneRegex, $phone)) {
            return response()->json([
                'valid' => false,
                'message' => 'Please enter a valid Philippine phone number (09XXXXXXXXX).'
            ]);
        }

        return response()->json(['valid' => true, 'message' => 'Phone number format is valid']);
    }

    public function validateFirstNameRealTime(Request $request)
    {
        $firstName = $request->input('first_name');
        
        if (empty($firstName)) {
            return response()->json([
                'valid' => false,
                'message' => 'First name is required.'
            ]);
        }
        
        if (strlen($firstName) < 3) {
            return response()->json([
                'valid' => false,
                'message' => 'First name must be at least 3 characters.'
            ]);
        }
        
        if (strlen($firstName) > 20) {
            return response()->json([
                'valid' => false,
                'message' => 'First name may not be greater than 20 characters.'
            ]);
        }
        
        if (!preg_match('/^[A-Za-z\s]+$/', $firstName)) {
            return response()->json([
                'valid' => false,
                'message' => 'First name can only contain letters and spaces.'
            ]);
        }

        return response()->json(['valid' => true, 'message' => 'First name is valid']);
    }

    public function validateLastNameRealTime(Request $request)
    {
        $lastName = $request->input('last_name');
        
        if (empty($lastName)) {
            return response()->json([
                'valid' => false,
                'message' => 'Last name is required.'
            ]);
        }
        
        if (strlen($lastName) < 3) {
            return response()->json([
                'valid' => false,
                'message' => 'Last name must be at least 3 characters.'
            ]);
        }
        
        if (strlen($lastName) > 20) {
            return response()->json([
                'valid' => false,
                'message' => 'Last name may not be greater than 20 characters.'
            ]);
        }
        
        if (!preg_match('/^[A-Za-z\s]+$/', $lastName)) {
            return response()->json([
                'valid' => false,
                'message' => 'Last name can only contain letters and spaces.'
            ]);
        }

        return response()->json(['valid' => true, 'message' => 'Last name is valid']);
    }

    public function validateUsernameFormatRealTime(Request $request)
    {
        $username = $request->input('username');
        
        if (empty($username)) {
            return response()->json([
                'valid' => false,
                'message' => 'Username is required.'
            ]);
        }
        
        if (strlen($username) < 6) {
            return response()->json([
                'valid' => false,
                'message' => 'Username must be at least 6 characters.'
            ]);
        }
        
        if (strlen($username) > 20) {
            return response()->json([
                'valid' => false,
                'message' => 'Username may not be greater than 20 characters.'
            ]);
        }
        
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
            return response()->json([
                'valid' => false,
                'message' => 'Username can only contain letters, numbers, and underscores.'
            ]);
        }

        return response()->json(['valid' => true, 'message' => 'Username format is valid']);
    }
}