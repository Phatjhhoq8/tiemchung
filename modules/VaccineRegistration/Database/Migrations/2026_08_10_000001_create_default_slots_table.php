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
        Schema::create('default_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('center_id')->constrained('centers')->cascadeOnDelete();
            $table->unsignedTinyInteger('day_of_week'); // 1 = Thứ 2, ..., 7 = Chủ nhật
            $table->string('start_at');
            $table->string('end_at');
            $table->integer('capacity')->default(10);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['center_id', 'day_of_week', 'start_at', 'end_at'], 'default_slots_unique_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('default_slots');
    }
};
