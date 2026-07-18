<?php
/**
 * Chức năng: Thêm cột type và doses vào bảng vaccines để phân loại vắc xin và xác định số mũi tiêm.
 * Lý do tạo: Tích hợp phân chia danh mục vắc xin lẻ / gói vắc xin và lưu phác đồ tiêm chủng từ yêu cầu bổ sung.
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
            $table->string('type')->default('single')->after('price'); // 'single' (lẻ) hoặc 'package' (gói)
            $table->unsignedTinyInteger('doses')->default(1)->after('type'); // số mũi tiêm
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vaccines', function (Blueprint $table) {
            $table->dropColumn(['type', 'doses']);
        });
    }
};
