<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccessoryStock extends Model
{
    use HasFactory;

    protected $table = 'cashews';

    protected $fillable = [
        'product_id',
        'status',
        'received_at',
        'batch_number',
        'condition',
        'quantity',
        'unit',
        'unit_price',
        'selling_price',
        'barcode',
        'user_id',
        'low_stock_threshold',
    ];

    protected $casts = [
        'received_at' => 'date',
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
