<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{
    use HasFactory;

    // Define which attributes are mass assignable
    protected $fillable = [
        'sale_id',
        'phone_id',
        'product_id',
        'cosmetic_id',
        'unit_price',
        'quantity',
        'unit_cost',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'quantity' => 'integer',
        'unit_cost' => 'decimal:2',
    ];

    /**
     * Get the sale that owns the sale item.
     */
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    /**
     * Get the phone associated with the sale item.
     */
    public function phone()
    {
        return $this->belongsTo(Phone::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function accessoryStock()
    {
        return $this->belongsTo(AccessoryStock::class, 'cosmetic_id');
    }

}
