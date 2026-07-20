<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhoneStorageCapacity extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'phone_model_id'];

    public function phoneModel()
    {
        return $this->belongsTo(PhoneModel::class);
    }
}
