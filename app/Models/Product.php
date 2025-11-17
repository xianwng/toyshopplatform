<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'brand',
        'category',
        'condition',
        'rarity',
        'description',
        'stock',
        'price',
        'model_file',
        'product_images',
        'asin',
        'amazon_price',
        'amazon_stock',
        'last_synced_at',
        'status',
        'sync_status',
        'last_sync_attempt',
        'use_amazon_data',
        'shipping_methods',
        'seller_phone',
        'seller_address',
        'original_price',
        'certificate_path',
        'market_value_proof',
    ];

    protected $casts = [
        'price' => 'float',
        'stock' => 'integer',
        'amazon_price' => 'float',
        'amazon_stock' => 'integer',
        'last_synced_at' => 'datetime',
        'last_sync_attempt' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'use_amazon_data' => 'boolean',
        'shipping_methods' => 'array',
        'product_images' => 'array',
    ];

    /**
     * Relationship: Product belongs to a user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope: Products that are publicly visible (active status + stock > 0)
     */
    public function scopePublic($query)
    {
        return $query->where('status', 'active')
                    ->where('stock', '>', 0);
    }

    /**
     * Scope: Approved products (ready to be activated by customer)
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope: Pending products
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope: Active products (approved and activated by customer)
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope: Inactive products (approved but not activated by customer)
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope: User's products (all statuses)
     */
    public function scopeUserProducts($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Check if product is approved (ready for activation)
     */
    public function isApproved()
    {
        return $this->status === 'approved';
    }

    /**
     * Check if product is pending
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }

    /**
     * Check if product is active (approved and activated by customer)
     */
    public function isActive()
    {
        return $this->status === 'active';
    }

    /**
     * Check if product can be displayed publicly
     */
    public function isPublic()
    {
        return $this->isActive() && $this->stock > 0;
    }

    /**
     * Check if product can be displayed to owner (approved or active)
     */
    public function isVisibleToOwner()
    {
        return $this->isActive() || $this->isApproved();
    }

    /**
     * Activate product (make it public) - Customer action
     */
    public function activate()
    {
        if ($this->isApproved()) {
            $this->update(['status' => 'active']);
            return true;
        }
        return false;
    }

    /**
     * Deactivate product (hide it from public) - Customer action
     */
    public function deactivate()
    {
        if ($this->isActive()) {
            $this->update(['status' => 'approved']);
            return true;
        }
        return false;
    }

    /**
     * Approve product (admin action) - Sets to approved but not active
     */
    public function approve()
    {
        $this->update(['status' => 'approved']);
    }

    /**
     * Reject product (admin action)
     */
    public function reject()
    {
        $this->update(['status' => 'rejected']);
    }

    /**
     * Get status badge class for styling
     */
    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            'active' => 'success',
            'approved' => 'warning',
            'pending' => 'info',
            'rejected' => 'danger',
            default => 'secondary'
        };
    }

    /**
     * Get status display text
     */
    public function getStatusDisplayAttribute()
    {
        return match($this->status) {
            'active' => 'Active',
            'approved' => 'Approved (Ready to Activate)',
            'pending' => 'Pending Approval',
            'rejected' => 'Rejected',
            default => 'Unknown'
        };
    }

    /**
     * Check if customer can activate this product
     */
    public function canActivate()
    {
        return $this->isApproved();
    }

    /**
     * Check if customer can deactivate this product
     */
    public function canDeactivate()
    {
        return $this->isActive();
    }

    /**
     * Relationship: A product can have one auction
     */
    public function auction()
    {
        return $this->hasOne(Auction::class);
    }

    /**
     * Check if the product is currently in an active auction
     */
    public function isInActiveAuction()
    {
        return $this->auction()->where('status', 'active')->exists();
    }

    /**
     * Check if the product is available for auction
     */
    public function isAvailableForAuction()
    {
        return !$this->isInActiveAuction() && $this->stock > 0;
    }

    /**
     * Scope: Products available for auction
     */
    public function scopeAvailableForAuction($query)
    {
        return $query->where('stock', '>', 0)
                    ->whereDoesntHave('auction', function($q) {
                        $q->where('status', 'active');
                    });
    }

    /**
     * Reduce stock when product is sold
     */
    public function reduceStock($quantity = 1)
    {
        if ($this->stock >= $quantity) {
            $this->decrement('stock', $quantity);
            return true;
        }
        return false;
    }

    // ==================== IMAGE METHODS ====================

    /**
     * Get the first product image (for thumbnails)
     */
    public function getFirstImageAttribute()
    {
        $images = $this->product_images ?? [];
        
        // Handle case where images might be stored as JSON string
        if (is_string($images) && !empty($images)) {
            $decoded = json_decode($images, true);
            $images = is_array($decoded) ? $decoded : [];
        }
        
        return !empty($images) && is_array($images) ? $images[0] : null;
    }

    /**
     * Get all product images
     */
    public function getImageGalleryAttribute()
    {
        $images = $this->product_images ?? [];
        
        // Handle case where images might be stored as JSON string
        if (is_string($images) && !empty($images)) {
            $decoded = json_decode($images, true);
            return is_array($decoded) ? $decoded : [];
        }
        
        return is_array($images) ? $images : [];
    }

    /**
     * Check if product has images
     */
    public function hasImages()
    {
        $images = $this->image_gallery;
        return !empty($images) && is_array($images) && count($images) > 0;
    }

    /**
     * Get image URLs array
     */
    public function getImageUrlsAttribute()
    {
        $images = $this->image_gallery;
        $urls = [];
        
        foreach ($images as $image) {
            $url = $this->getImageUrl($image);
            if ($url) {
                $urls[] = $url;
            }
        }
        
        return $urls;
    }

    /**
     * Get first image URL
     */
    public function getFirstImageUrlAttribute()
    {
        $firstImage = $this->first_image;
        
        if (!$firstImage) {
            return $this->getDefaultImageUrl();
        }

        $imageUrl = $this->getImageUrl($firstImage);
        return $imageUrl ?: $this->getDefaultImageUrl();
    }

    /**
     * Get image URL with multiple fallback checks
     */
    private function getImageUrl($imagePath)
    {
        if (!$imagePath) {
            return null;
        }

        // Clean the path - remove any directory prefixes to get just the filename
        $cleanFilename = basename($imagePath);
        
        // Try different directory locations in priority order
        $possiblePaths = [
            'models/' . $cleanFilename,    // Primary location (where files actually are)
            'products/' . $cleanFilename,  // Secondary location  
            $cleanFilename,                // Root as fallback
            $imagePath,                    // Original path as stored (fallback)
        ];

        foreach ($possiblePaths as $path) {
            if (Storage::disk('public')->exists($path)) {
                return Storage::url($path);
            }
        }

        // Direct file system check as final fallback
        $directPaths = [
            storage_path('app/public/models/' . $cleanFilename),
            storage_path('app/public/products/' . $cleanFilename),
            storage_path('app/public/' . $cleanFilename),
            storage_path('app/public/' . $imagePath),
        ];

        foreach ($directPaths as $directPath) {
            if (file_exists($directPath)) {
                // Extract the correct relative path
                $relativePath = str_replace(storage_path('app/public/'), '', $directPath);
                return Storage::url($relativePath);
            }
        }

        return null;
    }

    /**
     * Get default image URL
     */
    private function getDefaultImageUrl()
    {
        return asset('images/default-product.png');
    }

    /**
     * Get main product image URL
     */
    public function getMainImageUrl(): ?string
    {
        return $this->first_image_url;
    }

    /**
     * Check if product has gallery images
     */
    public function hasGallery()
    {
        return $this->hasImages();
    }

    /**
     * Get product images gallery with proper URLs
     */
    public function getProductImagesGalleryAttribute()
    {
        $images = $this->product_images ?? [];
        
        if (is_string($images) && !empty($images)) {
            $decoded = json_decode($images, true);
            $images = is_array($decoded) ? $decoded : [];
        }
        
        if (empty($images) || !is_array($images)) {
            return [];
        }
        
        $imageUrls = [];
        foreach ($images as $image) {
            $url = $this->getImageUrl($image);
            if ($url) {
                $imageUrls[] = $url;
            }
        }
        
        return $imageUrls;
    }

    // ==================== DOCUMENTATION METHODS ====================

    /**
     * Get certificate URL if exists
     */
    public function getCertificateUrlAttribute()
    {
        if (!$this->certificate_path) {
            return null;
        }

        if (Storage::disk('public')->exists($this->certificate_path)) {
            return Storage::url($this->certificate_path);
        }

        return null;
    }

    /**
     * Get market value proof URL if exists
     */
    public function getMarketValueProofUrlAttribute()
    {
        if (!$this->market_value_proof) {
            return null;
        }

        if (Storage::disk('public')->exists($this->market_value_proof)) {
            return Storage::url($this->market_value_proof);
        }

        return null;
    }

    /**
     * Check if product has certificate
     */
    public function hasCertificate()
    {
        return !empty($this->certificate_path) && Storage::disk('public')->exists($this->certificate_path);
    }

    /**
     * Check if product has market value proof
     */
    public function hasMarketValueProof()
    {
        return !empty($this->market_value_proof) && Storage::disk('public')->exists($this->market_value_proof);
    }

    /**
     * Get certificate file name
     */
    public function getCertificateFileNameAttribute()
    {
        if (!$this->certificate_path) {
            return null;
        }
        return basename($this->certificate_path);
    }

    /**
     * Get market value proof file name
     */
    public function getMarketValueProofFileNameAttribute()
    {
        if (!$this->market_value_proof) {
            return null;
        }
        return basename($this->market_value_proof);
    }

    /**
     * Get certificate file type
     */
    public function getCertificateFileTypeAttribute()
    {
        if (!$this->certificate_path) {
            return null;
        }
        return pathinfo($this->certificate_path, PATHINFO_EXTENSION);
    }

    /**
     * Get market value proof file type
     */
    public function getMarketValueProofFileTypeAttribute()
    {
        if (!$this->market_value_proof) {
            return null;
        }
        return pathinfo($this->market_value_proof, PATHINFO_EXTENSION);
    }

    /**
     * Check if certificate is PDF
     */
    public function getCertificateIsPdfAttribute()
    {
        return $this->certificate_file_type === 'pdf';
    }

    /**
     * Check if market value proof is PDF
     */
    public function getMarketValueProofIsPdfAttribute()
    {
        return $this->market_value_proof_file_type === 'pdf';
    }

    // ==================== SHIPPING METHODS ====================

    /**
     * Check if product has specific shipping method
     */
    public function hasShippingMethod($method)
    {
        return in_array($method, $this->shipping_methods ?? []);
    }

    /**
     * Get available shipping methods as string
     */
    public function getShippingMethodsStringAttribute()
    {
        if (empty($this->shipping_methods)) {
            return 'No shipping methods specified';
        }

        $methodNames = [
            'lalamove' => 'Lalamove',
            'lbc' => 'LBC Express',
            'jnt' => 'J&T Express'
        ];

        $formattedMethods = array_map(function($method) use ($methodNames) {
            return $methodNames[$method] ?? ucfirst($method);
        }, $this->shipping_methods);

        return implode(', ', $formattedMethods);
    }

    /**
     * Get shipping method icons
     */
    public function getShippingMethodIconsAttribute()
    {
        $icons = [
            'lalamove' => '🚚',
            'lbc' => '📦', 
            'jnt' => '✈️'
        ];

        $methodIcons = [];
        foreach ($this->shipping_methods ?? [] as $method) {
            $methodIcons[$method] = $icons[$method] ?? '📦';
        }

        return $methodIcons;
    }

    // ==================== CONDITION METHODS ====================

    /**
     * Get condition display text
     */
    public function getConditionDisplayAttribute()
    {
        return match($this->condition) {
            'sealed' => 'Sealed (0% discount)',
            'bib' => 'BIB - Box in Box (15% discount)',
            'loose' => 'Loose - No Box (20% discount)',
            default => ucfirst($this->condition)
        };
    }

    /**
     * Get condition badge class
     */
    public function getConditionBadgeClassAttribute()
    {
        return match($this->condition) {
            'sealed' => 'success',
            'bib' => 'warning',
            'loose' => 'info',
            default => 'secondary'
        };
    }

    // ==================== EXISTING METHODS ====================

    /**
     * Get formatted price
     */
    public function getFormattedPriceAttribute()
    {
        return '₱' . number_format($this->price, 2);
    }

    /**
     * Get formatted Amazon price
     */
    public function getFormattedAmazonPriceAttribute()
    {
        return $this->amazon_price ? '₱' . number_format($this->amazon_price, 2) : 'N/A';
    }

    /**
     * Check if product is in stock (from customer side)
     */
    public function inStock()
    {
        return $this->stock > 0;
    }

    /**
     * Check if product is out of stock (from customer side)
     */
    public function isOutOfStock()
    {
        return $this->stock <= 0;
    }

    /**
     * Get product availability status (from customer side)
     */
    public function getAvailabilityStatusAttribute()
    {
        if ($this->isOutOfStock()) {
            return 'Out of Stock';
        } else {
            return 'In Stock';
        }
    }

    /**
     * Get Amazon availability status
     */
    public function getAmazonAvailabilityStatusAttribute()
    {
        if ($this->amazon_stock === null) {
            return 'Not Synced';
        } elseif ($this->amazon_stock > 0) {
            return 'In Stock (' . $this->amazon_stock . ')';
        } else {
            return 'Out of Stock';
        }
    }

    /**
     * Get the rarity badge class for styling (from customer side)
     */
    public function getRarityBadgeClassAttribute()
    {
        return match($this->rarity) {
            'rare' => 'danger',
            'limited' => 'warning',
            'exclusive' => 'info',
            default => 'secondary'
        };
    }

    /**
     * Scope a query to only include available products. (from customer side)
     */
    public function scopeAvailable($query)
    {
        return $query->where('stock', '>', 0);
    }

    /**
     * Scope a query to only include products by category. (from customer side)
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope a query to only include products by rarity. (from customer side)
     */
    public function scopeByRarity($query, $rarity)
    {
        return $query->where('rarity', $rarity);
    }

    /**
     * Scope a query to search products. (from customer side)
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('name', 'LIKE', "%{$search}%")
              ->orWhere('brand', 'LIKE', "%{$search}%")
              ->orWhere('description', 'LIKE', "%{$search}%")
              ->orWhere('category', 'LIKE', "%{$search}%")
              ->orWhere('asin', 'LIKE', "%{$search}%");
        });
    }

    /**
     * Scope a query to sort products. (from customer side)
     */
    public function scopeSortBy($query, $sortBy = 'newest')
    {
        switch ($sortBy) {
            case 'price_low':
                return $query->orderBy('price', 'asc');
            case 'price_high':
                return $query->orderBy('price', 'desc');
            case 'name':
                return $query->orderBy('name', 'asc');
            case 'stock':
                return $query->orderBy('stock', 'desc');
            case 'amazon_price_low':
                return $query->orderBy('amazon_price', 'asc');
            case 'amazon_price_high':
                return $query->orderBy('amazon_price', 'desc');
            default:
                return $query->orderBy('created_at', 'desc');
        }
    }
}