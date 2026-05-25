<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sale_items', function (Blueprint $table) {

            // 1. Drop old FK first (IMPORTANT)
            $table->dropForeign(['product_id']);

            // 2. Re-add correct FK
            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('sale_items', function (Blueprint $table) {

            $table->dropForeign(['product_id']);

            // rollback to old (if needed)
            $table->foreign('product_id')
                ->references('id')
                ->on('medicines')
                ->cascadeOnDelete();
        });
    }
};
