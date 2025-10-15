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
        'unit_cost'
    ];
    protected $with = ['medicine', 'cosmetic'];
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
    public function medicine()
    {
        return $this->belongsTo(Medicine::class);
    }

    public function cosmetic()
    {
        return $this->belongsTo(Cosmetic::class);
    }

}
