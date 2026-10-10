<?php

use Illuminate\Support\Facades\Route;
use Modules\Payment\Http\Controllers\PaymentController;

// Public: checking out and NOWPayments' own IPN callback need no auth
// (the IPN is verified via HMAC signature instead).
Route::post('checkout', [PaymentController::class, 'checkout'])->middleware('throttle:checkout');
Route::post('payments/ipn', [PaymentController::class, 'ipn'])->middleware('throttle:payment-ipn');

// Simple crypto invoice (just amount, like Sky Play)
Route::post('payment/nowpayments/invoice', [PaymentController::class, 'createInvoice']);

// Card ramp providers (Alchemy Pay, MoonPay, Transak)
Route::get('payment/card/ramp-url', [PaymentController::class, 'cardRampUrl']);
Route::get('payment/wallet-config', [PaymentController::class, 'walletConfig']);

Route::get('payment/version', fn () => response()->json([
    'version' => 'v3',
    'time' => now()->toIso8601String(),
    'ipn_callback_url' => config('nowpayments.ipn_callback_url'),
    'success_url' => config('nowpayments.success_url'),
    'cancel_url' => config('nowpayments.cancel_url'),
    'mail_mailer' => config('mail.default'),
]));
