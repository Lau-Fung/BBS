<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('assignments', function (Blueprint $t) {
            $t->json('extras')->nullable()->after('install_note');
        });
    }
    public function down(): void {
        Schema::table('assignments', function (Blueprint $t) {
            $t->dropColumn('extras');
        });
    }
};

