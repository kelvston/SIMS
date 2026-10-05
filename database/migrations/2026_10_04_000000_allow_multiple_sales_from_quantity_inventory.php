<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('sale_items') && Schema::hasIndex('sale_items', 'sale_items_phone_id_unique')) {
            Schema::table('sale_items', function (Blueprint $table) {
                $table->dropUnique('sale_items_phone_id_unique');
                $table->index('phone_id');
            });
        }
    }

    public function down(): void
    {
        // A unique phone_id would invalidate legitimate partial-quantity sales.
    }
};
