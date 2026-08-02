<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->foreignId('customer_id')->nullable()->after('patient_id')->constrained('customers')->nullOnDelete();
            $table->string('booking_status')->default('pending')->after('status')->index();
            $table->string('payment_status')->default('unpaid')->after('booking_status')->index();
            $table->string('idempotency_key')->nullable()->after('payment_status')->unique();
            $table->unsignedInteger('points_discount_amount')->default(0)->after('total_price');
            $table->timestamp('paid_at')->nullable()->after('points_discount_amount');
            $table->timestamp('refunded_at')->nullable()->after('paid_at');
            $table->foreignId('settled_by')->nullable()->after('refunded_at')->constrained('users')->nullOnDelete();
            $table->index(['center_id', 'injection_date']);
            $table->index(['center_id', 'payment_status', 'created_at']);
            $table->index(['customer_id', 'created_at']);
        });

        Schema::table('registrations', function (Blueprint $table) {
            $table->date('patient_dob')->nullable()->change();
            $table->string('patient_gender')->nullable()->change();
            $table->string('patient_address')->nullable()->change();
        });

        DB::table('registrations')->orderBy('id')->chunkById(200, function ($registrations) {
            foreach ($registrations as $registration) {
                $bookingStatus = match ($registration->status) {
                    'Đã hủy', 'cancelled' => 'cancelled',
                    'Đã tiêm', 'completed' => 'completed',
                    'Đã xác nhận', 'Đã thanh toán', 'paid', 'checked_in', 'confirmed' => 'confirmed',
                    default => 'pending',
                };

                $paymentStatus = in_array($registration->status, ['Đã thanh toán', 'Đã tiêm', 'paid', 'completed'], true)
                    ? 'paid'
                    : 'unpaid';

                DB::table('registrations')->where('id', $registration->id)->update([
                    'booking_status' => $bookingStatus,
                    'payment_status' => $paymentStatus,
                    'status' => $bookingStatus,
                ]);

                $phone = $this->normalizePhone($registration->guardian_phone ?: $registration->patient_phone);
                if (!$phone) {
                    continue;
                }

                $customerId = DB::table('customers')->where('phone', $phone)->value('id');
                if (!$customerId) {
                    $customerId = DB::table('customers')->insertGetId([
                        'name' => trim((string) ($registration->guardian_name ?: $registration->patient_name)) ?: 'Khách hàng',
                        'phone' => $phone,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                DB::table('registrations')->where('id', $registration->id)->update(['customer_id' => $customerId]);
            }
        });
    }

    public function down(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->dropIndex(['center_id', 'injection_date']);
            $table->dropIndex(['center_id', 'payment_status', 'created_at']);
            $table->dropIndex(['customer_id', 'created_at']);
            $table->dropForeign(['customer_id']);
            $table->dropForeign(['settled_by']);
            $table->dropColumn([
                'customer_id',
                'booking_status',
                'payment_status',
                'idempotency_key',
                'points_discount_amount',
                'paid_at',
                'refunded_at',
                'settled_by',
            ]);
        });
    }

    private function normalizePhone(?string $value): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $value);

        if (str_starts_with($digits, '00')) {
            $digits = substr($digits, 2);
        }

        if (str_starts_with($digits, '0')) {
            $digits = '84' . substr($digits, 1);
        }

        return preg_match('/^84\d{9}$/', $digits) ? '+' . $digits : null;
    }
};
