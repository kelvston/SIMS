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
        'product_id',
        'current_stock',
        'low_stock_threshold',
        'last_updated_at',
    ];

    // Define the casts for attributes
    protected $casts = [
        'last_updated_at' => 'datetime',
    ];

    /**
     * Get the product that owns the stock level.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the medicine that owns the stock level.
     *
     * @return BelongsTo
     */
    public function medicine(): BelongsTo
    {
        return $this->belongsTo(Medicine::class);
    }

    /**
     * Get the cosmetic that owns the stock level.
     *
     * @return BelongsTo
     */
    public function cosmetic(): BelongsTo
    {
        return $this->belongsTo(Cosmetic::class);
    }
}
