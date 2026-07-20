<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            if (! Schema::hasColumn('sales', 'status')) {
                $table->string('status')->default('completed')->after('payment_option');
            }

            if (! Schema::hasColumn('sales', 'voided_at')) {
                $table->timestamp('voided_at')->nullable()->after('status');
            }

            if (! Schema::hasColumn('sales', 'voided_by')) {
                $table->unsignedBigInteger('voided_by')->nullable()->after('voided_at');
                $table->index('voided_by');
            }

            if (! Schema::hasColumn('sales', 'void_reason')) {
                $table->text('void_reason')->nullable()->after('voided_by');
            }

            if (! Schema::hasColumn('sales', 'original_final_amount')) {
                $table->decimal('original_final_amount', 15, 2)->nullable()->after('void_reason');
            }
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            if (Schema::hasColumn('sales', 'voided_by')) {
                $table->dropIndex(['voided_by']);
                $table->dropColumn('voided_by');
            }

            foreach (['status', 'voided_at', 'void_reason', 'original_final_amount'] as $column) {
                if (Schema::hasColumn('sales', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
