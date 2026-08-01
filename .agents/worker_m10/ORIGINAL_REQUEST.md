## 2026-08-01T11:02:39+07:00
Implement R6 (Payment Webhook Verification & Background Queue Jobs) under Ponytail principles (minimal, clean, standard Laravel 11, zero unnecessary abstractions).

REQUIREMENTS:
1. Payment Webhook Verification:
   - Provide/update endpoint `POST /api/webhooks/payment` for secure server-to-server payment verification.
   - Validate payload signature (e.g., HMAC/hash using configured webhook secret), verify registration transaction reference, validate payment amount against registration total amount.
   - On valid signature & amount match, update `Registration` status to `paid` in a database transaction.
   - Ensure browser return URLs CANNOT directly mutate payment status to `paid` without verified server-to-server signature validation.

2. Background Queue Jobs:
   - Create/update Laravel Queue Jobs (e.g., `SendRegistrationEmailJob`, `SendNotificationSmsJob`) implementing `ShouldQueue`.
   - Move Email/SMS notification logic when a registration is created or paid into background Queue Jobs so the main HTTP transaction request is never blocked.

3. Testing & Verification:
   - Write feature tests in `tests/Feature/PaymentWebhookAndQueueTest.php` covering signature verification, amount validation, blocking unverified browser return status updates, and queue job dispatching.
   - Run PHPUnit tests using `/opt/lampp/bin/php ./vendor/bin/phpunit`.
   - Ensure all feature tests and the full test suite pass cleanly.
