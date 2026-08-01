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
        // 1. Create patients table
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('identity_card')->nullable()->index();
            $table->string('full_name');
            $table->date('dob');
            $table->string('gender');
            $table->string('phone')->index();
            $table->text('address')->nullable();
            $table->text('medical_history')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Add patient_id and screening fields to registrations table
        Schema::table('registrations', function (Blueprint $table) {
            $table->foreignId('patient_id')->nullable()->constrained('patients')->nullOnDelete();
            $table->string('screening_status')->nullable(); // 'eligible', 'deferred', 'contraindicated'
            $table->text('screening_notes')->nullable();
        });

        // 3. Create administered_doses table
        Schema::create('administered_doses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registration_id')->constrained('registrations')->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->foreignId('vaccine_id')->constrained('vaccines')->cascadeOnDelete();
            $table->foreignId('inventory_lot_id')->nullable()->constrained('inventory_lots')->nullOnDelete();
            $table->foreignId('center_id')->nullable()->constrained('centers')->nullOnDelete();
            $table->foreignId('administered_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('administered_at');
            $table->string('screening_status')->nullable();
            $table->text('screening_notes')->nullable();
            $table->text('observation_notes')->nullable();
            $table->dateTime('observation_ended_at')->nullable();
            $table->string('status')->default('completed');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('administered_doses');

        Schema::table('registrations', function (Blueprint $table) {
            $table->dropForeign(['patient_id']);
            $table->dropColumn(['patient_id', 'screening_status', 'screening_notes']);
        });

        Schema::dropIfExists('patients');
    }
};
