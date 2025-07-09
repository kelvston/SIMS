<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstallmentPlan extends Model
{
    use HasFactory;

    // Define which attributes are mass assignable
    protected $fillable = [
        'sale_id',
        'total_installments',
        'installment_amount',
        'start_date',
        'next_payment_date',
        'status',
    ];

    // Define the casts for attributes
    protected $casts = [
        'start_date' => 'date',
        'next_payment_date' => 'date',
    ];

    /**
     * Get the sale that owns the installment plan.
     */
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    /**
     * Get the installment payments for the installment plan.
     */
    public function installmentPayments()
    {
        return $this->hasMany(InstallmentPayment::class);
    }
}
