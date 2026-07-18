<?php
/**
 * Chức năng: Tạo bảng centers lưu danh sách các trung tâm tiêm chủng Medicare Cờ Đỏ.
 * Lý do tạo: Chuyển đổi dữ liệu danh sách trung tâm tĩnh sang động (CSDL), hỗ trợ quản lý qua trang Admin.
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
        Schema::create('centers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('address');
            $table->string('phone')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('centers');
    }
};
