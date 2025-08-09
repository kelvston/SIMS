<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReturnLog extends Model
{

    protected $fillable = [
        'sale_id',
        'sale_item_id',
        'reason',
        'returned_by_user_id',
    ];
}
