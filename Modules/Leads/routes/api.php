<?php

use Illuminate\Support\Facades\Route;
use Modules\Leads\Http\Controllers\LeadController;

// Public: capturing a visitor's contact details requires no authentication.
Route::post('leads', [LeadController::class, 'store']);

// Admin: viewing captured leads requires an authenticated admin token.
Route::middleware('auth:sanctum')->prefix('admin')->group(function () {
    Route::get('leads', [LeadController::class, 'adminIndex']);
    Route::patch('leads/{lead}', [LeadController::class, 'update']);
    Route::delete('leads/{lead}', [LeadController::class, 'destroy']);
});
