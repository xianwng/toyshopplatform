<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Diamond extends Model
{
    use HasFactory;

    protected $table = 'diamond_bundles';
    
    protected $fillable = [
        'name',
        'diamond_amount',
        'price',
        'badge_type',
        'badge_text',
        'is_active',
        'display_order'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'price' => 'decimal:2'
    ];

    // Static methods for different types of operations
    public static function getActiveBundles()
    {
        return self::where('is_active', true)
            ->orderBy('display_order')
            ->get();
    }

    public static function findBundle($id)
    {
        return self::find($id);
    }
}