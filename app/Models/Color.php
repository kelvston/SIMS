<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Color extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'phone_model_id'];

    public function phoneModel()
    {
        return $this->belongsTo(PhoneModel::class);
    }
}
