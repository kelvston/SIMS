<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SaleReceipt extends Model
{
    use HasFactory;

    protected $table = 'sale_receipts';

    protected $fillable = [
        'receipt_number',
        'sale_id',
        'issued_at',
        'subtotal',
        'tax',
        'discount',
        'total',
        'is_installment',
        'paid_amount',
        'payment_method',
        'status',
        'notes',
    ];

    protected $casts = [
        'issued_at' => 'datetime',
        'is_installment' => 'boolean',
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
        'paid_amount' => 'decimal:2',
    ];

    // Relationship to Sale (assuming you have a Sale model)
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    // Accessor for balance (if not using virtual column in DB)
    public function getBalanceAttribute()
    {
        return $this->total - $this->paid_amount;
    }
}
