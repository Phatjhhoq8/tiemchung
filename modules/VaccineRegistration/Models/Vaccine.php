<?php
/**
 * Chức năng: Model Vaccine quản lý thông tin các loại vắc xin/gói vắc xin.
 * Lý do tạo/chỉnh sửa: Bổ sung các cột type (loại vắc xin), doses (số mũi tiêm) và các scope hỗ trợ truy vấn nhanh.
 */

namespace Modules\VaccineRegistration\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Vaccine extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'sale_price',       // Giá ưu đãi (nullable)
        'type',             // 'single' (lẻ) hoặc 'package' (gói)
        'doses',            // số mũi tiêm
        'stock_status',     // available, limited, out_of_stock
        'description',
        'disease_prevention',
        'category',         // Danh mục bệnh (VD: Cúm, HPV)
        'age_group',
        'origin',
        'manufacturer',     // Hãng sản xuất
        'dosage',           // Quy cách đóng gói
        'image',
        'is_featured',      // Vắc xin nổi bật
        'sort_order',       // Thứ tự hiển thị
        'views',            // Lượt xem vắc xin
    ];

    /**
     * Casts cho các trường đặc biệt
     */
    protected $casts = [
        'is_featured' => 'boolean',
        'sale_price' => 'integer',
        'price' => 'integer',
        'sort_order' => 'integer',
        'views' => 'integer',
    ];

    /**
     * Kiểm tra có đang giảm giá không
     */
    public function hasSalePrice(): bool
    {
        return !empty($this->sale_price) && $this->sale_price < $this->price;
    }

    /**
     * Lấy nhãn tình trạng kho
     */
    public function getStockLabel(): string
    {
        return match($this->stock_status) {
            'available' => 'Đầy đủ',
            'limited' => 'Còn ít',
            'out_of_stock' => 'Hết hàng',
            default => 'Đầy đủ',
        };
    }

    /**
     * Scope lọc vắc xin lẻ
     */
    public function scopeSingle($query)
    {
        return $query->where('type', 'single');
    }

    /**
     * Scope lọc gói vắc xin
     */
    public function scopePackage($query)
    {
        return $query->where('type', 'package');
    }

    /**
     * Scope lọc vắc xin nổi bật
     */
    public function scopeFeatured($query)
    {
        return $query->where($this->hasCenterVaccineJoin($query) ? 'center_vaccines.is_featured' : 'vaccines.is_featured', true);
    }

    /**
     * Lọc và lấy giá/trạng thái sản phẩm theo chi nhánh hiện tại.
     */
    public function scopeForCenter($query, ?int $centerId)
    {
        if (!$centerId) {
            return $query->whereRaw('1 = 0');
        }

        return $query->join('center_vaccines', 'vaccines.id', '=', 'center_vaccines.vaccine_id')
            ->where('center_vaccines.center_id', $centerId)
            ->where('center_vaccines.is_active', true)
            ->select(
                'vaccines.*',
                'center_vaccines.price as price',
                'center_vaccines.sale_price as sale_price',
                'center_vaccines.stock_quantity as stock_quantity',
                'center_vaccines.stock_status as stock_status',
                'center_vaccines.is_featured as is_featured',
                'center_vaccines.sort_order as sort_order'
            );
    }

    /**
     * Scope lọc theo tình trạng kho
     */
    public function scopeInStock($query)
    {
        return $query->where($this->hasCenterVaccineJoin($query) ? 'center_vaccines.stock_status' : 'vaccines.stock_status', '!=', 'out_of_stock');
    }

    private function hasCenterVaccineJoin($query): bool
    {
        foreach ($query->getQuery()->joins ?? [] as $join) {
            if ($join->table === 'center_vaccines') {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the registrations associated with the vaccine.
     */
    public function registrations()
    {
        return $this->belongsToMany(Registration::class, 'registration_vaccines')
                    ->withPivot('price')
                    ->withTimestamps();
    }

    public function centerVaccines()
    {
        return $this->hasMany(CenterVaccine::class);
    }
}
