<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('vaccines', 'type')) {
            return;
        }

        DB::table('vaccines')->where('type', 'package')->delete();

        Schema::table('vaccines', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }

    public function down(): void
    {
        if (!Schema::hasColumn('vaccines', 'type')) {
            Schema::table('vaccines', function (Blueprint $table) {
                $table->string('type')->default('single')->after('sale_price');
            });
        }
    }
};
