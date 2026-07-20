<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function accessoryStocks()
    {
        return $this->hasMany(AccessoryStock::class, 'product_id');
    }
}
