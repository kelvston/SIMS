<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Remove the unique index that depends on color first.
        Schema::table('stock_levels', function (Blueprint $table) {
            $table->dropUnique('stock_levels_brand_id_model_color_unique');
        });

        // Now remove color.
        Schema::table('stock_levels', function (Blueprint $table) {
            $table->dropColumn('color');
        });
    }

    public function down(): void
    {
        // Add color back.
        Schema::table('stock_levels', function (Blueprint $table) {
            $table->string('color')->nullable();
        });

        // Restore the old unique index.
        Schema::table('stock_levels', function (Blueprint $table) {
            $table->unique(
                ['brand_id', 'model', 'color'],
                'stock_levels_brand_id_model_color_unique'
            );
        });
    }
};
