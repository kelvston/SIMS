<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cosmetic extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'product_id',
        'unit',
        'purchase_price',
        'selling_price',
        'status',
        'quantity',
        'barcode',
        'category',
        'stock_origin',
        'description'
    ];

    /**
     * Get the brand that owns the accessory.
     */
    public function brand()
    {
        return $this->belongsTo(Product::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
