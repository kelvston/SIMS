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
        Schema::table('sale_items', function (Blueprint $table) {
            $table->dropUnique('sale_items_medicine_id_unique'); // drop old index
            $table->index('product_id', 'sale_items_product_id_index'); // add regular index (not unique)
        });
    }

    public function down()
    {
        Schema::table('sale_items', function (Blueprint $table) {
            $table->dropIndex('sale_items_product_id_index');
            $table->unique('product_id', 'sale_items_medicine_id_unique');
        });
    }
};
