<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Accessory extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'brand_id',
        'unit',
        'purchase_price',
        'selling_price',
        'status',
        'quantity',
        'barcode',
        'category'
    ];

    /**
     * Get the brand that owns the accessory.
     */
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
}
