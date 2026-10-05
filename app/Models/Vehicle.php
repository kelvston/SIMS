<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = ['customer_name', 'customer_phone', 'customer_email', 'registration_number', 'make', 'model', 'year', 'color', 'vin'];

    public function motorServices() { return $this->hasMany(MotorService::class); }
}
