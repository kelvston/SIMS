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
            $table->foreignId('brand_id')->constrained('brands')->onDelete('cascade');
            $table->string('model');
            $table->string('color');
            $table->integer('current_stock')->default(0);
            $table->integer('low_stock_threshold')->default(5); // Default threshold
            $table->timestamp('last_updated_at')->useCurrent();
            $table->timestamps();

            // Add a unique constraint to ensure only one stock level entry per brand, model, and color combination
            $table->unique(['brand_id', 'model', 'color']);
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
