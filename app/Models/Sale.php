<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    // Define which attributes are mass assignable
    protected $fillable = [
        'customer_name',
        'customer_phone',
        'total_amount',
        'discount_amount',
        'final_amount',
        'sale_date',
        'is_installment',
        'customer_email',
        'amount_paid',
        'amount_due',
        'payment_option',
        'status',
        'voided_at',
        'voided_by',
        'void_reason',
        'original_final_amount',
    ];

    // Define the casts for attributes
    protected $casts = [
        'sale_date' => 'datetime',
        'is_installment' => 'boolean',
        'total_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'final_amount' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'amount_due' => 'decimal:2',
        'voided_at' => 'datetime',
        'original_final_amount' => 'decimal:2',
    ];

    /**
     * Get the sale items for the sale.
     */
    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }

    /**
     * Get the installment plan associated with the sale.
     */
    public function installmentPlan()
    {
        return $this->hasOne(InstallmentPlan::class);
    }

    public function saleReceipt()
    {
        return $this->hasOne(SaleReceipt::class);
    }

    public function voidedBy()
    {
        return $this->belongsTo(User::class, 'voided_by');
    }

    public function getIsVoidedAttribute(): bool
    {
        return $this->status === 'voided' || $this->voided_at !== null;
    }

    public function scopeActiveTransaction($query)
    {
        return $query->where(function ($query) {
            $query->whereNull('status')
                ->orWhere('status', '!=', 'voided');
        });
    }

}
