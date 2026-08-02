<?php

namespace Modules\VaccineRegistration\Support;

use Illuminate\Support\Collection;
use Modules\VaccineRegistration\Models\Center;
use Modules\VaccineRegistration\Models\CenterVaccine;
use Modules\VaccineRegistration\Models\Vaccine;

class CenterContext
{
    public const SESSION_KEY = 'selected_center_id';
    private const ACTIVE_CENTERS_CACHE_KEY = '_center_context.active_centers';
    private const CURRENT_CENTER_CACHE_KEY = '_center_context.current_center';

    public static function activeCenters(): Collection
    {
        $request = app()->bound('request') ? app('request') : null;
        if ($request?->attributes->has(self::ACTIVE_CENTERS_CACHE_KEY)) {
            return $request->attributes->get(self::ACTIVE_CENTERS_CACHE_KEY);
        }

        $centers = Center::active()->orderBy('sort_order')->orderBy('id')->get();
        $request?->attributes->set(self::ACTIVE_CENTERS_CACHE_KEY, $centers);

        return $centers;
    }

    public static function current(): ?Center
    {
        $request = app()->bound('request') ? app('request') : null;
        if ($request?->attributes->has(self::CURRENT_CENTER_CACHE_KEY)) {
            return $request->attributes->get(self::CURRENT_CENTER_CACHE_KEY);
        }

        $centerId = session(self::SESSION_KEY);

        $center = $centerId
            ? self::activeCenters()->firstWhere('id', (int) $centerId)
            : self::activeCenters()->first();
        if ($center) {
            session([self::SESSION_KEY => $center->id]);
        }

        $request?->attributes->set(self::CURRENT_CENTER_CACHE_KEY, $center);

        return $center;
    }

    public static function set(int $centerId): ?Center
    {
        $center = self::activeCenters()->firstWhere('id', $centerId);
        abort_unless($center, 404);
        session([self::SESSION_KEY => $center->id]);
        if (app()->bound('request')) {
            app('request')->attributes->set(self::CURRENT_CENTER_CACHE_KEY, $center);
        }

        return $center;
    }

    public static function resolveCart(?int $centerId = null): array
    {
        $cart = session()->get('cart', []);
        $centerId = $centerId ?: self::current()?->id;

        $request = app()->bound('request') ? app('request') : null;
        $cacheKey = '_center_context.cart.' . ($centerId ?: 'none') . '.' . md5(json_encode($cart));
        if ($request?->attributes->has($cacheKey)) {
            return $request->attributes->get($cacheKey);
        }

        if (!$centerId || empty($cart)) {
            $state = ['cart' => $cart, 'total_price' => 0, 'unavailable_count' => 0];
            $request?->attributes->set($cacheKey, $state);

            return $state;
        }

        $vaccineIds = array_map('intval', array_keys($cart));
        $vaccines = Vaccine::whereIn('id', $vaccineIds)->get()->keyBy('id');
        $centerVaccines = CenterVaccine::where('center_id', $centerId)
            ->whereIn('vaccine_id', $vaccineIds)
            ->where('is_active', true)
            ->where('stock_status', '!=', 'out_of_stock')
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

        $state = ['cart' => $resolved, 'total_price' => $total, 'unavailable_count' => $unavailable];
        $request?->attributes->set($cacheKey, $state);

        return $state;
    }

    public static function phoneHref(?string $phone): string
    {
        return preg_replace('/\D+/', '', (string) $phone);
    }
}
