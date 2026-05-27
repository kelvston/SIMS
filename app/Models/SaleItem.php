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
        'medicine_id',
        'unit_price',
        'cosmetic_id',
        'quantity',
        'is_sold',
        'unit_cost',
        'product_id',
        'product_size_id'
    ];
    protected $with = ['cashew', ];
    /**
     * Get the sale that owns the sale item.
     */
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    /**
     * Get the medicine associated with the sale item.
     */
    public function cashew()
    {
        return $this->belongsTo(Cashew::class);
    }
    public function products(){
        return $this->belongsTo(Product::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function cashews()
    {
        return $this->belongsTo(Cashew::class, 'product_id', 'product_id');
    }

// In SaleItem model
    public function productSize()
    {
        return $this->belongsTo(\App\Models\ProductSize::class, 'product_size_id');
    }

}
