<?php

namespace App\Http\Controllers\Security;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Http\Controllers\Controller;

class AddressController extends Controller
{
    /**
     * Show the address form with multiple addresses
     */
    public function create()
    {
        $user = Auth::user();
        $addresses = [];
        
        // Get both home and work addresses if they exist
        $homeAddress = $this->getHomeAddressAsArray($user);
        $workAddress = $this->getWorkAddressAsArray($user);
        
        if ($homeAddress) {
            $addresses[] = $homeAddress;
        }
        if ($workAddress) {
            $addresses[] = $workAddress;
        }
        
        // Get the address type from request or default to home
        $addressType = request('type', 'home');
        
        // Get current address data for the form based on type
        $currentAddress = null;
        if ($addressType === 'home' && $homeAddress) {
            $currentAddress = $homeAddress;
        } elseif ($addressType === 'work' && $workAddress) {
            $currentAddress = $workAddress;
        }
        
        return view('Security.address_form', compact('addresses', 'addressType', 'currentAddress'));
    }

    /**
     * Convert current user home address fields to array format
     */
    private function getHomeAddressAsArray($user)
    {
        if (!$user->home_address) {
            return null;
        }

        return [
            'id' => 'home',
            'label' => 'Home',
            'region' => $user->address_region,
            'city' => $user->address_city,
            'district' => $user->address_district,
            'street' => $user->address_street,
            'unit' => $user->address_unit,
            'category' => 'home',
            'is_default_shipping' => (bool)$user->is_default_shipping,
            'summary' => $user->home_address,
            'created_at' => $user->updated_at ? $user->updated_at->toDateTimeString() : now()->toDateTimeString(),
            'updated_at' => $user->updated_at ? $user->updated_at->toDateTimeString() : now()->toDateTimeString(),
        ];
    }

    /**
     * Convert current user work address fields to array format
     */
    private function getWorkAddressAsArray($user)
    {
        if (!$user->work_address) {
            return null;
        }

        // Parse work address from the stored string
        $workAddressParts = $this->parseAddressString($user->work_address);
        
        return [
            'id' => 'work',
            'label' => 'Work',
            'region' => $workAddressParts['region'] ?? $user->address_region,
            'city' => $workAddressParts['city'] ?? $user->address_city,
            'district' => $workAddressParts['district'] ?? $user->address_district,
            'street' => $workAddressParts['street'] ?? '',
            'unit' => $workAddressParts['unit'] ?? $user->address_unit,
            'category' => 'work',
            'is_default_shipping' => false, // Work address typically not default shipping
            'summary' => $user->work_address,
            'created_at' => $user->updated_at ? $user->updated_at->toDateTimeString() : now()->toDateTimeString(),
            'updated_at' => $user->updated_at ? $user->updated_at->toDateTimeString() : now()->toDateTimeString(),
        ];
    }

    /**
     * Parse address string into components
     */
    private function parseAddressString($addressString)
    {
        if (!$addressString) {
            return [];
        }

        $parts = explode(', ', $addressString);
        $result = [];

        // Simple parsing - adjust based on your address format
        if (count($parts) >= 5) {
            $result['street'] = $parts[0];
            $result['unit'] = $parts[1] ?? '';
            $result['district'] = $parts[2] ?? '';
            $result['city'] = $parts[3] ?? '';
            $result['region'] = $parts[4] ?? '';
        } elseif (count($parts) >= 4) {
            $result['street'] = $parts[0];
            $result['district'] = $parts[1] ?? '';
            $result['city'] = $parts[2] ?? '';
            $result['region'] = $parts[3] ?? '';
        } elseif (count($parts) >= 3) {
            $result['street'] = $parts[0];
            $result['city'] = $parts[1] ?? '';
            $result['region'] = $parts[2] ?? '';
        }

        return $result;
    }

    /**
     * Store a new address (using individual columns)
     */
    public function store(Request $request)
    {
        $userId = Auth::id();
        if (!$userId) {
            return redirect()->route('login')->withErrors(['error' => 'Not authenticated.']);
        }

        $validator = Validator::make($request->all(), [
            'address_region' => 'required|string|max:255',
            'address_city' => 'required|string|max:255',
            'address_district' => 'required|string|max:255',
            'address_street' => 'required|string|max:500',
            'address_unit' => 'nullable|string|max:255',
            'address_category' => 'required|in:home,work',
            'address_label' => 'nullable|string|max:100',
            'is_default_shipping' => 'boolean',
        ], [
            'address_region.required' => 'Region is required.',
            'address_city.required' => 'City is required.',
            'address_district.required' => 'District is required.',
            'address_street.required' => 'Street/Building name is required.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Build address summary
        $summary = $request->address_street;
        if ($request->address_unit) {
            $summary .= ', ' . $request->address_unit;
        }
        $summary .= ', ' . $request->address_district . ', ' . $request->address_city . ', ' . $request->address_region;

        // Handle checkbox
        $isDefaultShipping = $request->boolean('is_default_shipping');

        // Update user data based on address category
        $updateData = [
            'updated_at' => now(),
        ];

        if ($request->address_category === 'home') {
            // Store home address in main fields and home_address column
            $updateData = array_merge($updateData, [
                'address_region' => $request->address_region,
                'address_city' => $request->address_city,
                'address_district' => $request->address_district,
                'address_street' => $request->address_street,
                'address_unit' => $request->address_unit,
                'address_category' => 'home',
                'is_default_shipping' => $isDefaultShipping,
                'home_address' => $summary,
            ]);
        } elseif ($request->address_category === 'work') {
            // Store work address only in work_address column
            $updateData['work_address'] = $summary;
            
            // If setting work as default, update the main fields too
            if ($isDefaultShipping) {
                $updateData = array_merge($updateData, [
                    'address_region' => $request->address_region,
                    'address_city' => $request->address_city,
                    'address_district' => $request->address_district,
                    'address_street' => $request->address_street,
                    'address_unit' => $request->address_unit,
                    'address_category' => 'work',
                    'is_default_shipping' => true,
                ]);
            }
        }

        try {
            $result = DB::table('users')->where('id', $userId)->update($updateData);
            
            if ($result) {
                $message = ucfirst($request->address_category) . ' address saved successfully!';
                if ($isDefaultShipping) {
                    $message .= ' This address is now set as your default shipping address.';
                }
                return redirect()->route('address.create', ['type' => $request->address_category])->with('success', $message);
            } else {
                return back()->withErrors(['error' => 'Failed to save address. No changes were made.'])->withInput();
            }
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Database error: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Switch between address types
     */
    public function switchType($type)
    {
        if (!in_array($type, ['home', 'work'])) {
            return redirect()->route('address.create')->withErrors(['error' => 'Invalid address type.']);
        }

        return redirect()->route('address.create', ['type' => $type]);
    }

    /**
     * Set default address (for both dropdown and button actions)
     */
    public function setDefault($addressId)
    {
        $userId = Auth::id();
        if (!$userId) {
            return redirect()->route('login');
        }

        $user = DB::table('users')->where('id', $userId)->first();
        
        if (!$user) {
            return back()->withErrors(['error' => 'User not found.']);
        }

        if ($addressId === 'home' && $user->home_address) {
            // Set home address as default
            $homeAddressParts = $this->parseAddressString($user->home_address);
            
            DB::table('users')->where('id', $userId)->update([
                'address_region' => $homeAddressParts['region'] ?? $user->address_region,
                'address_city' => $homeAddressParts['city'] ?? $user->address_city,
                'address_district' => $homeAddressParts['district'] ?? $user->address_district,
                'address_street' => $homeAddressParts['street'] ?? $user->address_street,
                'address_unit' => $homeAddressParts['unit'] ?? $user->address_unit,
                'address_category' => 'home',
                'is_default_shipping' => true,
                'updated_at' => now(),
            ]);

            return back()->with('success', 'Home address set as default shipping address!');

        } elseif ($addressId === 'work' && $user->work_address) {
            // Set work address as default
            $workAddressParts = $this->parseAddressString($user->work_address);
            
            DB::table('users')->where('id', $userId)->update([
                'address_region' => $workAddressParts['region'] ?? $user->address_region,
                'address_city' => $workAddressParts['city'] ?? $user->address_city,
                'address_district' => $workAddressParts['district'] ?? $user->address_district,
                'address_street' => $workAddressParts['street'] ?? '',
                'address_unit' => $workAddressParts['unit'] ?? $user->address_unit,
                'address_category' => 'work',
                'is_default_shipping' => true,
                'updated_at' => now(),
            ]);

            return back()->with('success', 'Work address set as default shipping address!');
        }

        return back()->withErrors(['error' => 'Address not found or cannot be set as default.']);
    }

    /**
     * Delete address
     */
    public function destroy($addressId)
    {
        $userId = Auth::id();
        if (!$userId) {
            return redirect()->route('login');
        }

        $updateData = [
            'updated_at' => now(),
        ];

        if ($addressId === 'home') {
            $updateData['home_address'] = null;
            // Also clear main fields if home was the current address
            $user = DB::table('users')->where('id', $userId)->first();
            if ($user && $user->address_category === 'home') {
                $updateData = array_merge($updateData, [
                    'address_region' => null,
                    'address_city' => null,
                    'address_district' => null,
                    'address_street' => null,
                    'address_unit' => null,
                    'is_default_shipping' => false,
                ]);
            }
        } elseif ($addressId === 'work') {
            $updateData['work_address'] = null;
            // Also clear main fields if work was the current address
            $user = DB::table('users')->where('id', $userId)->first();
            if ($user && $user->address_category === 'work') {
                $updateData = array_merge($updateData, [
                    'address_region' => null,
                    'address_city' => null,
                    'address_district' => null,
                    'address_street' => null,
                    'address_unit' => null,
                    'is_default_shipping' => false,
                ]);
            }
        } else {
            return back()->withErrors(['error' => 'Invalid address type.']);
        }

        DB::table('users')->where('id', $userId)->update($updateData);
        
        return back()->with('success', 'Address deleted successfully!');
    }

    /**
     * Debug method to check database structure
     */
    public function debugDatabase()
    {
        try {
            $userId = Auth::id();
            $user = DB::table('users')->where('id', $userId)->first();
            
            if (!$user) {
                return response()->json(['error' => 'User not found'], 404);
            }

            // Check address-related columns
            $columns = DB::select("
                SELECT column_name, data_type, column_default, is_nullable 
                FROM information_schema.columns 
                WHERE table_name = 'users' 
                AND column_name IN ('address_region', 'address_city', 'address_district', 'address_street', 'address_unit', 'address_category', 'is_default_shipping', 'home_address', 'work_address')
            ");

            return response()->json([
                'user_data' => [
                    'id' => $user->id,
                    'main_address_fields' => [
                        'address_region' => $user->address_region,
                        'address_city' => $user->address_city,
                        'address_district' => $user->address_district,
                        'address_street' => $user->address_street,
                        'address_unit' => $user->address_unit,
                        'address_category' => $user->address_category,
                        'is_default_shipping' => $user->is_default_shipping,
                        'home_address' => $user->home_address,
                        'work_address' => $user->work_address
                    ],
                    'has_home_address' => !empty($user->home_address),
                    'has_work_address' => !empty($user->work_address),
                    'home_address_summary' => $user->home_address,
                    'work_address_summary' => $user->work_address
                ],
                'database_columns' => $columns,
                'all_user_columns' => array_keys((array)$user)
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}