<?php
/**
 * Chức năng: Thêm số mũi tiêm theo phác đồ vào bảng vaccines.
 */

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
        Schema::table('vaccines', function (Blueprint $table) {
            $table->unsignedTinyInteger('doses')->default(1)->after('price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $columns = array_values(array_filter(
            ['type', 'doses'],
            fn (string $column) => Schema::hasColumn('vaccines', $column)
        ));

        if ($columns) {
            Schema::table('vaccines', function (Blueprint $table) use ($columns) {
                $table->dropColumn($columns);
            });
        }
    }
};
