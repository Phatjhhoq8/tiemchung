<?php
/**
 * Chức năng: Thêm các trường quản lý sản phẩm nâng cao vào bảng vaccines.
 * Lý do tạo: Tham khảo VNVC, bổ sung giá ưu đãi, tình trạng kho, hãng sản xuất, quy cách, 
 *            đánh dấu nổi bật, thứ tự hiển thị và danh mục bệnh.
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
            $table->unsignedInteger('sale_price')->nullable()->after('price'); // Giá ưu đãi
            $table->string('stock_status')->default('available')->after('doses'); // available, limited, out_of_stock
            $table->string('manufacturer')->nullable()->after('origin'); // Hãng sản xuất (VD: GlaxoSmithKline)
            $table->string('dosage')->nullable()->after('manufacturer'); // Quy cách đóng gói (VD: 0.5ml)
            $table->boolean('is_featured')->default(false)->after('image'); // Vắc xin nổi bật
            $table->unsignedInteger('sort_order')->default(0)->after('is_featured'); // Thứ tự hiển thị
            $table->string('category')->nullable()->after('disease_prevention'); // Danh mục bệnh (VD: Cúm, HPV)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vaccines', function (Blueprint $table) {
            $table->dropColumn([
                'sale_price', 'stock_status', 'manufacturer', 'dosage',
                'is_featured', 'sort_order', 'category'
            ]);
        });
    }
};
