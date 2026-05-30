<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('sale_items')) {
            return;
        }

        $needsFlexibleSchema = ! Schema::hasColumn('sale_items', 'product_id')
            || ! Schema::hasColumn('sale_items', 'quantity')
            || ! Schema::hasColumn('sale_items', 'unit_cost');

        if (! $needsFlexibleSchema) {
            return;
        }

        $existingRows = DB::table('sale_items')->count();

        if ($existingRows === 0) {
            Schema::dropIfExists('sale_items');

            Schema::create('sale_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('sale_id')->constrained('sales')->onDelete('cascade');
                $table->unsignedBigInteger('phone_id')->nullable()->index();
                $table->unsignedBigInteger('product_id')->nullable()->index();
                $table->unsignedBigInteger('cosmetic_id')->nullable()->index();
                $table->decimal('unit_price', 15, 2);
                $table->integer('quantity')->default(1);
                $table->decimal('unit_cost', 15, 2)->nullable();
                $table->timestamps();
            });

            return;
        }

        Schema::table('sale_items', function (Blueprint $table) {
            if (! Schema::hasColumn('sale_items', 'product_id')) {
                $table->unsignedBigInteger('product_id')->nullable()->index()->after('phone_id');
            }
            if (! Schema::hasColumn('sale_items', 'cosmetic_id')) {
                $table->unsignedBigInteger('cosmetic_id')->nullable()->index()->after('product_id');
            }
            if (! Schema::hasColumn('sale_items', 'quantity')) {
                $table->integer('quantity')->default(1)->after('unit_price');
            }
            if (! Schema::hasColumn('sale_items', 'unit_cost')) {
                $table->decimal('unit_cost', 15, 2)->nullable()->after('quantity');
            }
        });
    }

    public function down(): void
    {
        // Keep the flexible sale item schema because it can contain live sales data.
    }
};
