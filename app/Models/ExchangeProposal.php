<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExchangeProposal extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'exchange_proposals';

    /**
     * Fields that can be mass-assigned
     */
    protected $fillable = [
        'sender_id',
        'receiver_id',
        'receiver_trade_id',
        'proposed_item_name',
        'proposed_item_brand',
        'proposed_item_category',
        'proposed_item_condition',
        'proposed_item_location',
        'proposed_item_description',
        'proposed_item_images',
        'proposed_item_documents',
        'cash_amount',
        'delivery_method',
        'meetup_location',
        'message',
        'status',
        'responded_at',
        'cancelled_at', // Added this field
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'proposed_item_images' => 'array',
        'proposed_item_documents' => 'array',
        'cash_amount' => 'decimal:2',
        'responded_at' => 'datetime',
        'cancelled_at' => 'datetime', // Added this cast
    ];

    /**
     * Default attribute values
     */
    protected $attributes = [
        'status' => 'pending',
        'cash_amount' => 0,
    ];

    /**
     * Get the user who sent the proposal (sender)
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Get the user who received the proposal (trade owner)
     */
    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    /**
     * Get the trade item that the sender wants
     */
    public function receiverTrade()
    {
        return $this->belongsTo(Trade::class, 'receiver_trade_id');
    }

    /**
     * Get the chat conversation associated with this proposal
     */
    public function chatConversation()
    {
        return $this->hasOne(ChatConversation::class, 'item_id')
                    ->where('item_source', 'trade_management')
                    ->where(function($query) {
                        $query->where('user1_id', $this->sender_id)
                              ->where('user2_id', $this->receiver_id)
                              ->orWhere(function($q) {
                                  $q->where('user1_id', $this->receiver_id)
                                    ->where('user2_id', $this->sender_id);
                              });
                    });
    }

    /**
     * Check if proposal is pending
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }

    /**
     * Check if proposal is accepted
     */
    public function isAccepted()
    {
        return $this->status === 'accepted';
    }

    /**
     * Check if proposal is rejected
     */
    public function isRejected()
    {
        return $this->status === 'rejected';
    }

    /**
     * Check if proposal is cancelled
     */
    public function isCancelled()
    {
        return $this->status === 'cancelled';
    }

    /**
     * Check if proposal has been responded to
     */
    public function isResponded()
    {
        return !is_null($this->responded_at);
    }

    /**
     * Check if proposal has cash amount
     */
    public function hasCashAmount()
    {
        return $this->cash_amount > 0;
    }

    /**
     * Check if delivery method is meetup
     */
    public function isMeetupDelivery()
    {
        return $this->delivery_method === 'meetupOnly';
    }

    /**
     * Accept the proposal
     */
    public function accept($responseMessage = null)
    {
        $this->update([
            'status' => 'accepted',
            'response_message' => $responseMessage,
            'responded_at' => now(),
        ]);
    }

    /**
     * Reject the proposal
     */
    public function reject($responseMessage = null)
    {
        $this->update([
            'status' => 'rejected',
            'response_message' => $responseMessage,
            'responded_at' => now(),
        ]);
    }

    /**
     * Cancel the proposal
     */
    public function cancel()
    {
        $this->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);
    }

    /**
     * Scope to get pending proposals
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope to get accepted proposals
     */
    public function scopeAccepted($query)
    {
        return $query->where('status', 'accepted');
    }

    /**
     * Scope to get rejected proposals
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Scope to get cancelled proposals
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    /**
     * Scope to get active proposals (not cancelled)
     */
    public function scopeActive($query)
    {
        return $query->where('status', '!=', 'cancelled');
    }

    /**
     * Scope to get proposals by sender
     */
    public function scopeBySender($query, $userId)
    {
        return $query->where('sender_id', $userId);
    }

    /**
     * Scope to get proposals by receiver
     */
    public function scopeByReceiver($query, $userId)
    {
        return $query->where('receiver_id', $userId);
    }

    /**
     * Scope to get proposals for a specific trade
     */
    public function scopeForTrade($query, $tradeId)
    {
        return $query->where('receiver_trade_id', $tradeId);
    }

    /**
     * Scope to get proposals with cash amount
     */
    public function scopeWithCash($query)
    {
        return $query->where('cash_amount', '>', 0);
    }

    /**
     * Get status badge color
     */
    public function getStatusBadgeColorAttribute()
    {
        return match($this->status) {
            'pending' => 'warning',
            'accepted' => 'success',
            'rejected' => 'danger',
            'cancelled' => 'secondary',
            default => 'secondary',
        };
    }

    /**
     * Get status display text
     */
    public function getStatusTextAttribute()
    {
        return ucfirst($this->status);
    }

    /**
     * Get first image from proposed item
     */
    public function getFirstImageAttribute()
    {
        $images = $this->proposed_item_images;
        return !empty($images) && is_array($images) ? $images[0] : null;
    }

    /**
     * Get all images from proposed item
     */
    public function getAllImagesAttribute()
    {
        $images = $this->proposed_item_images;
        return !empty($images) && is_array($images) ? $images : [];
    }

    /**
     * Get all documents from proposed item
     */
    public function getAllDocumentsAttribute()
    {
        $documents = $this->proposed_item_documents;
        return !empty($documents) && is_array($documents) ? $documents : [];
    }

    /**
     * Get formatted cash amount
     */
    public function getFormattedCashAmountAttribute()
    {
        return $this->cash_amount > 0 ? '₱' . number_format($this->cash_amount, 2) : null;
    }

    /**
     * Get delivery method display text
     */
    public function getDeliveryMethodTextAttribute()
    {
        return match($this->delivery_method) {
            'cashOnDelivery' => 'Cash On Delivery',
            'meetupOnly' => 'Meet-up Only',
            default => ucfirst($this->delivery_method),
        };
    }

    /**
     * Get condition display text
     */
    public function getProposedItemConditionTextAttribute()
    {
        return ucfirst($this->proposed_item_condition);
    }

    /**
     * Get category display text
     */
    public function getProposedItemCategoryTextAttribute()
    {
        return ucfirst(str_replace('-', ' ', $this->proposed_item_category));
    }

    /**
     * Get time elapsed since creation
     */
    public function getTimeElapsedAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Check if user can respond to this proposal
     */
    public function canRespond($userId)
    {
        return $this->receiver_id === $userId && $this->isPending();
    }

    /**
     * Check if user can cancel this proposal
     */
    public function canCancel($userId)
    {
        return $this->sender_id === $userId && $this->isPending();
    }

    /**
     * Check if user can view this proposal
     */
    public function canView($userId)
    {
        return $this->sender_id === $userId || $this->receiver_id === $userId;
    }

    /**
     * Get the conversation ID for this proposal
     */
    public function getConversationIdAttribute()
    {
        return 'trade_proposal_' . $this->receiver_trade_id . '_' . $this->sender_id;
    }

    /**
     * Boot method for model events
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-set receiver_id when creating from trade
        static::creating(function ($proposal) {
            if ($proposal->receiver_trade_id && !$proposal->receiver_id) {
                $trade = Trade::find($proposal->receiver_trade_id);
                if ($trade) {
                    $proposal->receiver_id = $trade->user_id;
                }
            }
        });
    }
}