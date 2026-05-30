<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cashews', function (Blueprint $table) {
            if (! Schema::hasColumn('cashews', 'stock_origin')) {
                $table->string('stock_origin')->nullable()->after('batch_number');
            }
            if (! Schema::hasColumn('cashews', 'description')) {
                $table->string('description')->nullable()->after('stock_origin');
            }
        });

        Schema::table('sale_items', function (Blueprint $table) {
            if (! Schema::hasColumn('sale_items', 'cashew_id')) {
                $table->foreignId('cashew_id')
                    ->nullable()
                    ->after('sale_id')
                    ->constrained('cashews')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('sale_items', function (Blueprint $table) {
            if (Schema::hasColumn('sale_items', 'cashew_id')) {
                $table->dropConstrainedForeignId('cashew_id');
            }
        });

        Schema::table('cashews', function (Blueprint $table) {
            if (Schema::hasColumn('cashews', 'description')) {
                $table->dropColumn('description');
            }
            if (Schema::hasColumn('cashews', 'stock_origin')) {
                $table->dropColumn('stock_origin');
            }
        });
    }
};
