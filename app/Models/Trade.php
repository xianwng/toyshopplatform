<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class Trade extends Model
{
    use HasFactory;

    protected $table = 'trades';

    protected $fillable = [
        'user_id',
        'name',
        'brand',
        'category',
        'condition',
        'description',
        'status',
        'image',
        'location',
        'trade_preferences',
        'documents',
    ];

    protected $attributes = [
        'brand' => 'Unknown',
        'status' => 'pending',
    ];

    protected $casts = [
        'documents' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationship with user
    public function user()
    {
        return $this->belongsTo(User::class)->withDefault([
            'first_name' => 'Unknown',
            'last_name' => 'User',
            'username' => 'unknown',
            'email' => 'unknown@example.com',
            'profile_picture' => null,
            'contact_number' => null
        ]);
    }

    // Relationship with exchange proposals
    public function exchangeProposals()
    {
        return $this->hasMany(ExchangeProposal::class, 'receiver_trade_id');
    }

    // STATUS MANAGEMENT METHODS
    /**
     * Approve the trade (admin action)
     */
    public function approve()
    {
        $this->status = 'approved';
        $this->save();
    }

    /**
     * Reject the trade (admin action)
     */
    public function reject()
    {
        $this->status = 'rejected';
        $this->save();
    }

    /**
     * Activate the trade (user action after approval)
     */
    public function activate()
    {
        if ($this->status === 'approved') {
            $this->status = 'active';
            $this->save();
        }
    }

    /**
     * Deactivate the trade (user action)
     */
    public function deactivate()
    {
        $this->status = 'inactive';
        $this->save();
    }

    /**
     * Complete the trade (when exchange is done)
     */
    public function complete()
    {
        $this->status = 'completed';
        $this->save();
    }

    // STATUS CHECK METHODS
    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isApproved()
    {
        return $this->status === 'approved';
    }

    public function isActive()
    {
        return $this->status === 'active';
    }

    public function isInactive()
    {
        return $this->status === 'inactive';
    }

    public function isRejected()
    {
        return $this->status === 'rejected';
    }

    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    // SCOPES
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    public function scopeVisible($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeWithUploader($query)
    {
        return $query->with(['user' => function($query) {
            $query->select(['id', 'first_name', 'last_name', 'username', 'email', 'profile_picture', 'contact_number']);
        }]);
    }

    public function scopeWithProposalsCount($query)
    {
        return $query->withCount([
            'exchangeProposals as total_proposals_count',
            'exchangeProposals as pending_proposals_count' => function($query) {
                $query->where('status', 'pending');
            }
        ]);
    }

    public function scopeSearch($query, $searchTerm)
    {
        return $query->where(function($q) use ($searchTerm) {
            $q->where('name', 'like', "%{$searchTerm}%")
              ->orWhere('description', 'like', "%{$searchTerm}%")
              ->orWhere('brand', 'like', "%{$searchTerm}%")
              ->orWhere('category', 'like', "%{$searchTerm}%");
        });
    }

    // ATTRIBUTE METHODS
    public function getUploaderNameAttribute()
    {
        if (!$this->user) {
            return 'Unknown User';
        }
        
        $name = trim($this->user->first_name . ' ' . $this->user->last_name);
        return $name ?: 'Unknown User';
    }

    public function getUploaderUsernameAttribute()
    {
        return $this->user ? ($this->user->username ?? 'unknown') : 'unknown';
    }

    public function getImageUrlAttribute()
    {
        if ($this->image && Storage::disk('public')->exists($this->image)) {
            return asset('storage/' . $this->image);
        }
        return 'https://via.placeholder.com/400x300?text=No+Image';
    }

    public function getImagesArrayAttribute()
    {
        if (!$this->image) {
            return [];
        }

        // Handle both JSON string and array formats
        if (is_string($this->image) && json_decode($this->image)) {
            $images = json_decode($this->image, true);
            if (is_array($images)) {
                return array_map(function($image) {
                    return asset('storage/' . $image);
                }, $images);
            }
        }
        
        // Handle single image string
        return [asset('storage/' . $this->image)];
    }

    public function getDocumentUrlsAttribute()
    {
        if (!$this->documents || empty($this->documents)) {
            return [];
        }

        // FIXED: Properly handle documents that might be JSON string or already array
        $documents = $this->documents;
        
        // If documents is a string, try to decode it as JSON
        if (is_string($documents)) {
            $decoded = json_decode($documents, true);
            $documents = (json_last_error() === JSON_ERROR_NONE) ? $decoded : [$documents];
        }
        
        // Ensure documents is an array
        if (!is_array($documents) || empty($documents)) {
            return [];
        }

        $urls = [];
        foreach ($documents as $doc) {
            if (is_string($doc)) {
                $urls[] = [
                    'url' => asset('storage/' . $doc),
                    'type' => Str::endsWith($doc, '.pdf') ? 'pdf' : 'image',
                    'name' => basename($doc)
                ];
            }
        }

        return $urls;
    }

    public function getIsOwnerAttribute()
    {
        // FIXED: Use Auth facade with proper null checking
        if (!Auth::check()) {
            return false;
        }
        return $this->user_id === Auth::id();
    }

    public function getShortDescriptionAttribute()
    {
        return Str::limit($this->description, 150);
    }

    public function getFormattedCreatedAtAttribute()
    {
        return $this->created_at->format('M j, Y');
    }

    public function getTradePreferencesArrayAttribute()
    {
        if (!$this->trade_preferences) {
            return [];
        }
        return array_map('trim', explode(',', $this->trade_preferences));
    }

    public function getHasDocumentsAttribute()
    {
        return !empty($this->documents) && is_array($this->documents) && count($this->documents) > 0;
    }

    public function getFirstDocumentUrlAttribute()
    {
        if (empty($this->documents)) {
            return null;
        }
        
        // FIXED: Handle documents properly whether they're JSON string or array
        $documents = $this->documents;
        
        if (is_string($documents)) {
            $decoded = json_decode($documents, true);
            $documents = (json_last_error() === JSON_ERROR_NONE) ? $decoded : [$documents];
        }
        
        if (is_array($documents) && !empty($documents)) {
            $firstDoc = $documents[0];
            if (is_string($firstDoc)) {
                return asset('storage/' . $firstDoc);
            }
        }
        
        return null;
    }

    // RELATIONSHIPS
    public function proposalsReceived()
    {
        return $this->hasMany(ExchangeProposal::class, 'receiver_trade_id');
    }

    public function acceptedProposals()
    {
        return $this->proposalsReceived()->where('status', 'accepted');
    }

    // BOOT METHOD
    protected static function boot()
    {
        parent::boot();

        static::deleting(function($trade) {
            // Delete images
            if ($trade->image) {
                $images = is_string($trade->image) && json_decode($trade->image) 
                    ? json_decode($trade->image, true) 
                    : [$trade->image];
                
                foreach ($images as $image) {
                    if (Storage::disk('public')->exists($image)) {
                        Storage::disk('public')->delete($image);
                    }
                }
            }

            // Delete documents - FIXED: Handle documents properly
            if ($trade->documents) {
                $documents = $trade->documents;
                
                // If documents is a string, try to decode it as JSON
                if (is_string($documents)) {
                    $decoded = json_decode($documents, true);
                    $documents = (json_last_error() === JSON_ERROR_NONE) ? $decoded : [$documents];
                }
                
                // Ensure documents is an array before processing
                if (is_array($documents)) {
                    foreach ($documents as $doc) {
                        if (is_string($doc) && Storage::disk('public')->exists($doc)) {
                            Storage::disk('public')->delete($doc);
                        }
                    }
                }
            }

            // Delete related proposals
            $trade->exchangeProposals()->delete();
        });
    }

    public function getRouteKeyName()
    {
        return 'id';
    }
}