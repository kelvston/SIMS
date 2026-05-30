<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('sale_items') && ! Schema::hasColumn('sale_items', 'phone_id')) {
            Schema::table('sale_items', function (Blueprint $table) {
                $table->unsignedBigInteger('phone_id')->nullable()->after('sale_id');
                $table->index('phone_id');
            });
        }

        if (Schema::hasTable('sales')) {
            Schema::table('sales', function (Blueprint $table) {
                if (! Schema::hasColumn('sales', 'amount_paid')) {
                    $table->decimal('amount_paid', 15, 2)->default(0);
                }
                if (! Schema::hasColumn('sales', 'amount_due')) {
                    $table->decimal('amount_due', 15, 2)->default(0);
                }
                if (! Schema::hasColumn('sales', 'payment_option')) {
                    $table->string('payment_option')->nullable();
                }
            });
        }

        $stockNeedsRepair = ! Schema::hasTable('stock_levels')
            || ! Schema::hasColumn('stock_levels', 'brand_id')
            || ! Schema::hasColumn('stock_levels', 'model')
            || ! Schema::hasColumn('stock_levels', 'color');

        if ($stockNeedsRepair) {
            Schema::dropIfExists('stock_levels');

            Schema::create('stock_levels', function (Blueprint $table) {
                $table->id();
                $table->foreignId('brand_id')->constrained('brands')->onDelete('cascade');
                $table->string('model');
                $table->string('color');
                $table->integer('current_stock')->default(0);
                $table->integer('low_stock_threshold')->default(5);
                $table->timestamp('last_updated_at')->useCurrent();
                $table->timestamps();
                $table->unique(['brand_id', 'model', 'color']);
            });
        }

        if (Schema::hasTable('phones') && Schema::hasTable('stock_levels')) {
            DB::table('stock_levels')->delete();

            DB::table('phones')
                ->select('brand_id', 'model', 'color', DB::raw('COUNT(*) as current_stock'))
                ->where('status', 'available')
                ->groupBy('brand_id', 'model', 'color')
                ->orderBy('brand_id')
                ->get()
                ->each(function ($row) {
                    DB::table('stock_levels')->insert([
                        'brand_id' => $row->brand_id,
                        'model' => $row->model,
                        'color' => $row->color,
                        'current_stock' => $row->current_stock,
                        'low_stock_threshold' => 5,
                        'last_updated_at' => now(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                });
        }
    }

    public function down(): void
    {
        // This migration repairs live schema drift and intentionally leaves data intact.
    }
};
