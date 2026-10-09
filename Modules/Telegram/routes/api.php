<?php

use Illuminate\Support\Facades\Route;
use Modules\Telegram\Http\Controllers\TelegramController;

Route::post('telegram/webhook', [TelegramController::class, 'webhook']);

Route::middleware('auth:sanctum')->prefix('admin')->group(function () {
    Route::post('telegram/set-webhook', [TelegramController::class, 'setWebhook']);
    Route::post('telegram/remove-webhook', [TelegramController::class, 'removeWebhook']);
});
