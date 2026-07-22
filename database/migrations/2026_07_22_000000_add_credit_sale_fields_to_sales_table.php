<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            if (! Schema::hasColumn('sales', 'credit_due_date')) {
                $table->date('credit_due_date')->nullable()->after('payment_option');
            }

            if (! Schema::hasColumn('sales', 'credit_reminder_days')) {
                $table->unsignedSmallInteger('credit_reminder_days')->default(3)->after('credit_due_date');
            }
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            foreach (['credit_reminder_days', 'credit_due_date'] as $column) {
                if (Schema::hasColumn('sales', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
