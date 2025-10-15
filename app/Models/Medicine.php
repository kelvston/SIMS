<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medicine extends Model
{
    use HasFactory;

    // Define which attributes are mass assignable
    protected $fillable = [
        'product_id',
        'purchase_price',
        'selling_price',
        'status',
        'received_at',
        'condition',
        'stock_origin',
        'description'
    ];

    // Define the casts for attributes
    protected $casts = [
        'received_at' => 'datetime',
    ];

    /**
     * Get the product that owns the phone.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the sale item associated with the phone.
     */
    public function saleItem()
    {
        return $this->hasOne(SaleItem::class);
    }
    public function stockLevel()
    {
        return $this->hasOne(StockLevel::class, 'model', 'model')
            ->whereColumn('stock_levels.product_id', 'phones.product_id');
    }

    public function MedicineCategory()
    {
        return $this->belongsTo(MedicineCategory::class);
    }

}
