<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registration_vaccines', function (Blueprint $table) {
            $table->unsignedInteger('stock_committed_quantity')->default(0)->after('quantity');
        });

        DB::table('center_vaccines')->update([
            'stock_status' => DB::raw("CASE WHEN stock_quantity = 0 THEN 'out_of_stock' WHEN stock_quantity <= 5 THEN 'limited' ELSE 'available' END"),
        ]);
    }

    public function down(): void
    {
        Schema::table('registration_vaccines', function (Blueprint $table) {
            $table->dropColumn('stock_committed_quantity');
        });
    }
};
