<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('stock_adjustments', function (Blueprint $table) {
            $table->renameColumn('cosmetic_id', 'stock_item_id');
            $table->string('type')->default('medicine')->after('id'); // 'medicine' or 'cosmetic'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
