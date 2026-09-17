<?php

use Illuminate\Support\Facades\Route;
use Modules\Content\Http\Controllers\GalleryImageController;
use Modules\Content\Http\Controllers\NavigationLinkController;

// Public: browsing site content requires no authentication.
Route::get('navigation-links', [NavigationLinkController::class, 'index']);
Route::get('gallery-images', [GalleryImageController::class, 'index']);

// Admin: managing site content requires an authenticated admin token.
Route::middleware('auth:sanctum')->prefix('admin')->group(function () {
    Route::post('navigation-links', [NavigationLinkController::class, 'store']);
    Route::patch('navigation-links/{navigationLink}', [NavigationLinkController::class, 'update']);
    Route::delete('navigation-links/{navigationLink}', [NavigationLinkController::class, 'destroy']);

    Route::post('gallery-images', [GalleryImageController::class, 'store']);
    Route::patch('gallery-images/{galleryImage}', [GalleryImageController::class, 'update']);
    Route::delete('gallery-images/{galleryImage}', [GalleryImageController::class, 'destroy']);
});
