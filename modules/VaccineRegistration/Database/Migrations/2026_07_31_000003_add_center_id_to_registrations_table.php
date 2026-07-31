<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            if (!Schema::hasColumn('registrations', 'center_id')) {
                $table->foreignId('center_id')->nullable()->after('guardian_phone')->constrained('centers')->nullOnDelete();
            }
        });

        if (Schema::hasColumn('registrations', 'center_name')) {
            DB::table('registrations')->orderBy('id')->chunkById(200, function ($registrations) {
                foreach ($registrations as $registration) {
                    $center = DB::table('centers')->where('name', $registration->center_name)->first();
                    if ($center) {
                        DB::table('registrations')->where('id', $registration->id)->update(['center_id' => $center->id]);
                    }
                }
            });
        }
    }

    public function down(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            if (Schema::hasColumn('registrations', 'center_id')) {
                $table->dropConstrainedForeignId('center_id');
            }
        });
    }
};
