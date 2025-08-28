<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->string('imei', 20)->unique(); // 15–17 digits
            $table->foreignId('device_model_id')->constrained('device_models')->cascadeOnDelete();
            $table->string('firmware')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(['device_model_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};
