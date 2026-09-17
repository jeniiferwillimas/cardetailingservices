<?php

use Illuminate\Support\Facades\Route;
use Modules\Service\Http\Controllers\ServiceController;

// Public: browsing services requires no authentication.
Route::get('services', [ServiceController::class, 'index']);
Route::get('services/{slug}', [ServiceController::class, 'show']);

// Admin: managing services requires an authenticated admin token.
Route::middleware('auth:sanctum')->prefix('admin')->group(function () {
    Route::get('services', [ServiceController::class, 'adminIndex']);
    Route::post('services', [ServiceController::class, 'store']);
    Route::patch('services/{service}', [ServiceController::class, 'update']);
    Route::delete('services/{service}', [ServiceController::class, 'destroy']);
});
