<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstallmentPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'installment_plan_id',
        'payment_date',
        'amount_paid',
    ];

    protected $casts = [
        'payment_date' => 'datetime',
    ];

    /**
     * Get the installment plan that owns the payment.
     */
    public function installmentPlan()
    {
        return $this->belongsTo(InstallmentPlan::class);
    }
}
