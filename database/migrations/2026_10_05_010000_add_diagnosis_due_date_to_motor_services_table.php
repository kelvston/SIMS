<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('motor_services', function (Blueprint $table) {
            $table->date('diagnosis_due_date')->nullable()->after('service_date');
        });
    }

    public function down(): void
    {
        Schema::table('motor_services', function (Blueprint $table) {
            $table->dropColumn('diagnosis_due_date');
        });
    }
};
