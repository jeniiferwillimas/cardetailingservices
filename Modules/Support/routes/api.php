<?php

use Illuminate\Support\Facades\Route;
use Modules\Support\Http\Controllers\ChatController;

// Public: anonymous visitors starting/continuing a chat need no authentication.
Route::post('chat/conversations', [ChatController::class, 'startConversation']);
Route::get('chat/conversations/{uuid}/messages', [ChatController::class, 'messages']);
Route::post('chat/conversations/{uuid}/messages', [ChatController::class, 'sendMessage']);

// Admin: viewing/replying to the support inbox requires an authenticated admin token.
Route::middleware('auth:sanctum')->prefix('admin')->group(function () {
    Route::get('chat/conversations', [ChatController::class, 'adminIndex']);
    Route::get('chat/conversations/{conversation}/messages', [ChatController::class, 'adminMessages']);
    Route::post('chat/conversations/{conversation}/messages', [ChatController::class, 'adminReply']);
});
