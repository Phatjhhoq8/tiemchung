<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Queue;
use Modules\VaccineRegistration\Models\Center;
use Modules\VaccineRegistration\Models\Vaccine;
use Modules\VaccineRegistration\Models\Registration;
use Modules\VaccineRegistration\Models\CenterVaccine;
use App\Jobs\SendRegistrationEmailJob;
use App\Jobs\SendNotificationSmsJob;

class PaymentWebhookAndQueueTest extends TestCase
{
    use DatabaseTransactions;

    protected Center $center;
    protected Vaccine $vaccine;
    protected Registration $registration;
    protected string $secret;

    protected function setUp(): void
    {
        parent::setUp();

        $this->secret = config('services.payment.webhook_secret', 'test_webhook_secret_key_12345');

        $this->center = Center::create([
            'name' => 'Trung Tâm Webhook Test',
            'code' => 'TT-WHTEST-' . rand(1000, 9999),
            'address' => '123 Đường Test, Quận 1',
            'phone' => '0901234567',
            'email' => 'webhooktest@medicare.local',
            'is_active' => true,
        ]);

        $this->vaccine = Vaccine::create([
            'name' => 'Vắc xin Webhook Test',
            'code' => 'VX-WHTEST-' . rand(1000, 9999),
            'price' => 500000.00,
            'type' => 'single',
            'doses' => 1,
            'disease_prevention' => 'Cúm',
            'age_group' => 'Mọi lứa tuổi',
            'origin' => 'Pháp',
            'manufacturer' => 'Sanofi Pasteur',
            'is_active' => true,
        ]);

        CenterVaccine::create([
            'center_id' => $this->center->id,
            'vaccine_id' => $this->vaccine->id,
            'price' => 500000.00,
            'stock_quantity' => 100,
            'stock_status' => 'available',
            'is_active' => true,
        ]);

        $this->registration = Registration::create([
            'registration_code' => 'MCD-TEST-' . rand(100000, 999999),
            'patient_name' => 'Nguyễn Văn Test',
            'patient_dob' => '1995-05-15',
            'patient_gender' => 'Nam',
            'patient_phone' => '0987654321',
            'patient_address' => '456 Lê Lợi, Q1',
            'center_id' => $this->center->id,
            'center_name' => $this->center->name,
            'injection_date' => now()->addDays(2)->format('Y-m-d'),
            'status' => 'Chờ thanh toán',
            'payment_method' => 'QR',
            'total_price' => 500000.00,
        ]);
    }

    /** @test */
    public function valid_payment_webhook_updates_registration_status_to_paid_and_dispatches_jobs(): void
    {
        Queue::fake();

        $payload = [
            'registration_code' => $this->registration->registration_code,
            'amount' => 500000.00,
        ];

        $signature = hash_hmac('sha256', $this->registration->registration_code . '.' . 500000.00, $this->secret);

        $response = $this->postJson('/api/webhooks/payment', array_merge($payload, [
            'signature' => $signature,
        ]), [
            'X-Signature' => $signature,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'status' => 'paid',
        ]);

        $this->assertDatabaseHas('registrations', [
            'id' => $this->registration->id,
            'status' => 'paid',
        ]);

        Queue::assertPushed(SendRegistrationEmailJob::class, function ($job) {
            return $job->registration->id === $this->registration->id && $job->event === 'paid';
        });

        Queue::assertPushed(SendNotificationSmsJob::class, function ($job) {
            return $job->registration->id === $this->registration->id && $job->event === 'paid';
        });
    }

    /** @test */
    public function payment_webhook_with_invalid_signature_is_rejected(): void
    {
        Queue::fake();

        $payload = [
            'registration_code' => $this->registration->registration_code,
            'amount' => 500000.00,
            'signature' => 'invalid_signature_hash_xyz',
        ];

        $response = $this->postJson('/api/webhooks/payment', $payload, [
            'X-Signature' => 'invalid_signature_hash_xyz',
        ]);

        $response->assertStatus(401);
        $response->assertJson([
            'success' => false,
            'message' => 'Invalid webhook signature.',
        ]);

        $this->assertDatabaseHas('registrations', [
            'id' => $this->registration->id,
            'status' => 'Chờ thanh toán',
        ]);

        Queue::assertNotPushed(SendRegistrationEmailJob::class);
        Queue::assertNotPushed(SendNotificationSmsJob::class);
    }

    /** @test */
    public function payment_webhook_with_mismatched_amount_is_rejected(): void
    {
        Queue::fake();

        $invalidAmount = 300000.00;
        $signature = hash_hmac('sha256', $this->registration->registration_code . '.' . $invalidAmount, $this->secret);

        $payload = [
            'registration_code' => $this->registration->registration_code,
            'amount' => $invalidAmount,
            'signature' => $signature,
        ];

        $response = $this->postJson('/api/webhooks/payment', $payload, [
            'X-Signature' => $signature,
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
            'message' => 'Payment amount does not match registration total amount.',
        ]);

        $this->assertDatabaseHas('registrations', [
            'id' => $this->registration->id,
            'status' => 'Chờ thanh toán',
        ]);

        Queue::assertNotPushed(SendRegistrationEmailJob::class);
    }

    /** @test */
    public function payment_webhook_with_nonexistent_registration_returns_404(): void
    {
        $nonExistentCode = 'MCD-NONEXISTENT-999';
        $signature = hash_hmac('sha256', $nonExistentCode . '.' . 500000.00, $this->secret);

        $response = $this->postJson('/api/webhooks/payment', [
            'registration_code' => $nonExistentCode,
            'amount' => 500000.00,
            'signature' => $signature,
        ], [
            'X-Signature' => $signature,
        ]);

        $response->assertStatus(404);
        $response->assertJson([
            'success' => false,
            'message' => 'Registration transaction reference not found.',
        ]);
    }

    /** @test */
    public function browser_return_url_cannot_directly_mutate_payment_status_to_paid_without_signature(): void
    {
        $response = $this->getJson('/payment/return?status=paid&registration_code=' . $this->registration->registration_code);

        $response->assertStatus(403);
        $response->assertJson([
            'success' => false,
        ]);

        $this->assertDatabaseHas('registrations', [
            'id' => $this->registration->id,
            'status' => 'Chờ thanh toán',
        ]);
    }

    /** @test */
    public function registration_creation_dispatches_background_queue_jobs(): void
    {
        Queue::fake();

        $postData = [
            'center_id' => $this->center->id,
            'injection_date' => now()->addDays(3)->format('Y-m-d'),
            'payment_method' => 'Tại trung tâm',
            'patients' => [
                [
                    'name' => 'Trần Thị Thử Nghiệm',
                    'dob' => '1998-08-20',
                    'gender' => 'Nữ',
                    'phone' => '0912345678',
                    'address' => '789 Đường 3/2, Q10',
                    'vaccine_ids' => [$this->vaccine->id],
                    'quantity' => 1,
                ]
            ]
        ];

        $response = $this->postJson('/register', $postData);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        Queue::assertPushed(SendRegistrationEmailJob::class, function ($job) {
            return $job->event === 'created';
        });

        Queue::assertPushed(SendNotificationSmsJob::class, function ($job) {
            return $job->event === 'created';
        });
    }
}
