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
        Schema::create('installment_plans', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->foreignId('sale_id')->unique()->constrained('sales')->onDelete('cascade'); // Foreign key to sales table, unique per sale
            $table->integer('total_installments'); // Total number of installments
            $table->decimal('installment_amount', 10, 2); // Amount per installment
            $table->date('start_date'); // Date when the installment plan started
            $table->date('next_payment_date')->nullable(); // Next expected payment date
            $table->enum('status', ['active', 'completed', 'defaulted'])->default('active'); // Status of the installment plan
            $table->timestamps(); // created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('installment_plans');
    }
};
