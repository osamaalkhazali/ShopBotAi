<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\ChatSessionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

// Product Search Routes
Route::get('/search', [ProductController::class, 'search']);
Route::get('/ai-recommend', [ProductController::class, 'recommendWithAI']);
Route::post('/recommend-products', [ProductController::class, 'recommendWithAI']);

// AliExpress Product Search Routes
Route::get('/aliexpress/search', [App\Http\Controllers\AliExpressController::class, 'search']);
Route::post('/aliexpress/recommend', [App\Http\Controllers\AliExpressController::class, 'recommendWithAI'])->name('aliexpress.api');
Route::post('/aliexpress/fetch-products', [App\Http\Controllers\AliExpressController::class, 'fetchRecommendedProducts'])->name('aliexpress.fetch-products');
Route::get('/aliexpress/categories', [App\Http\Controllers\AliExpressController::class, 'getCategories'])->name('aliexpress.categories');

// AliExpress Chat Routes - protected by auth middleware
Route::middleware(['web', 'auth'])->prefix('aliexpress/chat')->group(function () {
    // Session management endpoints
    Route::get('/sessions', [App\Http\Controllers\AliExpressChatController::class, 'getSessions']);
    Route::post('/sessions', [App\Http\Controllers\AliExpressChatController::class, 'storeSession']);
    Route::post('/messages', [App\Http\Controllers\AliExpressChatController::class, 'storeMessage']);
    Route::get('/{sessionId}', [App\Http\Controllers\AliExpressChatController::class, 'show']);
    Route::put('/{sessionId}', [App\Http\Controllers\AliExpressChatController::class, 'update']);
    Route::delete('/{sessionId}', [App\Http\Controllers\AliExpressChatController::class, 'destroy']);
    Route::delete('/history/clear', [App\Http\Controllers\AliExpressChatController::class, 'clearHistory']);
});

// AliExpress Chat Ping endpoint (public for testing)
Route::get('/aliexpress/chat/ping', [App\Http\Controllers\AliExpressChatController::class, 'ping']);

// Allow OPTIONS requests for CORS preflight for AliExpress chat
Route::options('/aliexpress/chat/{any}', function () {
    return response()->json(['status' => 'success'], 200);
})->where('any', '.*');

// User Profile Routes - protected by auth middleware
Route::middleware(['web', 'auth'])->group(function () {
    // Product View Tracking
    Route::post('/products/view/{productId}', [UserProfileController::class, 'recordProductView'])->name('api.products.view');
    Route::get('/products/recently-viewed', [UserProfileController::class, 'getRecentlyViewed'])->name('api.products.recently-viewed');

    // Saved Products
    Route::post('/products/save/{productId}', [UserProfileController::class, 'toggleSaveProduct'])->name('api.products.save');
    Route::get('/products/saved', [UserProfileController::class, 'getSavedProducts'])->name('api.products.saved');
    Route::get('/products/saved/check/{productId}', [UserProfileController::class, 'checkIfSaved'])->name('api.products.check-saved');

    // Chat Sessions and Messages
    Route::prefix('chat')->group(function () {
        Route::get('/sessions', [ChatSessionController::class, 'getSessions']);
        Route::post('/sessions', [ChatSessionController::class, 'storeSession']);
        Route::post('/messages', [ChatSessionController::class, 'storeMessage']);
        Route::get('/{sessionId}', [ChatSessionController::class, 'show']);
        Route::put('/{sessionId}', [ChatSessionController::class, 'update']);
        Route::delete('/{sessionId}', [ChatSessionController::class, 'destroy']);
    });
});

// AliExpress Product Management Routes - protected by auth middleware
Route::middleware(['web', 'auth'])->prefix('aliexpress')->group(function () {
    // Product View Tracking
    Route::post('/products/view/{aliexpressProductId}', [App\Http\Controllers\AliExpressProductController::class, 'recordView'])->name('api.aliexpress.products.view');
    Route::get('/products/viewed', [App\Http\Controllers\AliExpressProductController::class, 'getViewedProducts'])->name('api.aliexpress.products.viewed');

    // Saved Products
    Route::post('/products/save/{aliexpressProductId}', [App\Http\Controllers\AliExpressProductController::class, 'toggleSave'])->name('api.aliexpress.products.save');
    Route::get('/products/saved', [App\Http\Controllers\AliExpressProductController::class, 'getSavedProducts'])->name('api.aliexpress.products.saved');
    Route::get('/products/saved/check/{aliexpressProductId}', [App\Http\Controllers\AliExpressProductController::class, 'checkIfSaved'])->name('api.aliexpress.products.check-saved');

    // Product Discovery
    Route::get('/products/most-recommended', [App\Http\Controllers\AliExpressProductController::class, 'getMostRecommended'])->name('api.aliexpress.products.most-recommended');
    Route::get('/products/by-category', [App\Http\Controllers\AliExpressProductController::class, 'getByCategory'])->name('api.aliexpress.products.by-category');
    Route::get('/products/by-price-range', [App\Http\Controllers\AliExpressProductController::class, 'getByPriceRange'])->name('api.aliexpress.products.by-price-range');
});

// AliExpress Product Management Routes (alternative URL format for JavaScript compatibility)
Route::middleware(['web', 'auth'])->prefix('aliexpress-products')->group(function () {
    // Product View Tracking
    Route::post('/view/{aliexpressProductId}', [App\Http\Controllers\AliExpressProductController::class, 'recordView'])->name('api.aliexpress-products.view');

    // Saved Products
    Route::post('/save/{aliexpressProductId}', [App\Http\Controllers\AliExpressProductController::class, 'toggleSave'])->name('api.aliexpress-products.save');
    Route::get('/saved/check/{aliexpressProductId}', [App\Http\Controllers\AliExpressProductController::class, 'checkIfSaved'])->name('api.aliexpress-products.check-saved');
});
