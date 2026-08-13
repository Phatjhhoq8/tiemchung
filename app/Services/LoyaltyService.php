<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Modules\VaccineRegistration\Models\Customer;
use Modules\VaccineRegistration\Models\PointTransaction;
use Modules\VaccineRegistration\Models\Registration;
use Modules\VaccineRegistration\Models\Setting;
use App\Models\User;

class LoyaltyService
{
    /**
     * Lấy cấu hình tích điểm áp dụng cho chi nhánh hoặc hệ thống.
     */
    public function getLoyaltySettings(?int $centerId = null): array
    {
        $defaults = [
            'use_system_settings' => true,
            'synced_system_at' => null,
            'enabled' => true,
            'vnd_per_earned_point' => 1000,
            'min_order_value_to_earn' => 0,
            'min_order_value_to_redeem' => 0,
            'point_expiry_months' => 0, // 0 = Vô hạn
            'redeem_value_type' => 'vnd', // 'vnd' hoặc 'percent'
            'redeem_vnd_per_point' => 1,
            'redeem_percent_bps_per_point' => 10, // 10 basis points = 0.1% đơn hàng
            'max_redeem_percent' => 50,
            'max_redeem_amount' => null,
            'birthday_multiplier' => 1.0,
            'tiers' => [],
            'campaigns' => [],
        ];

        $systemJson = Setting::get('loyalty_settings');
        $systemSettings = $defaults;
        if ($systemJson) {
            $data = json_decode($systemJson, true);
            if (is_array($data)) {
                // Tương thích ngược nếu còn lưu key cũ 'redeem_point_value'
                if (isset($data['redeem_point_value']) && !isset($data['redeem_vnd_per_point'])) {
                    $data['redeem_vnd_per_point'] = $data['redeem_point_value'];
                }
                $systemSettings = array_replace($defaults, $data);
            }
        }

        if ($centerId) {
            $centerJson = Setting::get('loyalty_settings_center_' . $centerId);
            if ($centerJson) {
                $centerSettings = json_decode($centerJson, true);
                if (is_array($centerSettings) && isset($centerSettings['use_system_settings']) && !$centerSettings['use_system_settings']) {
                    if (isset($centerSettings['redeem_point_value']) && !isset($centerSettings['redeem_vnd_per_point'])) {
                        $centerSettings['redeem_vnd_per_point'] = $centerSettings['redeem_point_value'];
                    }
                    return array_replace($defaults, $centerSettings);
                }
            }
        }

        return $systemSettings;
    }

    /**
     * Tính số dư khả dụng dựa trên allocations và thời gian hết hạn (FIFO).
     */
    public function calculateAvailablePoints(Customer $customer, ?array $settings = null): int
    {
        $credits = PointTransaction::where('customer_id', $customer->id)
            ->whereIn('type', [PointTransaction::EARN, PointTransaction::ADJUSTMENT])
            ->where('points', '>', 0)
            ->where(function ($query) {
                $query->whereNull('expired_at')
                      ->orWhere('expired_at', '>', now('Asia/Ho_Chi_Minh'));
            })
            ->get();

        $available = 0;
        foreach ($credits as $c) {
            $allocated = DB::table('point_allocations')
                ->where('credit_transaction_id', $c->id)
                ->sum('points');
            
            $remaining = max(0, $c->points - $allocated);
            $available += $remaining;
        }

        return (int) $available;
    }

    /**
     * Tính toán số điểm tối đa có thể quy đổi cho đơn hàng (Quote).
     */
    public function quote(Customer $customer, Registration $registration, ?array $settings = null): array
    {
        $settings = $settings ?? $this->getLoyaltySettings($registration->center_id);
        
        $availablePoints = $this->calculateAvailablePoints($customer, $settings);

        if (!$settings['enabled']) {
            return [
                'balance' => $availablePoints,
                'maximum_points' => 0,
                'available_points' => 0,
            ];
        }

        if ((int)$registration->total_price < (int)$settings['min_order_value_to_redeem']) {
            return [
                'balance' => $availablePoints,
                'maximum_points' => 0,
                'available_points' => 0,
            ];
        }

        $maxDiscountPercent = (int)$settings['max_redeem_percent'];
        $maxDiscountByPercent = ((int)$registration->total_price * $maxDiscountPercent) / 100;
        $maxDiscountAmount = $maxDiscountByPercent;
        
        if (isset($settings['max_redeem_amount']) && $settings['max_redeem_amount'] !== null && $settings['max_redeem_amount'] !== '' && (int)$settings['max_redeem_amount'] > 0) {
            $maxDiscountAmount = min($maxDiscountByPercent, (int)$settings['max_redeem_amount']);
        }

        if ($settings['redeem_value_type'] === 'percent') {
            $bps = (int)$settings['redeem_percent_bps_per_point'];
            if ($bps <= 0) {
                $maximumPoints = 0;
            } else {
                $discountPerPoint = floor($registration->total_price * $bps / 10000);
                $maximumPoints = $discountPerPoint > 0 ? (int)floor($maxDiscountAmount / $discountPerPoint) : 0;
            }
        } else {
            $vndVal = (int)$settings['redeem_vnd_per_point'];
            $maximumPoints = $vndVal > 0 ? (int)floor($maxDiscountAmount / $vndVal) : 0;
        }

        return [
            'balance' => $availablePoints,
            'maximum_points' => $maximumPoints,
            'available_points' => min(max(0, $availablePoints), $maximumPoints),
        ];
    }

    /**
     * Tích lũy điểm khi thanh toán đơn (Earn).
     */
    public function earn(Registration $registration, int $netPaid, array $settings, ?User $actor = null): ?PointTransaction
    {
        if (!$settings['enabled']) {
            return null;
        }

        // Kiểm tra ngưỡng đơn tối thiểu trên total_price gốc như phản hồi số 8
        if ($registration->total_price < (int)$settings['min_order_value_to_earn'] || (int)$settings['vnd_per_earned_point'] <= 0) {
            return null;
        }

        $customer = Customer::lockForUpdate()->find($registration->customer_id);
        if (!$customer) {
            return null;
        }

        // Tính điểm gốc dựa trên netPaid thực trả
        $basePoints = (int) floor($netPaid / (int)$settings['vnd_per_earned_point']);
        if ($basePoints <= 0) {
            return null;
        }

        // 1. Hệ số Hạng thành viên (chỉ tính lịch sử dựa trên EARN/EARN_REVERSAL như phản hồi số 7)
        $historyPoints = (int) PointTransaction::where('customer_id', $customer->id)
            ->where('type', PointTransaction::EARN)
            ->sum('points');
        $reversals = (int) PointTransaction::where('customer_id', $customer->id)
            ->where('type', PointTransaction::EARN_REVERSAL)
            ->sum('points');
        $historyPoints = max(0, $historyPoints + $reversals);

        $tierName = 'Thành viên';
        $tierMultiplier = 1.0;
        if (!empty($settings['tiers']) && is_array($settings['tiers'])) {
            $tiers = collect($settings['tiers'])->sortByDesc('min_points');
            foreach ($tiers as $tier) {
                if ($historyPoints >= (int)$tier['min_points']) {
                    $tierMultiplier = (float)$tier['multiplier'];
                    $tierName = $tier['name'];
                    break;
                }
            }
        }

        // 2. Hệ số các chiến dịch lễ tết đang diễn ra (cộng dồn phần lẻ như phản hồi số 6)
        $campaignsMultiplier = 1.0;
        $activeCampaigns = [];
        if (!empty($settings['campaigns']) && is_array($settings['campaigns'])) {
            $todayStr = now('Asia/Ho_Chi_Minh')->toDateString();
            foreach ($settings['campaigns'] as $camp) {
                if ($todayStr >= $camp['start_date'] && $todayStr <= $camp['end_date']) {
                    $campaignsMultiplier += ((float)$camp['multiplier'] - 1.0);
                    $activeCampaigns[] = $camp['name'];
                }
            }
        }

        // 3. Hệ số sinh nhật bệnh nhân (cố định DOB bệnh nhân đăng ký tiêm như phản hồi số 14)
        $birthdayMultiplier = 1.0;
        $isBirthday = false;
        if ((float)$settings['birthday_multiplier'] > 1.0 && $registration->patient_id) {
            $patient = DB::table('patients')->where('id', $registration->patient_id)->first();
            if ($patient && $patient->dob) {
                $dob = Carbon::parse($patient->dob);
                $injectionDate = Carbon::parse($registration->injection_date);
                if ($dob->format('m-d') === $injectionDate->format('m-d')) {
                    $birthdayMultiplier = (float)$settings['birthday_multiplier'];
                    $isBirthday = true;
                }
            }
        }

        // Tổng hợp hệ số nhân cộng dồn phần dư
        $finalMultiplier = 1.0 
            + ($tierMultiplier - 1.0) 
            + ($campaignsMultiplier - 1.0) 
            + ($birthdayMultiplier - 1.0);

        $finalPoints = (int) floor($basePoints * $finalMultiplier);
        if ($finalPoints <= 0) {
            return null;
        }

        // Tính ngày hết hạn (áp dụng addMonthsNoOverflow như phản hồi số 12)
        $expiredAt = null;
        $expiryMonths = (int) $settings['point_expiry_months'];
        if ($expiryMonths > 0) {
            $expiredAt = now('Asia/Ho_Chi_Minh')->addMonthsNoOverflow($expiryMonths)->endOfDay();
        }

        // Tạo metadata snapshot như phản hồi số 10
        $metadata = [
            'settings_snapshot' => [
                'vnd_per_earned_point' => $settings['vnd_per_earned_point'],
                'point_expiry_months' => $settings['point_expiry_months'],
            ],
            'multipliers' => [
                'tier' => ['name' => $tierName, 'multiplier' => $tierMultiplier],
                'campaigns' => ['names' => $activeCampaigns, 'multiplier' => $campaignsMultiplier],
                'birthday' => ['active' => $isBirthday, 'multiplier' => $birthdayMultiplier],
                'final_multiplier' => $finalMultiplier,
            ],
            'base_points' => $basePoints,
        ];

        return PointTransaction::create([
            'customer_id' => $customer->id,
            'registration_id' => $registration->id,
            'center_id' => $registration->center_id,
            'created_by' => $actor?->id,
            'type' => PointTransaction::EARN,
            'points' => $finalPoints,
            'source_key' => 'registration:' . $registration->id . ':earn',
            'note' => 'Tích điểm từ đơn ' . $registration->registration_code,
            'expired_at' => $expiredAt,
            'metadata' => $metadata,
        ]);
    }

    /**
     * Tiêu dùng điểm theo FIFO khi thanh toán đơn (Redeem).
     */
    public function redeem(Customer $customer, Registration $registration, int $redeemPoints, array $settings, ?User $actor = null): ?PointTransaction
    {
        if (!$settings['enabled'] || $redeemPoints <= 0) {
            return null;
        }

        $quote = $this->quote($customer, $registration, $settings);
        if ($redeemPoints > $quote['available_points']) {
            throw ValidationException::withMessages([
                'redeem_points' => 'Số điểm sử dụng vượt quá số dư hoặc giới hạn tối đa của đơn.',
            ]);
        }

        // Lấy các credit transactions còn hạn sử dụng
        $credits = PointTransaction::where('customer_id', $customer->id)
            ->whereIn('type', [PointTransaction::EARN, PointTransaction::ADJUSTMENT])
            ->where('points', '>', 0)
            ->where(function ($query) {
                $query->whereNull('expired_at')
                      ->orWhere('expired_at', '>', now('Asia/Ho_Chi_Minh'));
            })
            ->get()
            ->map(function ($c) {
                $allocated = DB::table('point_allocations')
                    ->where('credit_transaction_id', $c->id)
                    ->sum('points');
                $c->points_remaining = max(0, $c->points - $allocated);
                return $c;
            })
            ->filter(fn($c) => $c->points_remaining > 0);

        // Tạo giao dịch REDEEM (lưu âm)
        $metadata = [
            'settings_snapshot' => [
                'redeem_value_type' => $settings['redeem_value_type'],
                'redeem_vnd_per_point' => $settings['redeem_vnd_per_point'] ?? null,
                'redeem_percent_bps_per_point' => $settings['redeem_percent_bps_per_point'] ?? null,
            ],
            'allocations' => []
        ];

        $redeemTx = PointTransaction::create([
            'customer_id' => $customer->id,
            'registration_id' => $registration->id,
            'center_id' => $registration->center_id,
            'created_by' => $actor?->id,
            'type' => PointTransaction::REDEEM,
            'points' => -$redeemPoints,
            'source_key' => 'registration:' . $registration->id . ':redeem',
            'note' => 'Sử dụng điểm cho đơn ' . $registration->registration_code,
            'expired_at' => null,
            'metadata' => $metadata,
        ]);

        $needed = $redeemPoints;
        $allocSnapshots = [];
        foreach ($credits as $c) {
            if ($needed <= 0) {
                break;
            }

            $alloc = min($c->points_remaining, $needed);

            DB::table('point_allocations')->insert([
                'credit_transaction_id' => $c->id,
                'debit_transaction_id' => $redeemTx->id,
                'points' => $alloc,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $allocSnapshots[] = [
                'credit_transaction_id' => $c->id,
                'points' => $alloc,
                'expired_at' => $c->expired_at ? $c->expired_at->toDateTimeString() : null
            ];

            $needed -= $alloc;
        }

        // Cập nhật lại metadata lưu chi tiết allocation
        $redeemTx->update([
            'metadata' => array_merge($metadata, ['allocations' => $allocSnapshots])
        ]);

        return $redeemTx;
    }

    /**
     * Hoàn trả điểm đúng lô gốc khi huỷ đơn (Redeem Refund).
     */
    public function refund(Registration $registration, ?User $actor = null): ?PointTransaction
    {
        $redeemTx = PointTransaction::where('registration_id', $registration->id)
            ->where('type', PointTransaction::REDEEM)
            ->first();

        if (!$redeemTx) {
            return null;
        }

        $customer = Customer::lockForUpdate()->findOrFail($registration->customer_id);
        $refundPoints = abs($redeemTx->points);

        // Tạo giao dịch REDEEM_REFUND (lưu dương) liên kết reverses_transaction_id
        $refundTx = PointTransaction::create([
            'customer_id' => $customer->id,
            'registration_id' => $registration->id,
            'center_id' => $registration->center_id,
            'created_by' => $actor?->id,
            'type' => PointTransaction::REDEEM_REFUND,
            'points' => $refundPoints,
            'source_key' => 'registration:' . $registration->id . ':refund',
            'note' => 'Hoàn trả điểm sử dụng từ đơn ' . $registration->registration_code,
            'reverses_transaction_id' => $redeemTx->id,
            'expired_at' => null,
            'metadata' => [
                'reverses_transaction_id' => $redeemTx->id
            ]
        ]);

        // Tìm các allocations của giao dịch REDEEM gốc và hoàn lại (LIFO)
        $allocations = DB::table('point_allocations')
            ->where('debit_transaction_id', $redeemTx->id)
            ->orderBy('id', 'desc')
            ->get();

        $remainingRefund = $refundPoints;
        foreach ($allocations as $alloc) {
            if ($remainingRefund <= 0) {
                break;
            }

            $returnVal = min($alloc->points, $remainingRefund);

            if ($returnVal === $alloc->points) {
                DB::table('point_allocations')
                    ->where('id', $alloc->id)
                    ->delete();
            } else {
                DB::table('point_allocations')
                    ->where('id', $alloc->id)
                    ->decrement('points', $returnVal);
            }

            $remainingRefund -= $returnVal;
        }

        return $refundTx;
    }

    /**
     * Đảo điểm tích lũy đúng lô gốc khi huỷ đơn (Earn Reversal).
     */
    public function earnReversal(Registration $registration, ?User $actor = null): ?PointTransaction
    {
        $earnTx = PointTransaction::where('registration_id', $registration->id)
            ->where('type', PointTransaction::EARN)
            ->first();

        if (!$earnTx) {
            return null;
        }

        $customer = Customer::lockForUpdate()->findOrFail($registration->customer_id);
        $reversalPoints = abs($earnTx->points);

        // Tạo giao dịch EARN_REVERSAL (lưu âm) liên kết reverses_transaction_id
        $reversalTx = PointTransaction::create([
            'customer_id' => $customer->id,
            'registration_id' => $registration->id,
            'center_id' => $registration->center_id,
            'created_by' => $actor?->id,
            'type' => PointTransaction::EARN_REVERSAL,
            'points' => -$reversalPoints,
            'source_key' => 'registration:' . $registration->id . ':reversal',
            'note' => 'Thu hồi điểm tích lũy từ đơn ' . $registration->registration_code,
            'reverses_transaction_id' => $earnTx->id,
            'expired_at' => null,
            'metadata' => [
                'reverses_transaction_id' => $earnTx->id
            ]
        ]);

        // Logic đảo điểm an toàn (đặc biệt khi điểm đã tiêu dùng một phần hoặc toàn bộ)
        // Ta tạo một allocation liên kết từ EARN gốc sang EARN_REVERSAL để tiêu thụ hết points của EARN gốc
        // Điều này đảm bảo EARN gốc không thể được dùng để redeem sau này
        DB::table('point_allocations')->insert([
            'credit_transaction_id' => $earnTx->id,
            'debit_transaction_id' => $reversalTx->id,
            'points' => $reversalPoints,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return $reversalTx;
    }

    /**
     * Điều chỉnh điểm thủ công (Adjustment).
     */
    public function adjustPoints(Customer $customer, int $points, string $note, ?string $expiryDate = null, ?User $actor = null, ?string $sourceKey = null): PointTransaction
    {
        return DB::transaction(function () use ($customer, $points, $note, $expiryDate, $actor, $sourceKey) {
            $customer = Customer::lockForUpdate()->findOrFail($customer->id);

            if ($points === 0) {
                throw ValidationException::withMessages([
                    'points' => 'Số điểm điều chỉnh phải khác 0.',
                ]);
            }

            $sourceKeyResolved = $sourceKey ?: ('adjustment:' . (string) \Illuminate\Support\Str::uuid());

            if ($points > 0) {
                // Cộng điểm: lưu credit transaction
                $expiredAt = null;
                if (!empty($expiryDate)) {
                    $expiredAt = Carbon::parse($expiryDate)->endOfDay();
                }

                return PointTransaction::firstOrCreate(
                    ['source_key' => $sourceKeyResolved],
                    [
                        'customer_id' => $customer->id,
                        'created_by' => $actor?->id,
                        'type' => PointTransaction::ADJUSTMENT,
                        'points' => $points,
                        'note' => $note,
                        'expired_at' => $expiredAt,
                    ]
                );
            } else {
                // Trừ điểm: Kiểm tra available balance hiện có và trừ theo FIFO
                $needed = abs($points);
                $available = $this->calculateAvailablePoints($customer);
                
                // Áp dụng chính sách dư nợ: trừ điểm không được vượt quá số dư khả dụng
                if ($needed > $available) {
                    throw ValidationException::withMessages([
                        'points' => 'Số điểm điều chỉnh trừ vượt quá số dư điểm khả dụng hiện tại (' . number_format($available) . ' điểm).',
                    ]);
                }

                // Lấy các credit transactions còn hạn để trừ
                $credits = PointTransaction::where('customer_id', $customer->id)
                    ->whereIn('type', [PointTransaction::EARN, PointTransaction::ADJUSTMENT])
                    ->where('points', '>', 0)
                    ->where(function ($query) {
                        $query->whereNull('expired_at')
                              ->orWhere('expired_at', '>', now('Asia/Ho_Chi_Minh'));
                    })
                    ->get()
                    ->map(function ($c) {
                        $allocated = DB::table('point_allocations')
                            ->where('credit_transaction_id', $c->id)
                            ->sum('points');
                        $c->points_remaining = max(0, $c->points - $allocated);
                        return $c;
                    })
                    ->filter(fn($c) => $c->points_remaining > 0);

                // Tạo debit transaction
                $debitTx = PointTransaction::firstOrCreate(
                    ['source_key' => $sourceKeyResolved],
                    [
                        'customer_id' => $customer->id,
                        'created_by' => $actor?->id,
                        'type' => PointTransaction::ADJUSTMENT,
                        'points' => $points, // số âm
                        'note' => $note,
                        'expired_at' => null,
                    ]
                );

                if ($debitTx->wasRecentlyCreated) {
                    foreach ($credits as $c) {
                        if ($needed <= 0) {
                            break;
                        }

                        $alloc = min($c->points_remaining, $needed);

                        DB::table('point_allocations')->insert([
                            'credit_transaction_id' => $c->id,
                            'debit_transaction_id' => $debitTx->id,
                            'points' => $alloc,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);

                        $needed -= $alloc;
                    }
                }

                return $debitTx;
            }
        });
    }
}
