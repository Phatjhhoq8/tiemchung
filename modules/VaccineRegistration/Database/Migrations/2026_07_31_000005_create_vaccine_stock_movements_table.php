<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vaccine_stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('center_id')->constrained('centers')->onDelete('cascade');
            $table->foreignId('vaccine_id')->constrained('vaccines')->onDelete('cascade');
            $table->string('type'); // import, sale, adjustment
            $table->integer('quantity');
            $table->unsignedInteger('unit_price')->default(0);
            $table->text('note')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['center_id', 'type']);
            $table->index(['vaccine_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vaccine_stock_movements');
    }
};
