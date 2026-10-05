<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = ['order_id', 'phone_id', 'description', 'quantity', 'reserved_quantity', 'unit_price', 'line_total'];

    protected $casts = ['quantity' => 'integer', 'reserved_quantity' => 'integer', 'unit_price' => 'decimal:2', 'line_total' => 'decimal:2'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function phone()
    {
        return $this->belongsTo(Phone::class);
    }
}
