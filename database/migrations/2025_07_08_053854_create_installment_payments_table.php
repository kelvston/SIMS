<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('installment_payments', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->foreignId('installment_plan_id')->constrained('installment_plans')->onDelete('cascade'); // Foreign key to installment_plans table
            $table->timestamp('payment_date')->useCurrent(); // Date of the payment
            $table->decimal('amount_paid', 10, 2); // Amount paid in this installment
            $table->timestamps(); // created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('installment_payments');
    }
};
