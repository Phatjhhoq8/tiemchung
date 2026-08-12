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
        Schema::table('registration_vaccines', function (Blueprint $table) {
            $table->foreignId('regimen_id')->nullable()->after('vaccine_id')->constrained('vaccine_regimens')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('registration_vaccines', function (Blueprint $table) {
            $table->dropForeign(['regimen_id']);
            $table->dropColumn('regimen_id');
        });
    }
};
