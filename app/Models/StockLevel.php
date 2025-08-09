<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockLevel extends Model
{
    use HasFactory;

    // Define which attributes are mass assignable
    protected $fillable = [
        'brand_id',
        'model',
        'color',
        'current_stock',
        'low_stock_threshold',
        'last_updated_at',
    ];

    // Define the casts for attributes
    protected $casts = [
        'last_updated_at' => 'datetime',
    ];

    /**
     * Get the brand that owns the stock level.
     */
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * Get the phone that owns the stock level.
     *
     * @return BelongsTo
     */
    public function phone(): BelongsTo
    {
        return $this->belongsTo(Phone::class);
    }

    /**
     * Get the accessory that owns the stock level.
     *
     * @return BelongsTo
     */
    public function accessory(): BelongsTo
    {
        return $this->belongsTo(Accessory::class);
    }
}
