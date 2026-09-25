<?php

use Illuminate\Support\Facades\Route;
use Modules\Booking\Http\Controllers\BookingController;

// Public: submit a booking request. No payment is collected — the customer
// is directed to live chat (Modules/Support) to confirm and pay.
Route::post('bookings', [BookingController::class, 'store']);

// Admin: managing bookings requires an authenticated admin token.
Route::middleware('auth:sanctum')->prefix('admin')->group(function () {
    Route::get('bookings', [BookingController::class, 'index']);
    Route::patch('bookings/{booking}/status', [BookingController::class, 'updateStatus']);
});
