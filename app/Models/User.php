<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Carbon\Carbon;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'username',
        'email',
        'password',
        'role',
        'is_active',
        'last_login_at',
        'home_address',
        'work_address',
        'contact_number',
        'profile_picture',
        'valid_id_path',
        'facial_recognition_path',
        'username_updated_at',
        'email_verified_at',
        'name_updated_at',
        'otp_code',
        'otp_expires_at',
        'contact_number_verified_at',
        'address_region',
        'address_city',
        'address_district',
        'address_street',
        'address_unit',
        'address_category',
        'is_default_shipping',
        'email_verification_token',
        'email_verification_sent_at',
        'google_id', // ✅ Added for Google OAuth
        'avatar',    // ✅ Added for Google profile pictures
        'diamond_balance',
        'addresses',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'otp_code',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'username_updated_at' => 'datetime',
            'name_updated_at' => 'datetime',
            'otp_expires_at' => 'datetime',
            'contact_number_verified_at' => 'datetime',
            'email_verification_sent_at' => 'datetime',
            'is_default_shipping' => 'boolean',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
            'diamond_balance' => 'integer',
            'addresses' => 'array',
        ];
    }

    /**
     * ADMIN ROLE METHODS
     */

    /**
     * Check if user is super admin
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is customer
     */
    public function isCustomer(): bool
    {
        return $this->role === 'customer' || empty($this->role);
    }

    /**
     * Check if user can login
     */
    public function canLogin(): bool
    {
        return $this->is_active;
    }

    /**
     * Record login time
     */
    public function recordLogin(): void
    {
        $this->update([
            'last_login_at' => now(),
        ]);
    }

    /**
     * Get role badge class
     */
    public function getRoleBadgeAttribute(): string
    {
        return match($this->role) {
            'super_admin' => 'bg-danger',
            'admin' => 'bg-success',
            'customer' => 'bg-primary',
            default => 'bg-secondary'
        };
    }

    /**
     * Get role display name
     */
    public function getRoleDisplayAttribute(): string
    {
        return match($this->role) {
            'super_admin' => 'Super Admin',
            'admin' => 'Admin',
            'customer' => 'Customer',
            default => 'Customer'
        };
    }

    /**
     * Scope for super admins
     */
    public function scopeSuperAdmins($query)
    {
        return $query->where('role', 'super_admin');
    }

    /**
     * Scope for admins
     */
    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }

    /**
     * Scope for customers
     */
    public function scopeCustomers($query)
    {
        return $query->where('role', 'customer')->orWhereNull('role');
    }

    /**
     * Scope for active users
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get the user's full name
     */
    public function getFullNameAttribute(): string
    {
        $names = array_filter([
            $this->first_name,
            $this->middle_name,
            $this->last_name
        ]);
        
        return trim(implode(' ', $names));
    }

    /**
     * Get the user's full name (alternative method)
     */
    public function getFullName(): string
    {
        return $this->full_name;
    }

    /**
     * Check if user is fully verified
     */
    public function isFullyVerified(): bool
    {
        return $this->home_address &&
               $this->contact_number &&
               $this->valid_id_path &&
               $this->facial_recognition_path &&
               $this->email_verified_at;
    }

    /**
     * Check if username can be changed (60-day cooldown)
     */
    public function canChangeUsername(): bool
    {
        if (!$this->username_updated_at) {
            return true;
        }

        return $this->username_updated_at->diffInDays(now()) >= 60;
    }

    /**
     * Check if name can be changed (90-day cooldown)
     */
    public function canChangeName(): bool
    {
        if (!$this->name_updated_at) {
            return true;
        }

        return $this->name_updated_at->diffInDays(now()) >= 90;
    }

    /**
     * Get the next available date for name change
     */
    public function getNextNameChangeDate(): ?Carbon
    {
        if (!$this->name_updated_at) {
            return null;
        }

        return $this->name_updated_at->copy()->addDays(90);
    }

    /**
     * Get the next available date for username change
     */
    public function getNextUsernameChangeDate(): ?Carbon
    {
        if (!$this->username_updated_at) {
            return null;
        }

        return $this->username_updated_at->copy()->addDays(60);
    }

    /**
     * Check if contact number is verified
     */
    public function isContactNumberVerified(): bool
    {
        return !is_null($this->contact_number_verified_at);
    }

    /**
     * Check if OTP is valid and not expired
     */
    public function isOtpValid(string $otp): bool
    {
        if (!$this->otp_code || !$this->otp_expires_at) {
            return false;
        }

        if (now()->gt($this->otp_expires_at)) {
            return false;
        }

        return \Illuminate\Support\Facades\Hash::check($otp, $this->otp_code);
    }

    /**
     * Clear OTP data after successful verification
     */
    public function clearOtp(): void
    {
        $this->update([
            'otp_code' => null,
            'otp_expires_at' => null,
        ]);
    }

    /**
     * Get the remaining OTP validity time in minutes
     */
    public function getOtpRemainingMinutes(): int
    {
        if (!$this->otp_expires_at) {
            return 0;
        }

        return max(0, now()->diffInMinutes($this->otp_expires_at, false));
    }

    /**
     * Scope a query to only include users with verified contact numbers.
     */
    public function scopeContactVerified($query)
    {
        return $query->whereNotNull('contact_number_verified_at');
    }

    /**
     * Scope a query to only include users with verified emails.
     */
    public function scopeEmailVerified($query)
    {
        return $query->whereNotNull('email_verified_at');
    }

    /**
     * Check if user has completed basic profile
     */
    public function hasBasicProfile(): bool
    {
        return $this->first_name && 
               $this->last_name && 
               $this->email && 
               $this->username;
    }

    /**
     * Check if user has completed extended profile
     */
    public function hasExtendedProfile(): bool
    {
        return $this->hasBasicProfile() && 
               $this->home_address && 
               $this->contact_number;
    }

    /**
     * Get user's initials for avatar placeholder
     */
    public function getInitialsAttribute(): string
    {
        $initials = '';
        
        if ($this->first_name) {
            $initials .= strtoupper(substr($this->first_name, 0, 1));
        }
        
        if ($this->last_name) {
            $initials .= strtoupper(substr($this->last_name, 0, 1));
        }
        
        return $initials ?: 'U';
    }

    /**
     * Get the profile picture URL or return initials
     */
    public function getProfilePictureUrlAttribute(): string
    {
        // ✅ FIXED: Check both avatar (Google) and profile_picture fields
        if ($this->avatar) {
            return $this->avatar; // Google profile picture URL
        }
        
        if ($this->profile_picture) {
            return asset('storage/' . $this->profile_picture);
        }
        
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->initials) . '&color=7F9CF5&background=EBF4FF';
    }

    /**
     * Diamond Balance Methods
     */

    /**
     * Get the user's current diamond balance
     */
    public function getDiamondBalance(): int
    {
        return $this->diamond_balance ?? 0;
    }

    /**
     * Check if user has sufficient diamonds
     */
    public function hasSufficientDiamonds(int $amount): bool
    {
        return $this->getDiamondBalance() >= $amount;
    }

    /**
     * Add diamonds to user's balance
     */
    public function addDiamonds(int $amount): bool
    {
        $this->diamond_balance = $this->getDiamondBalance() + $amount;
        return $this->save();
    }

    /**
     * Subtract diamonds from user's balance
     */
    public function subtractDiamonds(int $amount): bool
    {
        if (!$this->hasSufficientDiamonds($amount)) {
            return false;
        }

        $this->diamond_balance = $this->getDiamondBalance() - $amount;
        return $this->save();
    }

    /**
     * Get formatted diamond balance with comma separator
     */
    public function getFormattedDiamondBalance(): string
    {
        return number_format($this->getDiamondBalance());
    }

    /**
     * Scope a query to only include users with minimum diamond balance
     */
    public function scopeWithMinDiamonds($query, int $minBalance)
    {
        return $query->where('diamond_balance', '>=', $minBalance);
    }

    /**
     * Scope a query to order users by diamond balance
     */
    public function scopeOrderByDiamonds($query, string $direction = 'desc')
    {
        return $query->orderBy('diamond_balance', $direction);
    }

    /**
     * Get diamond transactions for the user
     */
    public function diamondTransactions()
    {
        return \Illuminate\Support\Facades\DB::table('diamond_transactions')
            ->where('customer_id', $this->id)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get diamond purchase history for the user
     */
    public function diamondPurchases()
    {
        return \Illuminate\Support\Facades\DB::table('diamond_purchases')
            ->where('customer_id', $this->id)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get recent diamond transactions (last 10)
     */
    public function getRecentDiamondTransactions($limit = 10)
    {
        return $this->diamondTransactions()->take($limit);
    }

    /**
     * Get total diamonds purchased by user
     */
    public function getTotalDiamondsPurchased(): int
    {
        return \Illuminate\Support\Facades\DB::table('diamond_purchases')
            ->where('customer_id', $this->id)
            ->where('payment_status', 'completed')
            ->sum('diamond_amount');
    }

    /**
     * Get total amount spent on diamonds
     */
    public function getTotalAmountSpentOnDiamonds(): float
    {
        return \Illuminate\Support\Facades\DB::table('diamond_purchases')
            ->where('customer_id', $this->id)
            ->where('payment_status', 'completed')
            ->sum('amount_paid');
    }

    /**
     * Check if user can afford a purchase with diamonds
     */
    public function canAffordWithDiamonds(float $priceInPesos): bool
    {
        // Assuming 1 Peso = 1 Diamond
        $diamondsRequired = (int) ceil($priceInPesos);
        return $this->hasSufficientDiamonds($diamondsRequired);
    }

    /**
     * Get diamond balance with currency symbol
     */
    public function getDiamondBalanceWithSymbol(): string
    {
        return $this->getFormattedDiamondBalance() . ' 💎';
    }

    /**
     * Reset diamond balance to zero (for admin purposes)
     */
    public function resetDiamondBalance(): bool
    {
        $this->diamond_balance = 0;
        return $this->save();
    }

    /**
     * Get all trades uploaded by this user
     */
    public function trades()
    {
        return $this->hasMany(Trade::class);
    }

    /**
     * Get active trades uploaded by this user
     */
    public function activeTrades()
    {
        return $this->hasMany(Trade::class)->where('status', 'active');
    }

    /**
     * Get total number of trades uploaded by this user
     */
    public function getTotalTradesCount(): int
    {
        return $this->trades()->count();
    }

    /**
     * Get total active trades by this user
     */
    public function getActiveTradesCount(): int
    {
        return $this->activeTrades()->count();
    }

    /**
     * Get exchange proposals sent by this user
     */
    public function sentProposals()
    {
        return $this->hasMany(ExchangeProposal::class, 'sender_id');
    }

    /**
     * Get exchange proposals received by this user
     */
    public function receivedProposals()
    {
        return $this->hasMany(ExchangeProposal::class, 'receiver_id');
    }

    /**
     * Get pending proposals received
     */
    public function pendingReceivedProposals()
    {
        return $this->receivedProposals()->where('status', 'pending');
    }

    /**
     * Get count of pending received proposals
     */
    public function getPendingProposalsCountAttribute(): int
    {
        return $this->pendingReceivedProposals()->count();
    }

    /**
     * MULTIPLE ADDRESS MANAGEMENT METHODS
     */

    /**
     * Get all addresses as array
     */
    public function getAddressesAttribute(): array
    {
        if (!$this->attributes['addresses']) {
            return [];
        }
        return json_decode($this->attributes['addresses'], true) ?: [];
    }

    /**
     * Set addresses as JSON
     */
    public function setAddressesAttribute(array $value): void
    {
        $this->attributes['addresses'] = json_encode($value);
    }

    /**
     * Get default shipping address
     */
    public function getDefaultShippingAddress(): ?array
    {
        $addresses = $this->addresses;
        foreach ($addresses as $address) {
            if ($address['is_default_shipping'] ?? false) {
                return $address;
            }
        }
        return count($addresses) > 0 ? $addresses[0] : null;
    }

    /**
     * Check if user has any addresses
     */
    public function hasAddresses(): bool
    {
        return count($this->addresses) > 0;
    }

    /**
     * Get addresses count
     */
    public function getAddressesCount(): int
    {
        return count($this->addresses);
    }

    /**
     * Add new address to addresses array
     */
    public function addAddress(array $addressData): bool
    {
        $addresses = $this->addresses;
        
        // Generate unique ID for new address
        $addressData['id'] = uniqid();
        $addressData['created_at'] = now()->toDateTimeString();
        $addressData['updated_at'] = now()->toDateTimeString();
        
        // If this is set as default, remove default from others
        if ($addressData['is_default_shipping'] ?? false) {
            foreach ($addresses as &$address) {
                $address['is_default_shipping'] = false;
            }
        }
        
        $addresses[] = $addressData;
        $this->addresses = $addresses;
        
        return $this->save();
    }

    /**
     * Update specific address
     */
    public function updateAddress(string $addressId, array $addressData): bool
    {
        $addresses = $this->addresses;
        $updated = false;
        
        foreach ($addresses as &$address) {
            if ($address['id'] === $addressId) {
                $address = array_merge($address, $addressData);
                $address['updated_at'] = now()->toDateTimeString();
                $updated = true;
                break;
            }
        }
        
        if ($updated) {
            $this->addresses = $addresses;
            return $this->save();
        }
        
        return false;
    }

    /**
     * Delete specific address
     */
    public function deleteAddress(string $addressId): bool
    {
        $addresses = $this->addresses;
        $newAddresses = [];
        $deleted = false;
        
        foreach ($addresses as $address) {
            if ($address['id'] !== $addressId) {
                $newAddresses[] = $address;
            } else {
                $deleted = true;
            }
        }
        
        if ($deleted) {
            $this->addresses = $newAddresses;
            return $this->save();
        }
        
        return false;
    }

    /**
     * Set default shipping address
     */
    public function setDefaultShippingAddress(string $addressId): bool
    {
        $addresses = $this->addresses;
        $updated = false;
        
        foreach ($addresses as &$address) {
            if ($address['id'] === $addressId) {
                $address['is_default_shipping'] = true;
                $updated = true;
            } else {
                $address['is_default_shipping'] = false;
            }
        }
        
        if ($updated) {
            $this->addresses = $addresses;
            return $this->save();
        }
        
        return false;
    }

    /**
     * Get address by ID
     */
    public function getAddressById(string $addressId): ?array
    {
        $addresses = $this->addresses;
        foreach ($addresses as $address) {
            if ($address['id'] === $addressId) {
                return $address;
            }
        }
        return null;
    }

    /**
     * SINGLE ADDRESS COMPATIBILITY METHODS (for backward compatibility)
     */

    /**
     * Get current address as array (for single address compatibility)
     */
    public function getCurrentAddressAttribute(): array
    {
        $defaultAddress = $this->getDefaultShippingAddress();
        if ($defaultAddress) {
            return [
                'region' => $defaultAddress['region'],
                'city' => $defaultAddress['city'],
                'district' => $defaultAddress['district'],
                'street' => $defaultAddress['street'],
                'unit' => $defaultAddress['unit'],
                'category' => $defaultAddress['category'],
                'is_default_shipping' => $defaultAddress['is_default_shipping'],
            ];
        }

        return [
            'region' => $this->address_region,
            'city' => $this->address_city,
            'district' => $this->address_district,
            'street' => $this->address_street,
            'unit' => $this->address_unit,
            'category' => $this->address_category,
            'is_default_shipping' => $this->is_default_shipping,
        ];
    }

    /**
     * Get address summary (for single address compatibility)
     */
    public function getAddressSummaryAttribute(): string
    {
        $defaultAddress = $this->getDefaultShippingAddress();
        if ($defaultAddress) {
            return $defaultAddress['summary'] ?? $this->buildAddressSummary($defaultAddress);
        }

        $parts = array_filter([
            $this->address_street,
            $this->address_unit,
            $this->address_district,
            $this->address_city,
            $this->address_region
        ]);

        return implode(', ', $parts) ?: 'No address set';
    }

    /**
     * Build address summary from address data
     */
    private function buildAddressSummary(array $address): string
    {
        $parts = array_filter([
            $address['street'],
            $address['unit'],
            $address['district'],
            $address['city'],
            $address['region']
        ]);

        return implode(', ', $parts);
    }

    /**
     * Check if user has address set (for single address compatibility)
     */
    public function hasAddress(): bool
    {
        return $this->hasAddresses() || (!empty($this->address_region) && !empty($this->address_city) && !empty($this->address_street));
    }

    /**
     * Get address for display (for single address compatibility)
     */
    public function getDisplayAddress(): string
    {
        $defaultAddress = $this->getDefaultShippingAddress();
        if ($defaultAddress) {
            return $this->buildAddressSummary($defaultAddress);
        }

        if (!$this->hasAddress()) {
            return 'Address not set';
        }

        $addressParts = [];

        if ($this->address_street) {
            $addressParts[] = $this->address_street;
        }
        if ($this->address_unit) {
            $addressParts[] = $this->address_unit;
        }
        if ($this->address_district) {
            $addressParts[] = $this->address_district;
        }
        if ($this->address_city) {
            $addressParts[] = $this->address_city;
        }
        if ($this->address_region) {
            $addressParts[] = $this->address_region;
        }

        return implode(', ', $addressParts);
    }

    /**
     * Validate address completeness for checkout
     */
    public function isValidForCheckout(): bool
    {
        return $this->hasAddress() && 
               $this->contact_number && 
               $this->email_verified_at;
    }

    /**
     * Get address validation errors
     */
    public function getAddressValidationErrors(): array
    {
        $errors = [];

        if (!$this->hasAddress()) {
            $errors[] = 'Shipping address is required';
        }
        if (!$this->contact_number) {
            $errors[] = 'Contact number is required';
        }

        return $errors;
    }
}