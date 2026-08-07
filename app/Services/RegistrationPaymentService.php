<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Modules\VaccineRegistration\Models\Customer;
use Modules\VaccineRegistration\Models\PointTransaction;
use Modules\VaccineRegistration\Models\Registration;
use Modules\VaccineRegistration\Models\Slot;

class RegistrationPaymentService
{
    public const VND_PER_EARNED_POINT = 10000;
    public const VND_PER_REDEEMED_POINT = 100;
    public const MAX_REDEEM_PERCENT = 50;

    public function quote(Customer $customer, Registration $registration): array
    {
        $balance = (int) PointTransaction::where('customer_id', $customer->id)->sum('points');
        $maximumPoints = intdiv(
            (int) floor(((int) $registration->total_price * self::MAX_REDEEM_PERCENT) / 100),
            self::VND_PER_REDEEMED_POINT
        );

        return [
            'balance' => $balance,
            'maximum_points' => $maximumPoints,
            'available_points' => min(max(0, $balance), $maximumPoints),
        ];
    }

    public function settle(int $registrationId, int $redeemPoints, ?User $actor): Registration
    {
        return DB::transaction(function () use ($registrationId, $redeemPoints, $actor) {
            $registration = Registration::with('customer')->lockForUpdate()->findOrFail($registrationId);

            if ($registration->payment_status === Registration::PAYMENT_PAID) {
                return $registration;
            }

            if ($registration->payment_status === Registration::PAYMENT_REFUNDED) {
                throw ValidationException::withMessages([
                    'payment' => 'Đơn đã được hoàn tiền và không thể xác nhận thanh toán lại.',
                ]);
            }

            if ($registration->booking_status === Registration::BOOKING_CANCELLED) {
                throw ValidationException::withMessages([
                    'payment' => 'Lịch hẹn đã bị hủy và không thể xác nhận thanh toán.',
                ]);
            }

            $customer = Customer::lockForUpdate()->find($registration->customer_id);
            if (!$customer) {
                throw ValidationException::withMessages([
                    'customer' => 'Đơn chưa có khách hàng hợp lệ để tích điểm.',
                ]);
            }

            $quote = $this->quote($customer, $registration);
            if ($redeemPoints < 0 || $redeemPoints > $quote['available_points']) {
                throw ValidationException::withMessages([
                    'redeem_points' => 'Số điểm sử dụng vượt quá số dư hoặc giới hạn 50% của đơn.',
                ]);
            }

            $discount = $redeemPoints * self::VND_PER_REDEEMED_POINT;
            $netPaid = (int) $registration->total_price - $discount;
            $earnedPoints = intdiv($netPaid, self::VND_PER_EARNED_POINT);

            if ($redeemPoints > 0) {
                PointTransaction::firstOrCreate(
                    ['source_key' => "registration:{$registration->id}:redeem"],
                    [
                        'customer_id' => $customer->id,
                        'registration_id' => $registration->id,
                        'center_id' => $registration->center_id,
                        'created_by' => $actor?->id,
                        'type' => PointTransaction::REDEEM,
                        'points' => -$redeemPoints,
                        'note' => 'Dùng điểm cho đơn ' . $registration->registration_code,
                    ]
                );
            }

            if ($earnedPoints > 0) {
                PointTransaction::firstOrCreate(
                    ['source_key' => "registration:{$registration->id}:earn"],
                    [
                        'customer_id' => $customer->id,
                        'registration_id' => $registration->id,
                        'center_id' => $registration->center_id,
                        'created_by' => $actor?->id,
                        'type' => PointTransaction::EARN,
                        'points' => $earnedPoints,
                        'note' => 'Tích điểm từ đơn ' . $registration->registration_code,
                    ]
                );
            }

            $oldValues = [
                'booking_status' => $registration->booking_status,
                'payment_status' => $registration->payment_status,
                'points_discount_amount' => $registration->points_discount_amount,
            ];

            $registration->update([
                'booking_status' => $registration->booking_status === Registration::BOOKING_PENDING
                    ? Registration::BOOKING_CONFIRMED
                    : $registration->booking_status,
                'payment_status' => Registration::PAYMENT_PAID,
                'status' => $registration->booking_status === Registration::BOOKING_PENDING
                    ? Registration::BOOKING_CONFIRMED
                    : $registration->status,
                'points_discount_amount' => $discount,
                'paid_at' => now(),
                'settled_by' => $actor?->id,
            ]);

            AuditLogger::log(
                action: 'registration_settled',
                resourceType: 'registration',
                resourceId: $registration->id,
                oldValues: $oldValues,
                newValues: [
                    'payment_status' => Registration::PAYMENT_PAID,
                    'redeemed_points' => $redeemPoints,
                    'earned_points' => $earnedPoints,
                    'points_discount_amount' => $discount,
                ],
                centerId: $registration->center_id,
                actorId: $actor?->id,
            );

            return $registration->fresh(['customer']);
        });
    }

    public function refund(int $registrationId, ?User $actor): Registration
    {
        return DB::transaction(function () use ($registrationId, $actor) {
            $registration = Registration::with('customer')->lockForUpdate()->findOrFail($registrationId);

            if ($registration->payment_status === Registration::PAYMENT_REFUNDED) {
                return $registration;
            }

            if ($registration->payment_status !== Registration::PAYMENT_PAID) {
                throw ValidationException::withMessages([
                    'payment' => 'Chỉ có thể hoàn tiền cho đơn đã thanh toán.',
                ]);
            }

            $customer = Customer::lockForUpdate()->find($registration->customer_id);
            if (!$customer) {
                throw ValidationException::withMessages([
                    'customer' => 'Đơn chưa có khách hàng hợp lệ để hoàn điểm.',
                ]);
            }

            $earned = PointTransaction::where('source_key', "registration:{$registration->id}:earn")->first();
            if ($earned) {
                PointTransaction::firstOrCreate(
                    ['source_key' => "registration:{$registration->id}:earn-reversal"],
                    [
                        'customer_id' => $customer->id,
                        'registration_id' => $registration->id,
                        'center_id' => $registration->center_id,
                        'created_by' => $actor?->id,
                        'type' => PointTransaction::EARN_REVERSAL,
                        'points' => -$earned->points,
                        'note' => 'Thu hồi điểm do hoàn đơn ' . $registration->registration_code,
                    ]
                );
            }

            $redeemed = PointTransaction::where('source_key', "registration:{$registration->id}:redeem")->first();
            if ($redeemed) {
                PointTransaction::firstOrCreate(
                    ['source_key' => "registration:{$registration->id}:redeem-refund"],
                    [
                        'customer_id' => $customer->id,
                        'registration_id' => $registration->id,
                        'center_id' => $registration->center_id,
                        'created_by' => $actor?->id,
                        'type' => PointTransaction::REDEEM_REFUND,
                        'points' => abs($redeemed->points),
                        'note' => 'Hoàn lại điểm đã dùng cho đơn ' . $registration->registration_code,
                    ]
                );
            }

            $this->releaseSlot($registration);

            $registration->update([
                'booking_status' => Registration::BOOKING_CANCELLED,
                'payment_status' => Registration::PAYMENT_REFUNDED,
                'status' => Registration::BOOKING_CANCELLED,
                'refunded_at' => now(),
            ]);

            AuditLogger::logRefund(
                resourceId: $registration->id,
                oldValues: ['payment_status' => Registration::PAYMENT_PAID],
                newValues: ['payment_status' => Registration::PAYMENT_REFUNDED],
                centerId: $registration->center_id,
            );

            return $registration->fresh(['customer']);
        });
    }

    public function cancelUnpaid(int $registrationId, ?User $actor): Registration
    {
        return DB::transaction(function () use ($registrationId, $actor) {
            $registration = Registration::lockForUpdate()->findOrFail($registrationId);

            if ($registration->booking_status === Registration::BOOKING_CANCELLED) {
                return $registration;
            }

            if ($registration->payment_status === Registration::PAYMENT_PAID) {
                throw ValidationException::withMessages([
                    'booking_status' => 'Đơn đã thanh toán, vui lòng hoàn tiền thay vì hủy trực tiếp.',
                ]);
            }

            $this->releaseSlot($registration);
            $registration->update([
                'booking_status' => Registration::BOOKING_CANCELLED,
                'status' => Registration::BOOKING_CANCELLED,
            ]);

            AuditLogger::logOrderStatusUpdate(
                resourceId: $registration->id,
                oldValues: ['booking_status' => $registration->getOriginal('booking_status')],
                newValues: ['booking_status' => Registration::BOOKING_CANCELLED],
                centerId: $registration->center_id,
            );

            return $registration->fresh();
        });
    }

    private function releaseSlot(Registration $registration): void
    {
        if (!$registration->slot_id) {
            return;
        }

        $slot = Slot::lockForUpdate()->find($registration->slot_id);
        if ($slot && $slot->reserved_count > 0) {
            $slot->decrement('reserved_count');
        }
    }
}
