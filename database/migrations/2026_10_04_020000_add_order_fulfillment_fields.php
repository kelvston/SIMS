<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('phones', function (Blueprint $table) {
            $table->unsignedInteger('reserved_quantity')->default(0)->after('quantity');
        });

        Schema::table('order_items', function (Blueprint $table) {
            // Keep this as an indexed nullable id so the migration remains safe
            // on existing SQLite installations as well as MySQL.
            $table->unsignedBigInteger('phone_id')->nullable()->after('order_id')->index();
            $table->unsignedInteger('reserved_quantity')->default(0)->after('quantity');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->timestamp('reserved_at')->nullable()->after('notes');
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->unsignedBigInteger('order_id')->nullable()->unique()->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropUnique('sales_order_id_unique');
            $table->dropColumn('order_id');
        });
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('reserved_at');
        });
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropIndex('order_items_phone_id_index');
            $table->dropColumn('phone_id');
            $table->dropColumn('reserved_quantity');
        });
        Schema::table('phones', function (Blueprint $table) {
            $table->dropColumn('reserved_quantity');
        });
    }
};
