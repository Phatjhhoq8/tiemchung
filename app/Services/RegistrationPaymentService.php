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
    private readonly BranchStockService $stockService;
    private readonly LoyaltyService $loyaltyService;

    public function __construct(
        ?BranchStockService $stockService = null,
        ?LoyaltyService $loyaltyService = null
    ) {
        $this->stockService = $stockService ?? app(BranchStockService::class);
        $this->loyaltyService = $loyaltyService ?? app(LoyaltyService::class);
    }

    public function getLoyaltySettings(?int $centerId = null): array
    {
        return $this->loyaltyService->getLoyaltySettings($centerId);
    }

    public function calculateAvailablePoints(Customer $customer): int
    {
        return $this->loyaltyService->calculateAvailablePoints($customer);
    }

    public function quote(Customer $customer, Registration $registration): array
    {
        return $this->loyaltyService->quote($customer, $registration);
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

            // Đọc snapshot settings một lần duy nhất tại đây như phản hồi số 2
            $settings = $this->loyaltyService->getLoyaltySettings($registration->center_id);

            $quote = $this->loyaltyService->quote($customer, $registration, $settings);
            if ($redeemPoints < 0 || $redeemPoints > $quote['available_points']) {
                throw ValidationException::withMessages([
                    'redeem_points' => 'Số điểm sử dụng vượt quá số dư hoặc giới hạn tối đa của đơn.',
                ]);
            }

            $discount = 0;
            if ($settings['enabled'] && $redeemPoints > 0) {
                if ($settings['redeem_value_type'] === 'percent') {
                    $bps = (int)$settings['redeem_percent_bps_per_point'];
                    $discount = (int) floor($registration->total_price * ($bps * $redeemPoints) / 10000);
                } else {
                    $vndVal = (int)$settings['redeem_vnd_per_point'];
                    $discount = (int) ($redeemPoints * $vndVal);
                }
            }

            $netPaid = (int) $registration->total_price - $discount;

            // Thực hiện giao dịch tiêu dùng điểm (Redeem)
            $redeemTx = $this->loyaltyService->redeem($customer, $registration, $redeemPoints, $settings, $actor);

            // Thực hiện giao dịch tích điểm (Earn)
            $earnTx = $this->loyaltyService->earn($registration, $netPaid, $settings, $actor);

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
                    'earned_points' => $earnTx ? $earnTx->points : 0,
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

            // Gọi các method đảo điểm và khôi phục điểm theo đúng allocations từ LoyaltyService
            $reversalTx = $this->loyaltyService->earnReversal($registration, $actor);
            $refundTx = $this->loyaltyService->refund($registration, $actor);

            if ($registration->booking_status !== Registration::BOOKING_NO_SHOW) {
                $this->releaseSlot($registration);
            }
            $this->stockService->restore($registration);

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

            if ($registration->booking_status !== Registration::BOOKING_NO_SHOW) {
                $this->releaseSlot($registration);
            }
            $this->stockService->restore($registration);
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

    public function markNoShow(int $registrationId, ?User $actor): Registration
    {
        return DB::transaction(function () use ($registrationId, $actor) {
            $registration = Registration::lockForUpdate()->findOrFail($registrationId);
            if ($registration->booking_status === Registration::BOOKING_NO_SHOW
                || $registration->booking_status === Registration::BOOKING_CANCELLED) {
                return $registration;
            }

            $this->releaseSlot($registration);
            $this->stockService->restore($registration);
            $oldStatus = $registration->booking_status;
            $registration->update([
                'booking_status' => Registration::BOOKING_NO_SHOW,
                'status' => Registration::BOOKING_NO_SHOW,
            ]);

            AuditLogger::logOrderStatusUpdate(
                resourceId: $registration->id,
                oldValues: ['booking_status' => $oldStatus],
                newValues: ['booking_status' => Registration::BOOKING_NO_SHOW],
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
