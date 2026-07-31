<?php

namespace Modules\VaccineRegistration\Support;

use Illuminate\Support\Collection;
use Modules\VaccineRegistration\Models\Center;
use Modules\VaccineRegistration\Models\CenterVaccine;
use Modules\VaccineRegistration\Models\Vaccine;

class CenterContext
{
    public const SESSION_KEY = 'selected_center_id';

    public static function activeCenters(): Collection
    {
        return Center::active()->orderBy('sort_order')->orderBy('id')->get();
    }

    public static function current(): ?Center
    {
        $centerId = session(self::SESSION_KEY);

        if ($centerId) {
            $center = Center::active()->find($centerId);
            if ($center) {
                return $center;
            }
        }

        $center = Center::active()->orderBy('sort_order')->orderBy('id')->first();
        if ($center) {
            session([self::SESSION_KEY => $center->id]);
        }

        return $center;
    }

    public static function set(int $centerId): ?Center
    {
        $center = Center::active()->findOrFail($centerId);
        session([self::SESSION_KEY => $center->id]);

        return $center;
    }

    public static function resolveCart(?int $centerId = null): array
    {
        $cart = session()->get('cart', []);
        $centerId = $centerId ?: self::current()?->id;

        if (!$centerId || empty($cart)) {
            return ['cart' => $cart, 'total_price' => 0, 'unavailable_count' => 0];
        }

        $vaccineIds = array_map('intval', array_keys($cart));
        $vaccines = Vaccine::whereIn('id', $vaccineIds)->get()->keyBy('id');
        $centerVaccines = CenterVaccine::where('center_id', $centerId)
            ->whereIn('vaccine_id', $vaccineIds)
            ->where('is_active', true)
            ->get()
            ->keyBy('vaccine_id');

        $resolved = [];
        $total = 0;
        $unavailable = 0;

        foreach ($cart as $id => $item) {
            $vaccine = $vaccines->get((int) $id);
            $centerVaccine = $centerVaccines->get((int) $id);
            $quantity = max(1, (int) ($item['quantity'] ?? 1));
            $price = $centerVaccine ? ($centerVaccine->hasSalePrice() ? $centerVaccine->sale_price : $centerVaccine->price) : 0;
            $isUnavailable = !$centerVaccine;

            if ($isUnavailable) {
                $unavailable++;
            } else {
                $total += $price * $quantity;
            }

            $resolved[$id] = [
                'name' => $vaccine?->name ?? ($item['name'] ?? 'Sản phẩm không xác định'),
                'price' => $price,
                'image' => $vaccine?->image ?? ($item['image'] ?? 'hexaxim.jpg'),
                'quantity' => $quantity,
                'type' => $vaccine?->type ?? ($item['type'] ?? 'single'),
                'disease_prevention' => $vaccine?->disease_prevention ?? ($item['disease_prevention'] ?? ''),
                'unavailable_for_center' => $isUnavailable,
            ];
        }

        return ['cart' => $resolved, 'total_price' => $total, 'unavailable_count' => $unavailable];
    }

    public static function phoneHref(?string $phone): string
    {
        return preg_replace('/\D+/', '', (string) $phone);
    }
}
