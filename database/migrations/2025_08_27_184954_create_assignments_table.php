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
         Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_id')->constrained('devices')->cascadeOnDelete();
            $table->foreignId('sim_id')->nullable()->constrained('sims')->nullOnDelete();
            $table->foreignId('vehicle_id')->nullable()->constrained('vehicles')->nullOnDelete();
            $table->foreignId('sensor_id')->nullable()->constrained('sensors')->nullOnDelete();

            $table->boolean('is_installed')->default(false);
            $table->date('installed_on')->nullable();
            $table->date('removed_on')->nullable();
            $table->string('install_note')->nullable();

            $table->boolean('is_active')->default(true); // current mapping

            $table->timestamps();
            $table->unique(['device_id','is_active']); // one active mapping per device
            $table->index(['vehicle_id','sim_id','sensor_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
};
