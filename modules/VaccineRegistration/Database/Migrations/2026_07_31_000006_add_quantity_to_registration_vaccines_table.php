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
            if (!Schema::hasColumn('registration_vaccines', 'quantity')) {
                $table->unsignedInteger('quantity')->default(1)->after('price');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('registration_vaccines', function (Blueprint $table) {
            if (Schema::hasColumn('registration_vaccines', 'quantity')) {
                $table->dropColumn('quantity');
            }
        });
    }
};
