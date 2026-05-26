<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cashew extends Model
{
    use HasFactory;

    // Define which attributes are mass assignable
//array:12 [▼ // app/Http/Controllers/CashewController.php:53
//"product_id" => "1"
//"name" => "Raw Cashew Nuts"
//"unit" => "kg"
//"unit_price" => "10000"
//"selling_price" => "20000"
//"quantity" => "20"
//"stock_origin" => "mtwara"
//"condition" => "good"
//"batch_number" => "1"
//"received_at" => "2026-05-11"
//"description" => "yes"
//"status" => "available"
//]
    protected $fillable = [
        'status',
        'product_id',
        'unit',
        'unit_price',
        'selling_price',
        'quantity',
        'low_stock_threshold',
        'received_at',
        'stock_origin',
        'condition',
        'batch_number',
        'description',
    ];

    // Define the casts for attributes
    protected $casts = [
        'received_at' => 'datetime',
        'quantity' => 'integer',
        'low_stock_threshold' => 'integer',
        'unit_price' => 'float',
        'selling_price' => 'float',
    ];

    /**
     * Get the product that owns the phone.
     */
    public function product(){
        return $this->belongsTo(Product::class);
    }

}
