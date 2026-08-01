<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Modules\VaccineRegistration\Models\Registration;
use App\Jobs\SendRegistrationEmailJob;
use App\Jobs\SendNotificationSmsJob;

class PaymentWebhookController extends Controller
{
    /**
     * Endpoint for secure server-to-server payment verification: POST /api/webhooks/payment
     */
    public function handleWebhook(Request $request): JsonResponse
    {
        $registrationCode = $request->input('registration_code') 
            ?? $request->input('reference') 
            ?? $request->input('registration_id');

        $amount = $request->input('amount') ?? $request->input('total_price');

        $signature = $request->header('X-Signature') 
            ?? $request->header('X-Webhook-Signature') 
            ?? $request->input('signature');

        if (!$registrationCode || $amount === null || !$signature) {
            return response()->json([
                'success' => false,
                'message' => 'Missing required webhook payload or signature.'
            ], 400);
        }

        $secret = config('services.payment.webhook_secret', env('PAYMENT_WEBHOOK_SECRET', 'test_webhook_secret_key_12345'));

        if (!$this->verifySignature($request, (string)$registrationCode, (float)$amount, (string)$signature, (string)$secret)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid webhook signature.'
            ], 401);
        }

        $registration = Registration::where('registration_code', $registrationCode)
            ->orWhere('id', $registrationCode)
            ->first();

        if (!$registration) {
            return response()->json([
                'success' => false,
                'message' => 'Registration transaction reference not found.'
            ], 404);
        }

        if (abs((float)$amount - (float)$registration->total_price) > 0.01) {
            return response()->json([
                'success' => false,
                'message' => 'Payment amount does not match registration total amount.'
            ], 422);
        }

        DB::transaction(function () use ($registration) {
            $registration->update(['status' => 'paid']);
        });

        SendRegistrationEmailJob::dispatch($registration->fresh(), 'paid');
        SendNotificationSmsJob::dispatch($registration->fresh(), 'paid');

        return response()->json([
            'success' => true,
            'message' => 'Payment verified successfully and registration status updated to paid.',
            'registration_code' => $registration->registration_code,
            'status' => 'paid',
        ], 200);
    }

    /**
     * Browser return URL endpoint: GET/POST /payment/return
     * Ensures browser return URLs cannot directly mutate payment status to paid without valid signature.
     */
    public function handleBrowserReturn(Request $request)
    {
        $statusParam = $request->input('status') ?? $request->input('payment_status');
        $registrationCode = $request->input('registration_code') ?? $request->input('reference');
        $signature = $request->header('X-Signature') ?? $request->input('signature');

        if ($statusParam === 'paid' || $statusParam === 'success') {
            $secret = config('services.payment.webhook_secret', env('PAYMENT_WEBHOOK_SECRET', 'test_webhook_secret_key_12345'));
            $amount = (float)$request->input('amount', 0);

            if (!$signature || !$registrationCode || !$this->verifySignature($request, (string)$registrationCode, $amount, (string)$signature, (string)$secret)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Direct payment status mutation from browser return URL is forbidden without verified server signature.'
                ], 403);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Browser return received safely without unauthorized status mutation.'
        ]);
    }

    /**
     * HMAC signature verification supporting multiple standard payload formats.
     */
    private function verifySignature(Request $request, string $reference, float $amount, string $signature, string $secret): bool
    {
        $candidates = [
            hash_hmac('sha256', $request->getContent(), $secret),
            hash_hmac('sha256', $reference . '.' . $amount, $secret),
            hash_hmac('sha256', $reference . ':' . $amount, $secret),
            hash_hmac('sha256', $reference . $amount, $secret),
            hash_hmac('sha256', json_encode(['registration_code' => $reference, 'amount' => $amount]), $secret),
            hash_hmac('sha256', json_encode(['reference' => $reference, 'amount' => $amount]), $secret),
        ];

        foreach ($candidates as $expected) {
            if (hash_equals($expected, $signature)) {
                return true;
            }
        }

        return false;
    }
}
