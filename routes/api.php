<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentWebhookController;

Route::post('/webhooks/payment', [PaymentWebhookController::class, 'handleWebhook'])->name('api.webhooks.payment');
