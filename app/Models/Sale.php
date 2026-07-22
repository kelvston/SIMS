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
        'credit_due_date',
        'credit_reminder_days',
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
        'credit_due_date' => 'date',
        'credit_reminder_days' => 'integer',
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

    public function getIsCreditAttribute(): bool
    {
        return $this->payment_option === 'credit';
    }

    public function getCreditReminderStatusAttribute(): ?string
    {
        if (! $this->is_credit || $this->is_voided || (float) $this->amount_due <= 0 || ! $this->credit_due_date) {
            return null;
        }

        if ($this->credit_due_date->isPast() && ! $this->credit_due_date->isToday()) {
            return 'overdue';
        }

        if ($this->credit_due_date->lte(now()->addDays($this->credit_reminder_days ?? 3)->startOfDay())) {
            return 'due_soon';
        }

        return null;
    }

    public function getSaleTypeLabelAttribute(): string
    {
        return match ($this->payment_option) {
            'credit' => 'Credit',
            'installment' => 'Installment',
            default => $this->is_installment ? 'Installment' : 'Full Payment',
        };
    }

    public function scopeActiveTransaction($query)
    {
        return $query->where(function ($query) {
            $query->whereNull('status')
                ->orWhere('status', '!=', 'voided');
        });
    }

}
