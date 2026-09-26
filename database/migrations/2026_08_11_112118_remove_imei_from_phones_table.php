<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Remove the old unique IMEI index first.
        Schema::table('phones', function (Blueprint $table) {
            $table->dropUnique('phones_imei_unique');
        });

        // Now remove the phone-specific columns.
        Schema::table('phones', function (Blueprint $table) {
            $table->dropColumn([
                'imei',
                'color',
                'storage_capacity',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('phones', function (Blueprint $table) {
            $table->string('imei')->nullable();
            $table->string('color')->nullable();
            $table->string('storage_capacity')->nullable();
        });

        Schema::table('phones', function (Blueprint $table) {
            $table->unique('imei');
        });
    }
};
