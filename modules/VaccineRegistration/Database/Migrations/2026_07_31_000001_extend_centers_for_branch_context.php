<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('centers', function (Blueprint $table) {
            if (!Schema::hasColumn('centers', 'slug')) {
                $table->string('slug')->nullable()->unique()->after('name');
            }
            if (!Schema::hasColumn('centers', 'zalo_phone')) {
                $table->string('zalo_phone')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('centers', 'map_url')) {
                $table->text('map_url')->nullable()->after('zalo_phone');
            }
            if (!Schema::hasColumn('centers', 'working_hours')) {
                $table->string('working_hours')->nullable()->after('map_url');
            }
            if (!Schema::hasColumn('centers', 'sort_order')) {
                $table->unsignedInteger('sort_order')->default(0)->after('is_active');
            }
        });
    }

    public function down(): void
    {
        Schema::table('centers', function (Blueprint $table) {
            foreach (['slug', 'zalo_phone', 'map_url', 'working_hours', 'sort_order'] as $column) {
                if (Schema::hasColumn('centers', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
