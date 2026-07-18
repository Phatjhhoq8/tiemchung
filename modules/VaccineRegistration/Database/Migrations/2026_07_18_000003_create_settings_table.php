<?php
/**
 * Chức năng: Tạo bảng settings dạng key-value để quản lý các cấu hình động của hệ thống (hotline, hotline_2, address, email, footer...).
 * Lý do tạo: Loại bỏ dữ liệu tĩnh hardcode trên website, cho phép Admin tùy biến nội dung real-time từ CSDL.
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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
