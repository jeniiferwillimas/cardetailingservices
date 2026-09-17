<?php

use Illuminate\Support\Facades\Route;
use Modules\Payment\Http\Controllers\PaymentController;

// Public: checking out and NOWPayments' own IPN callback need no auth
// (the IPN is verified via HMAC signature instead).
Route::post('checkout', [PaymentController::class, 'checkout']);
Route::post('payments/ipn', [PaymentController::class, 'ipn']);
