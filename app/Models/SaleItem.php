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
        'unit_price',
        'accessory_id',
        'quantity',
        'is_sold'
    ];
    protected $with = ['phone', 'accessory'];
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

    public function accessory()
    {
        return $this->belongsTo(Accessory::class);
    }

}
