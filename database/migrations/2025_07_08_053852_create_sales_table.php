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
        Schema::create('sales', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->string('customer_name');
            $table->string('customer_phone')->nullable(); // Customer's phone number, optional
            $table->decimal('total_amount', 10, 2); // Sum of unit prices before discount
            $table->decimal('discount_amount', 10, 2)->nullable(); // Applied discount amount
            $table->decimal('final_amount', 10, 2); // Total amount after discount
            $table->timestamp('sale_date')->useCurrent(); // Date of the sale
            $table->boolean('is_installment')->default(false); // Flag if it's an installment sale
            $table->timestamps(); // created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
