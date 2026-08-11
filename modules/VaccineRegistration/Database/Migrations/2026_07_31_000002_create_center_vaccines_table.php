<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('center_vaccines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('center_id')->constrained('centers')->onDelete('cascade');
            $table->foreignId('vaccine_id')->constrained('vaccines')->onDelete('cascade');
            $table->unsignedInteger('price');
            $table->unsignedInteger('sale_price')->nullable();
            $table->unsignedInteger('stock_quantity')->default(0);
            $table->string('stock_status')->default('available');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['center_id', 'vaccine_id']);
        });

        if (Schema::hasTable('centers') && Schema::hasTable('vaccines')) {
            $centers = DB::table('centers')->pluck('id');
            $vaccines = DB::table('vaccines')->get();
            $now = now();

            foreach ($centers as $centerId) {
                foreach ($vaccines as $vaccine) {
                    DB::table('center_vaccines')->insertOrIgnore([
                        'center_id' => $centerId,
                        'vaccine_id' => $vaccine->id,
                        'price' => $vaccine->price,
                        'sale_price' => $vaccine->sale_price ?? null,
                        'stock_quantity' => 100,
                        'stock_status' => $vaccine->stock_status ?? 'available',
                        'is_active' => true,
                        'is_featured' => (bool) ($vaccine->is_featured ?? false),
                        'sort_order' => $vaccine->sort_order ?? 0,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('center_vaccines');
    }
};
