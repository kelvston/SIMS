<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Phone extends Model
{
    use HasFactory;

    // Define which attributes are mass assignable
    protected $fillable = [
        'imei',
        'model',
        'brand_id',
        'color',
        'storage_capacity',
        'purchase_price',
        'selling_price',
        'status',
        'received_at',
    ];

    // Define the casts for attributes
    protected $casts = [
        'received_at' => 'datetime',
    ];

    /**
     * Get the brand that owns the phone.
     */
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * Get the sale item associated with the phone.
     */
    public function saleItem()
    {
        return $this->hasOne(SaleItem::class);
    }


}
