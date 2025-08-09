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
            Schema::table('stock_adjustments', function (Blueprint $table) {
                $table->integer('phone_id')->nullable();
                $table->unsignedBigInteger('accessory_id')->nullable()->change();
                $table->unsignedInteger('old_quantity')->nullable()->change();
                $table->unsignedInteger('new_quantity')->nullable()->change();
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_adjustments', function (Blueprint $table) {
            $table->integer('phone_id')->nullable();
            $table->unsignedBigInteger('accessory_id')->change();
            $table->unsignedInteger('old_quantity')->change();
            $table->unsignedInteger('new_quantity')->change();
        });
    }
};
