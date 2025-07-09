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
        Schema::create('sale_items', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->foreignId('sale_id')->constrained('sales')->onDelete('cascade'); // Foreign key to sales table
            $table->foreignId('phone_id')->unique()->constrained('phones')->onDelete('cascade'); // Foreign key to phones table, unique to ensure a phone is sold only once per sale
            $table->decimal('unit_price', 10, 2); // Price of this specific phone in this sale
            $table->timestamps(); // created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_items');
    }
};
