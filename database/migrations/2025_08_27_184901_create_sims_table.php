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
        Schema::create('sims', function (Blueprint $table) {
            $table->id();
            $table->foreignId('carrier_id')->constrained('carriers')->cascadeOnDelete();
            $table->string('msisdn', 32)->unique()->nullable();    // phone number
            $table->string('sim_serial', 32)->nullable();          // optional (ICCID last digits, etc.)
            $table->date('plan_expiry_at')->nullable();
            $table->boolean('is_recharged')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(['carrier_id','is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sims');
    }
};
