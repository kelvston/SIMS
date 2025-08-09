<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockAdjustment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'accessory_id',
        'old_quantity',
        'new_quantity',
        'comment',
        'adjusted_by_user_id',
        'phone_id'
    ];

    /**
     * Get the accessory that the stock adjustment belongs to.
     */
    public function accessory()
    {
        return $this->belongsTo(Accessory::class);
    }

    /**
     * Get the user that made the stock adjustment.
     */
    public function adjustedBy()
    {
        return $this->belongsTo(User::class, 'adjusted_by_user_id');
    }
}
