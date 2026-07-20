<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('phone_storage_capacities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('phone_model_id')->constrained('phone_models')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['phone_model_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('phone_storage_capacities');
    }
};
