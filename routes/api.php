<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AmazonProductController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Amazon Product API Routes
Route::prefix('amazon')->group(function () {
    // Search and product lookup
    Route::get('/search', [AmazonProductController::class, 'search']);
    Route::get('/product', [AmazonProductController::class, 'getProduct']);
    Route::get('/search-figures', [AmazonProductController::class, 'searchFigures']);
    
    // Sync operations
    Route::post('/sync-product/{productId}', [AmazonProductController::class, 'syncProduct']);
    Route::post('/sync-product-by-asin/{productId}', [AmazonProductController::class, 'syncProductByAsin']);
    Route::post('/sync-all', [AmazonProductController::class, 'syncAllProducts']);
    Route::post('/sync-needed', [AmazonProductController::class, 'syncNeededProducts']);
    
    // Status and monitoring
    Route::get('/sync-status', [AmazonProductController::class, 'getSyncStatus']);
});

// Optional: Admin-only routes
Route::middleware(['auth:sanctum', 'admin'])->prefix('admin/amazon')->group(function () {
    // Admin sync operations
    Route::post('/sync-product/{productId}', [AmazonProductController::class, 'syncProduct']);
    Route::post('/sync-product-by-asin/{productId}', [AmazonProductController::class, 'syncProductByAsin']);
    Route::post('/sync-all', [AmazonProductController::class, 'syncAllProducts']);
    Route::post('/sync-needed', [AmazonProductController::class, 'syncNeededProducts']);
    
    // Admin status and monitoring
    Route::get('/sync-status', [AmazonProductController::class, 'getSyncStatus']);
    Route::get('/search', [AmazonProductController::class, 'search']);
    Route::get('/product', [AmazonProductController::class, 'getProduct']);
    Route::get('/search-figures', [AmazonProductController::class, 'searchFigures']);
});