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
        if (Schema::hasTable('vaccines') && !Schema::hasColumn('vaccines', 'is_active')) {
            Schema::table('vaccines', function (Blueprint $table) {
                $table->boolean('is_active')->default(true)->after('views');
            });
        }

        if (Schema::hasTable('articles') && !Schema::hasColumn('articles', 'is_active')) {
            Schema::table('articles', function (Blueprint $table) {
                $table->boolean('is_active')->default(true)->after('is_published');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('vaccines') && Schema::hasColumn('vaccines', 'is_active')) {
            Schema::table('vaccines', function (Blueprint $table) {
                $table->dropColumn('is_active');
            });
        }

        if (Schema::hasTable('articles') && Schema::hasColumn('articles', 'is_active')) {
            Schema::table('articles', function (Blueprint $table) {
                $table->dropColumn('is_active');
            });
        }
    }
};
