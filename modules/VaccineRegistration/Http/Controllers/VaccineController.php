<?php
/**
 * Chức năng: VaccineController xử lý danh mục vắc xin, giỏ hàng và quy trình đăng ký tiêm chủng của khách hàng.
 * Lý do chỉnh sửa: Bổ sung 8 vắc xin liên quan phục vụ cuộn trượt Slider có mũi tên điều hướng trên trang chi tiết.
 */

namespace Modules\VaccineRegistration\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\VaccineRegistration\Models\Vaccine;
use Modules\VaccineRegistration\Models\Registration;
use Modules\VaccineRegistration\Models\Center;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class VaccineController extends Controller
{
    /**
     * Hiển thị danh mục sản phẩm tiêm chủng với các bộ lọc động từ CSDL.
     */
    public function index(Request $request)
    {
        $query = Vaccine::query();
        $type = $request->input('type');

        // Tìm kiếm theo tên sản phẩm. Lọc theo bệnh dùng tham số disease riêng.
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', '%' . $search . '%');
        }

        if ($request->filled('disease')) {
            $disease = $request->input('disease');
            $query->where(function ($q) use ($disease) {
                $q->where('disease_prevention', 'like', '%' . $disease . '%')
                    ->orWhere('category', 'like', '%' . $disease . '%');
            });
        }

        // Lọc theo nhóm tuổi chỉ định
        if ($request->filled('age_group')) {
            $query->where('age_group', 'like', '%' . $request->input('age_group') . '%');
        }

        if ($request->filled('origin')) {
            $query->where('origin', $request->input('origin'));
        }

        if ($request->filled('doses')) {
            $query->where('doses', (int) $request->input('doses'));
        }

        if (in_array($type, ['single', 'package'], true)) {
            $query->where('type', $type);
        }

        $sort = $request->input('sort', 'popular');
        match ($sort) {
            'price_asc' => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'name_asc' => $query->orderBy('name', 'asc'),
            default => $query->orderBy('views', 'desc')->orderBy('id', 'asc'),
        };

        $vaccines = $query->paginate(12)->withQueryString();
        $cart = session()->get('cart', []);

        $allVaccines = Vaccine::all();
        $diseaseOptions = $this->buildDiseaseOptions($allVaccines);
        $diseases = $diseaseOptions;
        
        $ageGroupOptions = $this->buildAgeGroupOptions($allVaccines);
        $ageGroups = $ageGroupOptions;

        $originOptions = $this->buildOriginOptions($allVaccines);
        $origins = $originOptions;

        $doseOptions = [1, 2, 3, 4];
        $doses = $doseOptions;

        $productCategories = $this->buildProductCategories($allVaccines);

        return view('vaccine::index', compact(
            'vaccines',
            'cart',
            'diseaseOptions',
            'diseases',
            'ageGroupOptions',
            'ageGroups',
            'originOptions',
            'origins',
            'doseOptions',
            'doses',
            'productCategories'
        ));
    }

    private function buildDiseaseOptions($vaccines)
    {
        return $vaccines
            ->flatMap(function ($vaccine) {
                $items = [];
                if (!empty($vaccine->category)) {
                    $items[] = trim($vaccine->category);
                }

                if (!empty($vaccine->disease_prevention)) {
                    $parts = preg_split('/[,;\-\/]+/', $vaccine->disease_prevention);
                    foreach ($parts as $part) {
                        $cleaned = trim($part);
                        if (!empty($cleaned)) {
                            $items[] = $cleaned;
                        }
                    }
                }

                return $items;
            })
            ->filter()
            ->unique()
            ->sort()
            ->values();
    }

    private function buildAgeGroupOptions($vaccines)
    {
        return $vaccines
            ->pluck('age_group')
            ->filter()
            ->map(fn ($item) => trim($item))
            ->unique()
            ->sort()
            ->values();
    }

    private function buildOriginOptions($vaccines)
    {
        return $vaccines
            ->pluck('origin')
            ->filter()
            ->map(fn ($item) => trim($item))
            ->unique()
            ->sort()
            ->values();
    }

    private function buildProductCategories($vaccines)
    {
        return $vaccines
            ->filter(fn ($vaccine) => !empty($vaccine->category) || !empty($vaccine->disease_prevention))
            ->groupBy(fn ($vaccine) => $vaccine->category ?: $this->buildDiseaseOptions(collect([$vaccine]))->first())
            ->map(function ($items, $name) {
                $first = $items->first();

                return [
                    'name' => $name,
                    'count' => $items->count(),
                    'image' => $first?->image,
                ];
            })
            ->sortBy('name')
            ->values();
    }

    /**
     * Hiển thị trang chi tiết một loại vắc xin.
     */
    public function show(Request $request, $id)
    {
        $vaccine = Vaccine::findOrFail($id);
        
        // Tăng số lượt xem sản phẩm khi xem chi tiết
        $vaccine->increment('views');
        
        $cart = session()->get('cart', []);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'vaccine' => [
                    'id' => $vaccine->id,
                    'name' => $vaccine->name,
                    'price' => $vaccine->price,
                    'formatted_price' => number_format($vaccine->price, 0, ',', '.') . ' đ',
                    'type' => $vaccine->type,
                    'type_label' => $vaccine->type === 'package' ? 'Gói vắc xin' : 'Vắc xin lẻ',
                    'doses' => $vaccine->doses,
                    'disease_prevention' => $vaccine->disease_prevention,
                    'age_group' => $vaccine->age_group,
                    'origin' => $vaccine->origin,
                    'manufacturer' => $vaccine->manufacturer,
                    'dosage' => $vaccine->dosage,
                    'description' => $vaccine->description,
                    'image' => asset('images/vaccines/' . ($vaccine->image ?: 'hexaxim.jpg')),
                    'is_in_cart' => isset($cart[$vaccine->id]),
                    'views' => $vaccine->views,
                    'formatted_views' => number_format($vaccine->views, 0, ',', '.') . ' lượt xem',
                ]
            ]);
        }

        // Lấy 8 vắc xin liên quan cùng phòng bệnh hoặc cùng xuất xứ
        $relatedVaccines = Vaccine::where('id', '!=', $vaccine->id)
            ->where(function ($q) use ($vaccine) {
                $q->where('disease_prevention', 'like', '%' . $vaccine->disease_prevention . '%')
                  ->orWhere('origin', $vaccine->origin);
            })
            ->take(8)
            ->get();

        if ($relatedVaccines->count() < 8) {
            $existingIds = $relatedVaccines->pluck('id')->push($vaccine->id)->toArray();
            $extraVaccines = Vaccine::whereNotIn('id', $existingIds)->take(8 - $relatedVaccines->count())->get();
            $relatedVaccines = $relatedVaccines->concat($extraVaccines);
        }

        return view('vaccine::show', compact('vaccine', 'cart', 'relatedVaccines'));
    }
}
