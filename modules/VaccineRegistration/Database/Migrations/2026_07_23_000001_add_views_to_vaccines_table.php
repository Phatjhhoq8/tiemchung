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
        if (!Schema::hasColumn('vaccines', 'views')) {
            Schema::table('vaccines', function (Blueprint $table) {
                $table->integer('views')->default(0)->after('sort_order');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('vaccines', 'views')) {
            Schema::table('vaccines', function (Blueprint $table) {
                $table->dropColumn('views');
            });
        }
    }
};
