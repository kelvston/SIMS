<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('products')) {
            Schema::create('products', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('cashews')) {
            Schema::create('cashews', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
                $table->string('status')->default('available');
                $table->date('received_at')->nullable();
                $table->string('batch_number')->nullable();
                $table->string('condition')->nullable();
                $table->decimal('quantity', 15, 2)->default(0);
                $table->string('unit')->default('piece');
                $table->decimal('unit_price', 15, 2)->default(0);
                $table->decimal('selling_price', 15, 2)->default(0);
                $table->string('barcode')->nullable();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->integer('low_stock_threshold')->default(5);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        // Keep compatibility tables intact to avoid deleting live inventory data.
    }
};
