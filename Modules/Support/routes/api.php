<?php

use Illuminate\Support\Facades\Route;
use Modules\Support\Http\Controllers\ChatController;

// Public: anonymous visitors starting/continuing a chat need no authentication.
Route::post('chat/conversations', [ChatController::class, 'startConversation']);
Route::get('chat/conversations/{uuid}/messages', [ChatController::class, 'messages']);
Route::post('chat/conversations/{uuid}/messages', [ChatController::class, 'sendMessage']);
Route::patch('chat/conversations/{uuid}/messages/{message}', [ChatController::class, 'updateMessage']);
Route::delete('chat/conversations/{uuid}/messages/{message}', [ChatController::class, 'deleteMessage']);

// Admin: viewing/replying to the support inbox requires an authenticated admin token.
Route::middleware('auth:sanctum')->prefix('admin')->group(function () {
    Route::get('chat/conversations', [ChatController::class, 'adminIndex']);
    Route::get('chat/conversations/{conversation}/messages', [ChatController::class, 'adminMessages']);
    Route::post('chat/conversations/{conversation}/messages', [ChatController::class, 'adminReply']);
    Route::patch('chat/conversations/{conversation}/messages/{message}', [ChatController::class, 'adminUpdateMessage']);
    Route::delete('chat/conversations/{conversation}/messages/{message}', [ChatController::class, 'adminDeleteMessage']);
    Route::post('chat/conversations/{conversation}/read', [ChatController::class, 'adminMarkRead']);
    Route::delete('chat/conversations/{conversation}', [ChatController::class, 'adminDestroyConversation']);
});
