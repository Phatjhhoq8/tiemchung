<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vaccines', function (Blueprint $table) {
            $table->string('administration_route')->nullable()->after('dosage');
            $table->text('detailed_schedule')->nullable()->after('administration_route');
            $table->text('contraindications')->nullable()->after('detailed_schedule');
            $table->text('adverse_effects')->nullable()->after('contraindications');
            $table->text('warnings')->nullable()->after('adverse_effects');
            $table->string('source_reference_url', 2048)->nullable()->after('warnings');
            $table->date('source_review_date')->nullable()->after('source_reference_url');
        });
    }

    public function down(): void
    {
        Schema::table('vaccines', function (Blueprint $table) {
            $table->dropColumn([
                'administration_route',
                'detailed_schedule',
                'contraindications',
                'adverse_effects',
                'warnings',
                'source_reference_url',
                'source_review_date',
            ]);
        });
    }
};
