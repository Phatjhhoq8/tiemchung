<?php

namespace Modules\VaccineRegistration\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AuditLogger;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\VaccineRegistration\Models\Setting;
use Modules\VaccineRegistration\Support\AdminContext;

class AdminLoyaltySettingController extends Controller
{
    private function resolveLoyaltyContext(): array
    {
        $isSuperAdmin = AdminContext::isSuperAdmin();
        $isBranchAdmin = AdminContext::isBranchAdmin();

        if ($isBranchAdmin) {
            $centerId = AdminContext::user()->center_id;
        } else {
            $centerId = AdminContext::selectedCenterId();
        }

        return [$centerId, $isSuperAdmin];
    }

    /**
     * Hiển thị giao diện cấu hình tích điểm.
     */
    public function index()
    {
        [$centerId, $isSuperAdmin] = $this->resolveLoyaltyContext();

        $defaults = [
            'use_system_settings' => true,
            'synced_system_at' => null,
            'enabled' => true,
            'vnd_per_earned_point' => 10000,
            'min_order_value_to_earn' => 0,
            'min_order_value_to_redeem' => 0,
            'point_expiry_months' => 0,
            'redeem_value_type' => 'vnd',
            'redeem_vnd_per_point' => 100,
            'redeem_percent_bps_per_point' => 10,
            'max_redeem_percent' => 50,
            'max_redeem_amount' => null,
            'birthday_multiplier' => 1.0,
            'tiers' => [],
            'campaigns' => [],
        ];

        // Lấy cấu hình hệ thống
        $systemSettingModel = Setting::where('key', 'loyalty_settings')->first();
        $systemSettings = $defaults;
        if ($systemSettingModel && $systemSettingModel->value) {
            $decoded = json_decode($systemSettingModel->value, true);
            if (is_array($decoded)) {
                if (isset($decoded['redeem_point_value']) && !isset($decoded['redeem_vnd_per_point'])) {
                    $decoded['redeem_vnd_per_point'] = $decoded['redeem_point_value'];
                }
                $systemSettings = array_replace($defaults, $decoded);
            }
        }

        $settings = $systemSettings;
        $hasSyncWarning = false;
        $systemUpdatedAt = $systemSettingModel ? $systemSettingModel->updated_at : null;

        if ($centerId) {
            $centerJson = Setting::get('loyalty_settings_center_' . $centerId);
            if ($centerJson) {
                $centerSettings = json_decode($centerJson, true);
                if (is_array($centerSettings)) {
                    if (isset($centerSettings['redeem_point_value']) && !isset($centerSettings['redeem_vnd_per_point'])) {
                        $centerSettings['redeem_vnd_per_point'] = $centerSettings['redeem_point_value'];
                    }
                    $settings = array_replace($defaults, $centerSettings);

                    // Kiểm tra cảnh báo lệch cấu hình
                    if ($systemUpdatedAt && !empty($settings['synced_system_at']) && !$settings['use_system_settings']) {
                        $syncedTime = Carbon::parse($settings['synced_system_at']);
                        if ($systemUpdatedAt->gt($syncedTime)) {
                            $hasSyncWarning = true;
                        }
                    }
                }
            } else {
                $settings = $defaults;
                $settings['use_system_settings'] = true;
            }
        }

        return view('vaccine::admin.settings.loyalty', compact('settings', 'centerId', 'isSuperAdmin', 'hasSyncWarning', 'systemUpdatedAt'));
    }

    /**
     * Cập nhật cấu hình tích điểm.
     */
    public function update(Request $request)
    {
        [$centerId, $isSuperAdmin] = $this->resolveLoyaltyContext();

        // Nếu là cấu hình chi nhánh và chọn sử dụng cấu hình hệ thống
        if ($centerId && $request->boolean('use_system_settings')) {
            return DB::transaction(function () use ($centerId) {
                $oldJson = Setting::get('loyalty_settings_center_' . $centerId);
                $oldData = $oldJson ? json_decode($oldJson, true) : [];

                $systemSettingModel = Setting::where('key', 'loyalty_settings')->first();
                $systemUpdatedAt = $systemSettingModel ? $systemSettingModel->updated_at : now();

                $newData = [
                    'use_system_settings' => true,
                    'synced_system_at' => $systemUpdatedAt->toDateTimeString(),
                ];

                Setting::set('loyalty_settings_center_' . $centerId, json_encode($newData, JSON_UNESCAPED_UNICODE));

                AuditLogger::log(
                    action: 'loyalty_settings.center_reset_to_system',
                    resourceType: 'setting',
                    resourceId: 'loyalty_center_' . $centerId,
                    oldValues: $oldData,
                    newValues: $newData,
                    resolveCenter: false
                );

                return redirect()->route('admin.settings.loyalty')->with('success', 'Đã chuyển chi nhánh sang sử dụng cấu hình toàn hệ thống.');
            });
        }

        // Validate các cấu hình tích điểm
        $validated = $request->validate([
            'enabled' => 'required|boolean',
            'vnd_per_earned_point' => 'required|integer|min:1',
            'min_order_value_to_earn' => 'required|integer|min:0',
            'min_order_value_to_redeem' => 'required|integer|min:0',
            'point_expiry_months' => 'required|integer|min:0',
            'redeem_value_type' => 'required|in:vnd,percent',
            'redeem_vnd_per_point' => 'required|integer|min:0',
            'redeem_percent_bps_per_point' => 'required|integer|min:0',
            'max_redeem_percent' => 'required|integer|between:1,100',
            'max_redeem_amount' => 'nullable|integer|min:0',
            'birthday_multiplier' => 'required|numeric|min:1',
            'tiers' => 'nullable|array',
            'tiers.*.name' => 'required|string|max:100',
            'tiers.*.min_points' => 'required|integer|min:0',
            'tiers.*.multiplier' => 'required|numeric|min:1',
            'campaigns' => 'nullable|array',
            'campaigns.*.name' => 'required|string|max:100',
            'campaigns.*.start_date' => 'required|date',
            'campaigns.*.end_date' => 'required|date|after_or_equal:campaigns.*.start_date',
            'campaigns.*.multiplier' => 'required|numeric|min:1',
        ], [
            'vnd_per_earned_point.min' => 'Số tiền tích điểm tối thiểu phải từ 1đ.',
            'redeem_vnd_per_point.min' => 'Giá trị quy đổi điểm phải lớn hơn hoặc bằng 0.',
            'max_redeem_percent.between' => 'Phần trăm giảm tối đa phải nằm trong khoảng 1 - 100%.',
        ]);

        if (!empty($validated['tiers'])) {
            usort($validated['tiers'], function ($a, $b) {
                return (int)$a['min_points'] <=> (int)$b['min_points'];
            });
        } else {
            $validated['tiers'] = [];
        }

        if (empty($validated['campaigns'])) {
            $validated['campaigns'] = [];
        }

        return DB::transaction(function () use ($centerId, $validated) {
            if ($centerId) {
                $validated['use_system_settings'] = false;
                
                $systemSettingModel = Setting::where('key', 'loyalty_settings')->first();
                $validated['synced_system_at'] = $systemSettingModel ? $systemSettingModel->updated_at->toDateTimeString() : now()->toDateTimeString();

                $oldJson = Setting::get('loyalty_settings_center_' . $centerId);
                $oldSettings = $oldJson ? json_decode($oldJson, true) : [];

                Setting::set('loyalty_settings_center_' . $centerId, json_encode($validated, JSON_UNESCAPED_UNICODE));

                AuditLogger::log(
                    action: 'loyalty_settings.center_updated',
                    resourceType: 'setting',
                    resourceId: 'loyalty_center_' . $centerId,
                    oldValues: $oldSettings,
                    newValues: $validated,
                    resolveCenter: false
                );
            } else {
                $oldJson = Setting::get('loyalty_settings');
                $oldSettings = $oldJson ? json_decode($oldJson, true) : [];

                Setting::set('loyalty_settings', json_encode($validated, JSON_UNESCAPED_UNICODE));

                AuditLogger::log(
                    action: 'loyalty_settings.updated',
                    resourceType: 'setting',
                    resourceId: 'loyalty',
                    oldValues: $oldSettings,
                    newValues: $validated,
                    resolveCenter: false
                );
            }

            return redirect()->route('admin.settings.loyalty')->with('success', 'Cập nhật cấu hình tích điểm thành công.');
        });
    }

    /**
     * Đồng bộ cấu hình hệ thống sang chi nhánh (toàn bộ hoặc từng phần).
     */
    public function syncSettings(Request $request)
    {
        [$centerId] = $this->resolveLoyaltyContext();
        if (!$centerId) {
            return back()->with('error', 'Chỉ có thể đồng bộ cấu hình khi đang trong ngữ cảnh chi nhánh.');
        }

        return DB::transaction(function () use ($centerId, $request) {
            $systemSettingModel = Setting::where('key', 'loyalty_settings')->lockForUpdate()->first();
            if (!$systemSettingModel || !$systemSettingModel->value) {
                return back()->with('error', 'Chưa có cấu hình toàn hệ thống để đồng bộ.');
            }

            $systemSettings = json_decode($systemSettingModel->value, true);
            if (!is_array($systemSettings)) {
                return back()->with('error', 'Cấu hình hệ thống không hợp lệ.');
            }

            $defaults = [
                'use_system_settings' => false,
                'synced_system_at' => null,
                'enabled' => true,
                'vnd_per_earned_point' => 10000,
                'min_order_value_to_earn' => 0,
                'min_order_value_to_redeem' => 0,
                'point_expiry_months' => 0,
                'redeem_value_type' => 'vnd',
                'redeem_vnd_per_point' => 100,
                'redeem_percent_bps_per_point' => 10,
                'max_redeem_percent' => 50,
                'max_redeem_amount' => null,
                'birthday_multiplier' => 1.0,
                'tiers' => [],
                'campaigns' => [],
            ];

            $centerJson = Setting::get('loyalty_settings_center_' . $centerId);
            $centerSettings = $centerJson ? json_decode($centerJson, true) : $defaults;

            $syncFields = $request->input('sync_fields', []);
            
            if (empty($syncFields)) {
                foreach ($defaults as $key => $val) {
                    if ($key !== 'use_system_settings' && $key !== 'synced_system_at') {
                        $centerSettings[$key] = $systemSettings[$key] ?? $val;
                    }
                }
            } else {
                if (in_array('basic', $syncFields)) {
                    $centerSettings['enabled'] = $systemSettings['enabled'] ?? true;
                    $centerSettings['vnd_per_earned_point'] = $systemSettings['vnd_per_earned_point'] ?? 10000;
                    $centerSettings['min_order_value_to_earn'] = $systemSettings['min_order_value_to_earn'] ?? 0;
                    $centerSettings['point_expiry_months'] = $systemSettings['point_expiry_months'] ?? 0;
                }

                if (in_array('redeem', $syncFields)) {
                    $centerSettings['min_order_value_to_redeem'] = $systemSettings['min_order_value_to_redeem'] ?? 0;
                    $centerSettings['redeem_value_type'] = $systemSettings['redeem_value_type'] ?? 'vnd';
                    $centerSettings['redeem_vnd_per_point'] = $systemSettings['redeem_vnd_per_point'] ?? 100;
                    $centerSettings['redeem_percent_bps_per_point'] = $systemSettings['redeem_percent_bps_per_point'] ?? 10;
                    $centerSettings['max_redeem_percent'] = $systemSettings['max_redeem_percent'] ?? 50;
                    $centerSettings['max_redeem_amount'] = $systemSettings['max_redeem_amount'] ?? null;
                }

                if (in_array('tiers', $syncFields)) {
                    $centerSettings['tiers'] = $systemSettings['tiers'] ?? [];
                }

                if (in_array('campaigns', $syncFields)) {
                    $centerSettings['campaigns'] = $systemSettings['campaigns'] ?? [];
                }

                if (in_array('birthday', $syncFields)) {
                    $centerSettings['birthday_multiplier'] = $systemSettings['birthday_multiplier'] ?? 1.0;
                }
            }

            $centerSettings['use_system_settings'] = false;
            $centerSettings['synced_system_at'] = $systemSettingModel->updated_at->toDateTimeString();

            Setting::set('loyalty_settings_center_' . $centerId, json_encode($centerSettings, JSON_UNESCAPED_UNICODE));

            AuditLogger::log(
                action: 'loyalty_settings.center_synced',
                resourceType: 'setting',
                resourceId: 'loyalty_center_' . $centerId,
                oldValues: $centerJson ? json_decode($centerJson, true) : [],
                newValues: $centerSettings,
                resolveCenter: false
            );

            return redirect()->route('admin.settings.loyalty')->with('success', 'Đồng bộ cấu hình tích điểm thành công.');
        });
    }

    /**
     * Từ chối đồng bộ: Chỉ cập nhật synced_system_at để ẩn cảnh báo lệch.
     */
    public function rejectSync()
    {
        [$centerId] = $this->resolveLoyaltyContext();
        if (!$centerId) {
            return back()->with('error', 'Chỉ áp dụng trong ngữ cảnh chi nhánh.');
        }

        return DB::transaction(function () use ($centerId) {
            $systemSettingModel = Setting::where('key', 'loyalty_settings')->lockForUpdate()->first();
            if (!$systemSettingModel) {
                return back()->with('error', 'Chưa có cấu hình toàn hệ thống.');
            }

            $centerJson = Setting::get('loyalty_settings_center_' . $centerId);
            if (!$centerJson) {
                return back();
            }

            $centerSettings = json_decode($centerJson, true);
            if (is_array($centerSettings)) {
                $centerSettings['synced_system_at'] = $systemSettingModel->updated_at->toDateTimeString();
                Setting::set('loyalty_settings_center_' . $centerId, json_encode($centerSettings, JSON_UNESCAPED_UNICODE));
            }

            return redirect()->route('admin.settings.loyalty')->with('success', 'Đã ghi nhận giữ nguyên cấu hình riêng biệt của chi nhánh.');
        });
    }
}
