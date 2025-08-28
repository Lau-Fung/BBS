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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('plate', 32)->unique(); // "3191 JXA"
            $table->unsignedSmallInteger('tank_capacity_liters')->nullable(); // 650
            $table->enum('status', ['جاهز','صالح','خارج الخدمة','معلق'])->default('جاهز');
            $table->string('crm_no', 64)->nullable(); // CRM reference
            $table->text('notes')->nullable();
            $table->foreignId('supervisor_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
