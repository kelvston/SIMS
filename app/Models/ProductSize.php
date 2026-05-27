<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductSize extends Model
{
    protected $fillable = [
        'product_id',
        'size',
        'color',
        'quantity',
    ];

    public function cashew()
    {
        return $this->belongsTo(Cashew::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
