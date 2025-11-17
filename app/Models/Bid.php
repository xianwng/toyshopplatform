<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Bid extends Model
{
    use HasFactory;

    protected $fillable = [
        'auction_id',
        'user_id',
        'amount',
        'is_buyout',
    ];

    protected $casts = [
        'amount'     => 'float',
        'is_buyout'  => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $appends = [
        'formatted_amount',
        'time_elapsed',
        'type_label',
        'type_badge_class',
    ];

    /**
     * Relationship: Bid belongs to an auction
     */
    public function auction(): BelongsTo
    {
        return $this->belongsTo(Auction::class);
    }

    /**
     * Relationship: Bid belongs to a user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope: Buyout bids
     */
    public function scopeBuyout($query)
    {
        return $query->where('is_buyout', true);
    }

    /**
     * Scope: Regular bids (non-buyout)
     */
    public function scopeRegular($query)
    {
        return $query->where('is_buyout', false);
    }

    /**
     * Check if this bid won the auction
     */
    public function isWinningBid(): bool
    {
        if (!$this->relationLoaded('auction') || $this->auction->status !== 'ended') {
            return false;
        }
        
        return $this->auction->winner_id === $this->user_id;
    }

    /**
     * Get formatted amount
     */
    public function getFormattedAmountAttribute(): string
    {
        return '₱' . number_format($this->amount, 2);
    }

    /**
     * Check if this is a buyout bid
     */
    public function isBuyout(): bool
    {
        // If is_buyout is explicitly set to true
        if ($this->is_buyout) {
            return true;
        }

        // Check if amount meets or exceeds buyout price (only if auction is loaded)
        if ($this->relationLoaded('auction') && $this->auction->buyout_bid) {
            return $this->amount >= $this->auction->buyout_bid;
        }

        return false;
    }

    /**
     * Get the time elapsed since the bid was placed
     */
    public function getTimeElapsedAttribute(): string
    {
        if (!$this->created_at) {
            return 'Unknown time';
        }
        
        $now = now();
        $diff = $this->created_at->diff($now);
        
        if ($diff->days > 0) {
            return $diff->days . ' day' . ($diff->days > 1 ? 's' : '') . ' ago';
        } elseif ($diff->h > 0) {
            return $diff->h . ' hour' . ($diff->h > 1 ? 's' : '') . ' ago';
        } elseif ($diff->i > 0) {
            return $diff->i . ' minute' . ($diff->i > 1 ? 's' : '') . ' ago';
        } else {
            return 'Just now';
        }
    }

    /**
     * Check if bid is valid (higher than current bid at time of placement)
     * Note: This should be checked before creating the bid, not after
     */
    public function isValidBid(): bool
    {
        if (!$this->relationLoaded('auction')) {
            return false;
        }
        
        if ($this->isBuyout()) {
            return $this->amount >= $this->auction->buyout_bid;
        }
        
        return $this->amount > $this->auction->current_bid;
    }

    /**
     * Scope: Get highest bid for an auction
     */
    public function scopeHighest($query, $auctionId = null)
    {
        $query = $query->orderBy('amount', 'desc');
        
        if ($auctionId) {
            $query->where('auction_id', $auctionId);
        }
        
        return $query;
    }

    /**
     * Scope: Get bids for a specific auction
     */
    public function scopeForAuction($query, $auctionId)
    {
        return $query->where('auction_id', $auctionId);
    }

    /**
     * Scope: Get bids by a specific user
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Check if this bid triggered the buyout
     */
    public function triggeredBuyout(): bool
    {
        return $this->isBuyout() && $this->relationLoaded('auction') && $this->auction->status === 'ended';
    }

    /**
     * Get bid type label
     */
    public function getTypeLabelAttribute(): string
    {
        return $this->isBuyout() ? 'Buyout' : 'Regular';
    }

    /**
     * Get bid type badge class
     */
    public function getTypeBadgeClassAttribute(): string
    {
        return $this->isBuyout() ? 'bg-danger' : 'bg-primary';
    }

    /**
     * Check if bid is the highest for its auction
     * Note: Use with caution - can cause performance issues if used in loops
     */
    public function isHighestBid(): bool
    {
        if (!$this->relationLoaded('auction') || !$this->auction->relationLoaded('bids')) {
            return false;
        }

        $highestBid = $this->auction->bids->max('amount');
        return $highestBid === $this->amount;
    }

    /**
     * Get the bid position (1st, 2nd, 3rd, etc.)
     * Note: Use with caution - can cause performance issues if used in loops
     */
    public function getPositionAttribute(): ?int
    {
        if (!$this->relationLoaded('auction') || !$this->auction->relationLoaded('bids')) {
            return null;
        }

        $sortedBids = $this->auction->bids->sortByDesc('amount')->values();
        $position = $sortedBids->search(function ($bid) {
            return $bid->id === $this->id;
        });

        return $position !== false ? $position + 1 : null;
    }

    /**
     * Get bid details for display
     * Note: Removed from appends to avoid circular references
     */
    public function getBidDetails(): array
    {
        return [
            'id' => $this->id,
            'amount' => $this->formatted_amount,
            'user_id' => $this->user_id,
            'auction_id' => $this->auction_id,
            'is_buyout' => $this->is_buyout,
            'type' => $this->type_label,
            'time_elapsed' => $this->time_elapsed,
            'position' => $this->position,
            'is_highest' => $this->isHighestBid(),
            'created_at' => $this->created_at->format('M j, Y g:i A')
        ];
    }
}