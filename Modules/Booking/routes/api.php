<?php

use Illuminate\Support\Facades\Route;
use Modules\Booking\Http\Controllers\BookingController;

// Bookings are only ever created via checkout (Modules/Payment) so that
// every booking is tied to a payment — there is deliberately no public
// "create a booking directly" route here.

// Admin: managing bookings requires an authenticated admin token.
Route::middleware('auth:sanctum')->prefix('admin')->group(function () {
    Route::get('bookings', [BookingController::class, 'index']);
    Route::patch('bookings/{booking}/status', [BookingController::class, 'updateStatus']);
});
