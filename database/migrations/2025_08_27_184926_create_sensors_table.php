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
        Schema::create('sensors', function (Blueprint $table) {
            $table->id();
            $table->string('serial_or_bt_id', 64)->unique(); // "A96E9B…"
            $table->foreignId('sensor_model_id')->nullable()->constrained('sensor_models')->nullOnDelete();
            $table->string('notes')->nullable(); // "dominator bt"
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sensors');
    }
};
