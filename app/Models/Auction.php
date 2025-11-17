<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class Auction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_name',
        'brand',
        'condition',
        'category',
        'description',
        'product_img',
        'start_time',
        'end_time',
        'starting_price',
        'buyout_bid',
        'current_bid',
        'winner_id',
        'status',
        'payout_status',
        'payout_amount',
        'payout_approved_at',
        'payout_approved_by',
        'escrow_held_at',
        'escrow_released_at',
        'item_received_at',
        'seller_reply_deadline',
        'chat_created_at',
        'delivery_method',
        'delivery_cost',
        // Security verification fields
        'owner_proof',
        'market_value_proof',
        'minimum_market_value',
        'terms_accepted',
        'verified_at',
        'verified_by',
        'reference_links',
    ];

    protected $casts = [
        'start_time'    => 'datetime',
        'end_time'      => 'datetime',
        'starting_price'=> 'float',
        'buyout_bid'    => 'float',
        'current_bid'   => 'float',
        'payout_amount' => 'float',
        'minimum_market_value' => 'float',
        'delivery_cost' => 'float',
        'terms_accepted' => 'boolean',
        'verified_at'   => 'datetime',
        'payout_approved_at' => 'datetime',
        'escrow_held_at' => 'datetime',
        'escrow_released_at' => 'datetime',
        'item_received_at' => 'datetime',
        'seller_reply_deadline' => 'datetime',
        'chat_created_at' => 'datetime',
        'created_at'    => 'datetime',
        'updated_at'    => 'datetime',
        'product_img'   => 'array', // Cast JSON to array
    ];

    protected $appends = [
        'formatted_current_bid',
        'formatted_buyout_bid',
        'time_remaining',
        'bids_count',
        'delivery_method_text',
        'is_verified',
        'payout_status_text',
        'is_seller_reply_overdue',
        'is_item_received',
        'escrow_days_held',
        'product_images',
        'is_active',
        'has_ended',
        'first_image_url',
        'has_images',
        'image_urls',
        'image_gallery',
        'has_multiple_images', // NEW: Add this to appends
        'image_count', // NEW: Add this to appends
    ];

    /**
     * Relationship: Auction winner (user)
     */
    public function winner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'winner_id');
    }

    /**
     * Relationship: All bids for this auction
     */
    public function bids(): HasMany
    {
        return $this->hasMany(Bid::class);
    }

    /**
     * Relationship to user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relationship to verifier (admin)
     */
    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Relationship to payout approver (admin)
     */
    public function payoutApprover(): BelongsTo
    {
        return $this->belongsTo(User::class, 'payout_approved_by');
    }

    /**
     * Get simple array of all image URLs (for easy access in views)
     */
    public function getImageUrlsAttribute(): array
    {
        $images = $this->product_img ?? [];
        $urls = [];
        
        // Handle string format (legacy single image)
        if (is_string($images) && !empty($images)) {
            $urls[] = $this->getImageUrl($images);
            return $urls;
        }
        
        // Handle array format (multiple images)
        if (is_array($images) && !empty($images)) {
            foreach ($images as $image) {
                if ($image && is_string($image)) {
                    $urls[] = $this->getImageUrl($image);
                }
            }
        }
        
        // If no images, return placeholder
        if (empty($urls)) {
            $urls[] = $this->getProductPlaceholder();
        }
        
        return $urls;
    }

    /**
     * Get structured image gallery data
     */
    public function getImageGalleryAttribute(): array
    {
        $images = $this->product_img ?? [];
        $gallery = [];
        
        // Handle string format (legacy single image)
        if (is_string($images) && !empty($images)) {
            $gallery[] = [
                'path' => $images,
                'url' => $this->getImageUrl($images),
                'exists' => $this->checkImageExists($images),
                'thumbnail_url' => $this->getImageUrl($images),
                'is_primary' => true,
                'index' => 0
            ];
            return $gallery;
        }
        
        // Handle array format (multiple images)
        if (is_array($images) && !empty($images)) {
            foreach ($images as $index => $image) {
                if ($image && is_string($image)) {
                    $gallery[] = [
                        'path' => $image,
                        'url' => $this->getImageUrl($image),
                        'exists' => $this->checkImageExists($image),
                        'thumbnail_url' => $this->getImageUrl($image),
                        'is_primary' => $index === 0,
                        'index' => $index
                    ];
                }
            }
        }
        
        // If no images, return placeholder
        if (empty($gallery)) {
            $gallery[] = [
                'path' => null,
                'url' => $this->getProductPlaceholder(),
                'exists' => false,
                'thumbnail_url' => $this->getProductPlaceholder(),
                'is_primary' => true,
                'index' => 0
            ];
        }
        
        return $gallery;
    }

    /**
     * Get first product image URL with proper path handling
     */
    public function getFirstImageUrlAttribute(): string
    {
        $images = $this->product_img ?? [];
        
        // Handle string format (JSON or single path)
        if (is_string($images) && !empty($images)) {
            return $this->getImageUrl($images);
        }
        
        // Handle array format
        if (is_array($images) && !empty($images) && isset($images[0])) {
            return $this->getImageUrl($images[0]);
        }
        
        return $this->getProductPlaceholder();
    }

    /**
     * Check if auction has any valid images
     */
    public function getHasImagesAttribute(): bool
    {
        $images = $this->product_img ?? [];
        
        // Handle string format
        if (is_string($images) && !empty($images)) {
            return $this->checkImageExists($images);
        }
        
        // Handle array format
        if (is_array($images) && !empty($images)) {
            foreach ($images as $image) {
                if ($image && is_string($image) && $this->checkImageExists($image)) {
                    return true;
                }
            }
        }
        
        return false;
    }

    /**
     * NEW: Check if auction has multiple images
     */
    public function getHasMultipleImagesAttribute(): bool
    {
        return $this->getImageCount() > 1;
    }

    /**
     * NEW: Get count of images
     */
    public function getImageCountAttribute(): int
    {
        $images = $this->product_img ?? [];
        
        if (is_string($images) && !empty($images)) {
            return 1;
        }
        
        if (is_array($images)) {
            return count($images);
        }
        
        return 0;
    }

    /**
     * Helper method to get image URL with multiple storage location checks
     */
    private function getImageUrl(string $imagePath): string
    {
        if (empty($imagePath)) {
            return $this->getProductPlaceholder();
        }
        
        // Remove any leading slashes or problematic characters
        $imagePath = ltrim($imagePath, '/\\');
        
        // Check multiple possible storage locations
        if (Storage::disk('public')->exists($imagePath)) {
            return asset('storage/' . $imagePath);
        }
        
        if (Storage::disk('public')->exists('auctions/' . $imagePath)) {
            return asset('storage/auctions/' . $imagePath);
        }
        
        if (Storage::disk('public')->exists('models/' . $imagePath)) {
            return asset('storage/models/' . $imagePath);
        }
        
        if (file_exists(public_path('storage/' . $imagePath))) {
            return asset('storage/' . $imagePath);
        }
        
        // Final fallback - try direct URL
        return asset('storage/' . $imagePath);
    }

    /**
     * Helper method to check if image exists
     */
    private function checkImageExists(string $imagePath): bool
    {
        if (empty($imagePath)) {
            return false;
        }
        
        $imagePath = ltrim($imagePath, '/\\');
        
        return Storage::disk('public')->exists($imagePath) ||
               Storage::disk('public')->exists('auctions/' . $imagePath) ||
               Storage::disk('public')->exists('models/' . $imagePath) ||
               file_exists(public_path('storage/' . $imagePath));
    }

    /**
     * Get the product image URLs (multiple images) - UPDATED: Better error handling
     */
    public function getProductImageUrls(): array
    {
        return $this->image_urls;
    }

    /**
     * Get first product image URL (for backward compatibility) - UPDATED
     */
    public function getProductImageUrl(): ?string
    {
        return $this->first_image_url;
    }

    /**
     * Accessor for product images - UPDATED: Better data handling
     */
    public function getProductImagesAttribute(): array
    {
        return $this->image_gallery;
    }

    /**
     * Get owner proof document URL - UPDATED: Better error handling
     */
    public function getOwnerProofUrl(): ?string
    {
        if ($this->owner_proof && Storage::disk('public')->exists($this->owner_proof)) {
            return asset('storage/' . $this->owner_proof);
        }
        return null;
    }

    /**
     * Get market value proof document URL - UPDATED: Better error handling
     */
    public function getMarketValueProofUrl(): ?string
    {
        if ($this->market_value_proof && Storage::disk('public')->exists($this->market_value_proof)) {
            return asset('storage/' . $this->market_value_proof);
        }
        return null;
    }

    /**
     * Check if auction has product images - UPDATED
     */
    public function hasProductImages(): bool
    {
        return $this->has_images;
    }

    /**
     * Check if auction has owner proof
     */
    public function hasOwnerProof(): bool
    {
        return !empty($this->owner_proof);
    }

    /**
     * Check if auction has market value proof
     */
    public function hasMarketValueProof(): bool
    {
        return !empty($this->market_value_proof);
    }

    /**
     * Get product image file extension
     */
    public function getProductImageExtension(): ?string
    {
        $images = $this->product_img ?? [];
        
        // Handle string format
        if (is_string($images) && !empty($images)) {
            return pathinfo($images, PATHINFO_EXTENSION);
        }
        
        // Handle array format
        if (is_array($images) && !empty($images) && isset($images[0]) && is_string($images[0])) {
            return pathinfo($images[0], PATHINFO_EXTENSION);
        }
        
        return null;
    }

    /**
     * Get product placeholder image
     */
    public function getProductPlaceholder(): string
    {
        return asset('images/product-placeholder.png');
    }

    /**
     * Check if auction is currently active - UPDATED: More reliable check
     */
    public function isActive(): bool
    {
        return $this->status === 'active' && $this->end_time > now();
    }

    /**
     * Accessor for is_active
     */
    public function getIsActiveAttribute(): bool
    {
        return $this->isActive();
    }

    /**
     * Check if auction has ended - UPDATED: More reliable check
     */
    public function isEnded(): bool
    {
        return $this->status === 'ended' || $this->end_time <= now();
    }

    /**
     * Accessor for has_ended
     */
    public function getHasEndedAttribute(): bool
    {
        return $this->isEnded();
    }

    /**
     * Check if auction is pending verification
     */
    public function isPendingVerification(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if auction is verified by admin
     */
    public function isVerified(): bool
    {
        return !is_null($this->verified_at);
    }

    /**
     * Get formatted current bid
     */
    public function getFormattedCurrentBidAttribute(): string
    {
        // If current_bid is 0, show starting price as minimum bid
        if ($this->current_bid == 0) {
            return '💎' . number_format($this->starting_price, 0) . ' (Starting)';
        }
        return '💎' . number_format($this->current_bid, 0);
    }

    /**
     * Get formatted buyout bid
     */
    public function getFormattedBuyoutBidAttribute(): string
    {
        return $this->buyout_bid ? '💎' . number_format($this->buyout_bid, 0) : 'N/A';
    }

    /**
     * Get formatted minimum market value
     */
    public function getFormattedMinimumMarketValue(): string
    {
        return '💎' . number_format($this->minimum_market_value, 0);
    }

    /**
     * Get formatted delivery cost
     */
    public function getFormattedDeliveryCost(): string
    {
        return '💎' . number_format($this->delivery_cost, 0);
    }

    /**
     * Get delivery method as text
     */
    public function getDeliveryMethodTextAttribute(): string
    {
        switch ($this->delivery_method) {
            case 'seller_delivery':
                return 'Seller Delivery';
            case 'pickup':
                return 'Pickup';
            case 'courier':
                return 'Courier Service';
            default:
                return 'Seller Delivery';
        }
    }

    /**
     * Get verification status
     */
    public function getIsVerifiedAttribute(): bool
    {
        return $this->isVerified();
    }

    /**
     * Get payout status text
     */
    public function getPayoutStatusTextAttribute(): string
    {
        switch ($this->payout_status) {
            case 'pending':
                return 'Pending Admin Approval';
            case 'approved':
                return 'Approved - Ready for Release';
            case 'released':
                return 'Released to Seller';
            case 'rejected':
                return 'Rejected by Admin';
            case 'refunded':
                return 'Refunded to Buyer';
            default:
                return 'Not Applicable';
        }
    }

    /**
     * Check if payout is in escrow
     */
    public function isPayoutInEscrow(): bool
    {
        return $this->payout_status === 'pending' && !is_null($this->escrow_held_at);
    }

    /**
     * Check if payout is approved but not released
     */
    public function isPayoutApproved(): bool
    {
        return $this->payout_status === 'approved';
    }

    /**
     * Check if payout is released to seller
     */
    public function isPayoutReleased(): bool
    {
        return $this->payout_status === 'released';
    }

    /**
     * Check if payout is rejected
     */
    public function isPayoutRejected(): bool
    {
        return $this->payout_status === 'rejected';
    }

    /**
     * Check if payout is refunded
     */
    public function isPayoutRefunded(): bool
    {
        return $this->payout_status === 'refunded';
    }

    /**
     * Check if seller reply is overdue (more than 12 hours) - UPDATED
     */
    public function getIsSellerReplyOverdueAttribute(): bool
    {
        if (!$this->seller_reply_deadline) {
            return false;
        }
        return now()->greaterThan($this->seller_reply_deadline);
    }

    /**
     * Check if item has been received by buyer
     */
    public function getIsItemReceivedAttribute(): bool
    {
        return !is_null($this->item_received_at);
    }

    /**
     * Get hours escrow has been held - UPDATED: Show hours instead of days
     */
    public function getEscrowDaysHeldAttribute(): int
    {
        if (!$this->escrow_held_at) {
            return 0;
        }
        return $this->escrow_held_at->diffInHours(now());
    }

    /**
     * Get time remaining for auction - UPDATED: Better formatting
     */
    public function getTimeRemainingAttribute(): string
    {
        if ($this->isEnded()) {
            return 'Ended';
        }

        if ($this->isPendingVerification()) {
            return 'Pending Verification';
        }

        if ($this->isActive()) {
            $diff = now()->diff($this->end_time);
            
            if ($diff->days > 0) {
                return $diff->days . ' days ' . $diff->h . ' hours';
            } elseif ($diff->h > 0) {
                return $diff->h . ' hours ' . $diff->i . ' minutes';
            } else {
                return $diff->i . ' minutes ' . $diff->s . ' seconds';
            }
        }

        return 'Not started';
    }

    /**
     * Get bids count
     */
    public function getBidsCountAttribute(): int
    {
        // Use count if relation is loaded, otherwise query the database
        if ($this->relationLoaded('bids')) {
            return $this->bids->count();
        }
        
        return $this->bids()->count();
    }

    /**
     * Get highest bid for this auction
     */
    public function getHighestBid(): ?Bid
    {
        if ($this->relationLoaded('bids') && $this->bids->isNotEmpty()) {
            return $this->bids->sortByDesc('amount')->first();
        }
        
        return $this->bids()->orderByDesc('amount')->first();
    }

    /**
     * Check if buyout price is met
     */
    public function isBuyoutMet(float $amount): bool
    {
        return $this->buyout_bid && $amount >= $this->buyout_bid;
    }

    /**
     * Check if auction has winner
     */
    public function hasWinner(): bool
    {
        return !empty($this->winner_id);
    }

    /**
     * Get winner information
     */
    public function getWinner(): ?array
    {
        if ($this->hasWinner()) {
            return [
                'user_id' => $this->winner_id,
                'winning_bid' => $this->current_bid
            ];
        }
        return null;
    }

    /**
     * Scope: Active auctions
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')->where('end_time', '>', now());
    }

    /**
     * Scope: Ended auctions
     */
    public function scopeEnded($query)
    {
        return $query->where('status', 'ended')->orWhere('end_time', '<=', now());
    }

    /**
     * Scope: Pending verification auctions
     */
    public function scopePendingVerification($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope: Verified auctions
     */
    public function scopeVerified($query)
    {
        return $query->whereNotNull('verified_at');
    }

    /**
     * Scope: Pending payout auctions
     */
    public function scopePendingPayout($query)
    {
        return $query->where('payout_status', 'pending')
                    ->whereNotNull('winner_id')
                    ->where('payout_amount', '>', 0);
    }

    /**
     * Scope: Approved payout auctions
     */
    public function scopeApprovedPayout($query)
    {
        return $query->where('payout_status', 'approved');
    }

    /**
     * Scope: Released payout auctions
     */
    public function scopeReleasedPayout($query)
    {
        return $query->where('payout_status', 'released');
    }

    /**
     * Scope: Refunded payout auctions
     */
    public function scopeRefundedPayout($query)
    {
        return $query->where('payout_status', 'refunded');
    }

    /**
     * Scope: Search by product name or category
     */
    public function scopeSearch($query, $searchTerm)
    {
        return $query->where(function($q) use ($searchTerm) {
            $q->where('product_name', 'like', '%' . $searchTerm . '%')
              ->orWhere('category', 'like', '%' . $searchTerm . '%')
              ->orWhere('brand', 'like', '%' . $searchTerm . '%')
              ->orWhere('description', 'like', '%' . $searchTerm . '%');
        });
    }

    /**
     * Scope: Auctions with bids
     */
    public function scopeWithBids($query)
    {
        return $query->whereHas('bids');
    }

    /**
     * Scope: Auctions without winner
     */
    public function scopeWithoutWinner($query)
    {
        return $query->whereNull('winner_id');
    }

    /**
     * Get auction status badge class
     */
    public function getStatusBadgeClass(): string
    {
        switch ($this->status) {
            case 'active':
                return $this->isActive() ? 'bg-success' : 'bg-warning';
            case 'ended':
                return 'bg-secondary';
            case 'pending':
                return 'bg-warning';
            case 'approved':
                return 'bg-primary';
            case 'rejected':
                return 'bg-danger';
            default:
                return 'bg-secondary';
        }
    }

    /**
     * Get payout status badge class
     */
    public function getPayoutStatusBadgeClass(): string
    {
        switch ($this->payout_status) {
            case 'pending':
                return 'bg-warning';
            case 'approved':
                return 'bg-success';
            case 'released':
                return 'bg-info';
            case 'rejected':
                return 'bg-danger';
            case 'refunded':
                return 'bg-secondary';
            default:
                return 'bg-secondary';
        }
    }

    /**
     * Get auction status text
     */
    public function getStatusText(): string
    {
        switch ($this->status) {
            case 'active':
                return $this->isActive() ? 'Active' : 'Expired';
            case 'ended':
                return 'Ended';
            case 'pending':
                return 'Pending Verification';
            case 'approved':
                return 'Approved';
            case 'rejected':
                return 'Rejected';
            default:
                return ucfirst($this->status);
        }
    }

    /**
     * Check if auction can accept new bids
     */
    public function canAcceptBids(): bool
    {
        return $this->isActive() && !$this->hasWinner();
    }

    /**
     * Get the minimum next bid amount
     */
    public function getMinimumNextBid(): float
    {
        $minIncrement = 1.00; // Minimum bid increment
        
        // If no bids yet, minimum bid is the starting price
        if ($this->current_bid == 0) {
            return $this->starting_price;
        }
        
        return $this->current_bid + $minIncrement;
    }

    /**
     * Get formatted minimum next bid
     */
    public function getFormattedMinimumNextBid(): string
    {
        if ($this->current_bid == 0) {
            return '💎' . number_format($this->starting_price, 0) . ' (Starting bid)';
        }
        return '💎' . number_format($this->getMinimumNextBid(), 0);
    }

    /**
     * Check if auction has any bids
     */
    public function hasBids(): bool
    {
        return $this->current_bid > 0;
    }

    /**
     * Check if auction payout is pending
     */
    public function isPayoutPending(): bool
    {
        return $this->payout_status === 'pending';
    }

    /**
     * Check if auction payout is completed
     */
    public function isPayoutCompleted(): bool
    {
        return $this->payout_status === 'released';
    }

    /**
     * Check if auction meets all verification requirements
     */
    public function meetsVerificationRequirements(): bool
    {
        return $this->hasOwnerProof() && 
               $this->hasMarketValueProof() && 
               $this->minimum_market_value > 0 &&
               $this->terms_accepted;
    }

    /**
     * Get verification requirements status
     */
    public function getVerificationRequirementsStatus(): array
    {
        return [
            'owner_proof' => $this->hasOwnerProof(),
            'market_value_proof' => $this->hasMarketValueProof(),
            'minimum_market_value' => $this->minimum_market_value > 0,
            'terms_accepted' => $this->terms_accepted,
            'all_met' => $this->meetsVerificationRequirements(),
        ];
    }

    /**
     * Mark auction as verified
     */
    public function markAsVerified(User $verifier): bool
    {
        return $this->update([
            'verified_at' => now(),
            'verified_by' => $verifier->id,
            'status' => 'active'
        ]);
    }

    /**
     * Mark auction as rejected
     */
    public function markAsRejected(): bool
    {
        return $this->update([
            'verified_at' => null,
            'verified_by' => null,
            'status' => 'rejected'
        ]);
    }

    /**
     * Mark payout as approved
     */
    public function markPayoutApproved(User $approver): bool
    {
        return $this->update([
            'payout_status' => 'approved',
            'payout_approved_at' => now(),
            'payout_approved_by' => $approver->id
        ]);
    }

    /**
     * Mark payout as released - UPDATED: Automatically sends diamonds to seller
     */
    public function markPayoutReleased(): bool
    {
        try {
            DB::transaction(function () {
                // AUTOMATICALLY release diamonds to seller
                User::where('id', $this->user_id)->increment('diamond_balance', $this->payout_amount);

                // Mark payout as released
                $this->update([
                    'payout_status' => 'released',
                    'escrow_released_at' => now()
                ]);

                Log::info("AUTO-RELEASED payout for auction {$this->id}. 💎{$this->payout_amount} sent to seller {$this->user_id}");
            });

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to auto-release payout: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Mark payout as rejected
     */
    public function markPayoutRejected(User $rejecter): bool
    {
        return $this->update([
            'payout_status' => 'rejected',
            'payout_approved_at' => now(),
            'payout_approved_by' => $rejecter->id
        ]);
    }

    /**
     * Mark payout as refunded
     */
    public function markPayoutRefunded(): bool
    {
        return $this->update([
            'payout_status' => 'refunded'
        ]);
    }

    /**
     * Put payout in escrow - UPDATED: 12 hours for seller reply
     */
    public function putPayoutInEscrow(float $amount): bool
    {
        return $this->update([
            'payout_status' => 'pending',
            'payout_amount' => $amount,
            'escrow_held_at' => now(),
            'seller_reply_deadline' => now()->addHours(12),
            'chat_created_at' => now()
        ]);
    }

    /**
     * Mark item as received - UPDATED: Automatically releases payout to seller
     */
    public function markItemReceived(): bool
    {
        try {
            DB::transaction(function () {
                // Mark item as received
                $this->update([
                    'item_received_at' => now()
                ]);

                // AUTOMATICALLY release payout to seller
                $this->markPayoutReleased();

                Log::info("Item marked as received for auction {$this->id}. Payout AUTOMATICALLY released to seller.");
            });

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to mark item as received and release payout: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Calculate total cost including delivery
     */
    public function getTotalCost(float $bidAmount = null): float
    {
        $bid = $bidAmount ?: $this->current_bid;
        return $bid + $this->delivery_cost;
    }

    /**
     * Get formatted total cost including delivery
     */
    public function getFormattedTotalCost(float $bidAmount = null): string
    {
        return '💎' . number_format($this->getTotalCost($bidAmount), 0);
    }

    /**
     * Check if seller has replied within 12 hours - UPDATED
     */
    public function hasSellerReplied(): bool
    {
        if (!$this->chat_created_at) {
            return false;
        }
        
        // This method should be implemented with your actual chat system
        return false; // Placeholder - actual implementation in controller
    }

    /**
     * Check if automatic refund is due (seller didn't reply in 12 hours) - UPDATED
     */
    public function isAutomaticRefundDue(): bool
    {
        return $this->isSellerReplyOverdue && !$this->hasSellerReplied();
    }

    /**
     * Check if payout can be automatically released (buyer received item)
     */
    public function canAutoReleasePayout(): bool
    {
        return $this->isPayoutInEscrow() && 
               $this->item_received_at !== null &&
               $this->winner_id !== null;
    }

    /**
     * Automatically release payout to seller (called when buyer marks item received)
     */
    public function autoReleasePayout(): bool
    {
        if (!$this->canAutoReleasePayout()) {
            return false;
        }

        try {
            DB::transaction(function () {
                // Release diamonds to seller
                User::where('id', $this->user_id)->increment('diamond_balance', $this->payout_amount);

                // Mark payout as released
                $this->update([
                    'payout_status' => 'released',
                    'escrow_released_at' => now()
                ]);

                Log::info("AUTO-RELEASED payout for auction {$this->id}. 💎{$this->payout_amount} sent to seller {$this->user_id}");
            });

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to auto-release payout: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Process automatic refund for overdue seller replies (12 hours) - UPDATED
     */
    public function processAutomaticRefund(): bool
    {
        if (!$this->isAutomaticRefundDue()) {
            return false;
        }

        try {
            DB::transaction(function () {
                // Refund diamonds to buyer
                User::where('id', $this->winner_id)->increment('diamond_balance', $this->payout_amount);
                
                // Mark payout as refunded
                $this->update([
                    'payout_status' => 'refunded',
                    'escrow_released_at' => now()
                ]);

                Log::info("AUTO-REFUNDED payout for auction {$this->id}. 💎{$this->payout_amount} refunded to buyer {$this->winner_id} (12-hour timeout)");
            });

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to process automatic refund: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get all images with validation
     */
    public function getAllImages(): array
    {
        return $this->product_images;
    }

    /**
     * Check if auction can be edited
     */
    public function canBeEdited(): bool
    {
        return $this->status === 'pending' && $this->bids()->count() === 0;
    }

    /**
     * Check if auction can be deleted
     */
    public function canBeDeleted(): bool
    {
        return $this->status === 'pending' && $this->bids()->count() === 0;
    }

    /**
     * Get time until seller reply deadline
     */
    public function getTimeUntilSellerDeadline(): string
    {
        if (!$this->seller_reply_deadline) {
            return 'No deadline set';
        }
        
        if ($this->isSellerReplyOverdue) {
            return 'Overdue';
        }
        
        return $this->seller_reply_deadline->diffForHumans();
    }

    /**
     * Get count of images
     */
    public function getImageCount(): int
    {
        return $this->image_count;
    }

    /**
     * Check if auction has multiple images
     */
    public function hasMultipleImages(): bool
    {
        return $this->has_multiple_images;
    }
}