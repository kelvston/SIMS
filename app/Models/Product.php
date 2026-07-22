<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    // Define which attributes are mass assignable
    protected $fillable = ['name'];

    /**
     * Get the medicines for the product.
     */
    public function medicines()
    {
        return $this->hasMany(Medicine::class);
    }

    /**
     * Get the stock levels for the product.
     */
    public function stockLevels()
    {
        return $this->hasMany(StockLevel::class);
    }


    public function cosmetics()
    {
        return $this->hasMany(Cosmetic::class);
    }
}
