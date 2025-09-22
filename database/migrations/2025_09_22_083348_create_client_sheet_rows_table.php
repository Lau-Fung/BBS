<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('client_sheet_rows', function (Blueprint $table) {
            $table->id();

            // owners / links
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();

            // keep all identifiers as strings to avoid scientific notation problems
            $table->string('data_package_type', 50)->nullable();    // (data package type)نوع الباقة
            $table->string('sim_type', 50)->nullable();             // (SIM type) نوع الشريحة
            $table->string('sim_number', 64)->nullable();           // (SIM Number / ICCID) رقم الشريحة
            $table->string('imei', 32)->nullable();                 //IMEI
            $table->string('plate', 50)->nullable();                //رقم اللوحة 

            $table->date('installed_on')->nullable();               // (Date of installation) تاريخ التركيب 
            $table->string('year_model', 16)->nullable();           // (year model) موديل المركبة 
            $table->string('company_manufacture', 120)->nullable(); // (company manufacture) اسم الشركة المصنعة للمركبة
            $table->string('device_type', 60)->nullable();          // (device type / model code) نوع الجهاز

            // three “green” columns in your shot
            $table->boolean('air')->nullable();                     // منافيخ
            $table->boolean('mechanic')->nullable();                // سست
            $table->string('tracking', 60)->nullable();             // تتبع / tracking
            $table->string('system_type', 60)->nullable();          // system type نظام التتبع

            $table->string('calibration', 60)->nullable();          // المعايرة 
            $table->string('color', 40)->nullable();                // لون المركبة
            $table->string('crm_integration', 120)->nullable();     // رقم الطلبcrm
            $table->string('technician', 120)->nullable();          // الفني
            $table->string('vehicle_serial_number', 120)->nullable();       //الرقم التسلسلي للسيارة 
            $table->string('vehicle_weight', 60)->nullable();               // وزن المركبة للسيارة

            $table->string('user', 120)->nullable();                // USER
            $table->string('contract', 120)->nullable();            // العقد (if you have it)
            $table->text('notes')->nullable();                      // ملاحظات

            $table->timestamps();
            $table->softDeletes();

            // some helpful indexes
            $table->index(['client_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_sheet_rows');
    }
};
