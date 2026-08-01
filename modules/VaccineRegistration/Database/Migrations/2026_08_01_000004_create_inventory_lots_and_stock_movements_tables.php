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
        Schema::create('inventory_lots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vaccine_id')->constrained('vaccines')->onDelete('cascade');
            $table->foreignId('center_id')->constrained('centers')->onDelete('cascade');
            $table->string('lot_number');
            $table->integer('initial_quantity');
            $table->integer('available_quantity');
            $table->integer('reserved_quantity')->default(0);
            $table->dateTime('expires_at');
            $table->string('status')->default('active');
            $table->timestamps();
        });

        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_lot_id')->constrained('inventory_lots')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('type');
            $table->integer('quantity');
            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->string('note')->nullable();
            $table->timestamps();
        });

        Schema::table('registration_vaccines', function (Blueprint $table) {
            if (!Schema::hasColumn('registration_vaccines', 'inventory_lot_id')) {
                $table->foreignId('inventory_lot_id')->nullable()->constrained('inventory_lots')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('registration_vaccines', function (Blueprint $table) {
            if (Schema::hasColumn('registration_vaccines', 'inventory_lot_id')) {
                $table->dropForeign(['inventory_lot_id']);
                $table->dropColumn('inventory_lot_id');
            }
        });

        Schema::dropIfExists('stock_movements');
        Schema::dropIfExists('inventory_lots');
    }
};
