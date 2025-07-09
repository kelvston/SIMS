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
        Schema::create('phones', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->string('imei')->unique(); // IMEI, must be unique for each phone
            $table->string('model'); // Phone model (e.g., iPhone 15 Pro Max)
            $table->foreignId('brand_id')->constrained('brands')->onDelete('cascade'); // Foreign key to brands table
            $table->string('color');
            $table->string('storage_capacity'); // e.g., '128GB', '256GB'
            $table->decimal('purchase_price', 10, 2); // Price at which the phone was bought
            $table->decimal('selling_price', 10, 2); // Price at which the phone will be sold
            $table->enum('status', ['available', 'sold', 'under_installment', 'damaged'])->default('available'); // Current status of the phone
            $table->timestamp('received_at')->useCurrent(); // Timestamp when the phone was received
            $table->timestamps(); // created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phones');
    }
};
