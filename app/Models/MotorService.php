<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MotorService extends Model
{
    use HasFactory;

    protected $fillable = ['job_number', 'vehicle_id', 'mechanic_id', 'service_date', 'diagnosis_due_date', 'complaint', 'diagnosis', 'work_performed', 'labor_amount', 'parts_amount', 'discount_amount', 'total_amount', 'amount_paid', 'status', 'payment_status', 'completed_at', 'notes'];
    protected $casts = ['service_date' => 'date', 'diagnosis_due_date' => 'date', 'completed_at' => 'datetime', 'labor_amount' => 'decimal:2', 'parts_amount' => 'decimal:2', 'discount_amount' => 'decimal:2', 'total_amount' => 'decimal:2', 'amount_paid' => 'decimal:2'];

    public function vehicle() { return $this->belongsTo(Vehicle::class); }
    public function mechanic() { return $this->belongsTo(User::class, 'mechanic_id'); }
}
