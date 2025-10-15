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
        Schema::create('stock_levels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->integer('current_stock')->default(0);
            $table->integer('low_stock_threshold')->default(5); // Default threshold
            $table->timestamp('last_updated_at')->useCurrent();
            $table->timestamps();

            // Add a unique constraint to ensure only one stock level entry per product, model, and color combination
            $table->unique(['product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_levels');
    }
};
