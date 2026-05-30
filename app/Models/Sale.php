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
        'amount_due','payment_option',
        'user_id',
        'product_size_id'
    ];

    // Define the casts for attributes
    protected $casts = [
        'sale_date' => 'datetime',
        'is_installment' => 'boolean',
    ];


    /**
     * Get the sale items for the sale.
     */
    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function soldBy()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the installment plan associated with the sale.
     */
    public function installmentPlan()
    {
        return $this->hasOne(InstallmentPlan::class);
    }


}
