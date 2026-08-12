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
        Schema::create('vaccine_regimens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vaccine_id')->constrained('vaccines')->cascadeOnDelete();
            $table->string('age_group'); // Ví dụ: "9 - 14 tuổi", "Từ 15 tuổi trở lên"
            $table->integer('doses')->default(1); // Số mũi tiêm
            $table->text('schedule_description')->nullable(); // Phác đồ tiêm chi tiết
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vaccine_regimens');
    }
};
