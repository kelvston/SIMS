<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('stock_adjustments')) {
            return;
        }

        Schema::create('stock_adjustments_new', function (Blueprint $table) {
            $table->id();
            $table->string('type')->default('medicine');
            $table->foreignId('stock_item_id')->nullable()->constrained('cashews')->onDelete('cascade');
            $table->unsignedInteger('old_quantity')->nullable();
            $table->unsignedInteger('new_quantity')->nullable();
            $table->string('comment')->nullable();
            $table->foreignId('adjusted_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->integer('medicine_id')->nullable();
        });

        DB::statement('
            INSERT INTO stock_adjustments_new (
                id,
                type,
                stock_item_id,
                old_quantity,
                new_quantity,
                comment,
                adjusted_by_user_id,
                created_at,
                updated_at,
                medicine_id
            )
            SELECT
                id,
                type,
                CASE
                    WHEN stock_item_id IS NULL THEN NULL
                    WHEN EXISTS (SELECT 1 FROM cashews WHERE cashews.id = stock_adjustments.stock_item_id)
                        THEN stock_item_id
                    ELSE NULL
                END,
                old_quantity,
                new_quantity,
                comment,
                adjusted_by_user_id,
                created_at,
                updated_at,
                medicine_id
            FROM stock_adjustments
        ');

        Schema::drop('stock_adjustments');
        Schema::rename('stock_adjustments_new', 'stock_adjustments');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('stock_adjustments')) {
            return;
        }

        Schema::create('stock_adjustments_old', function (Blueprint $table) {
            $table->id();
            $table->string('type')->default('medicine');
            $table->foreignId('stock_item_id')->nullable()->constrained('cosmetics')->onDelete('cascade');
            $table->unsignedInteger('old_quantity')->nullable();
            $table->unsignedInteger('new_quantity')->nullable();
            $table->string('comment')->nullable();
            $table->foreignId('adjusted_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->integer('medicine_id')->nullable();
        });

        DB::statement('
            INSERT INTO stock_adjustments_old (
                id,
                type,
                stock_item_id,
                old_quantity,
                new_quantity,
                comment,
                adjusted_by_user_id,
                created_at,
                updated_at,
                medicine_id
            )
            SELECT
                id,
                type,
                CASE
                    WHEN stock_item_id IS NULL THEN NULL
                    WHEN EXISTS (SELECT 1 FROM cosmetics WHERE cosmetics.id = stock_adjustments.stock_item_id)
                        THEN stock_item_id
                    ELSE NULL
                END,
                old_quantity,
                new_quantity,
                comment,
                adjusted_by_user_id,
                created_at,
                updated_at,
                medicine_id
            FROM stock_adjustments
        ');

        Schema::drop('stock_adjustments');
        Schema::rename('stock_adjustments_old', 'stock_adjustments');
    }
};
