<?php

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\ChatSessionController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('welcome');
});

// Authenticated User Routes
Route::middleware(['auth', 'verified'])->group(function () {
    // User Dashboard (renamed from /dashboard to clarify its purpose)
    Route::get('/account/history', [UserProfileController::class, 'dashboard'])->name('user.history');

    // Legacy route to maintain backwards compatibility, redirects to new route
    Route::get('/dashboard', function () {
        return redirect()->route('user.history');
    });

    // Chat Interface
    Route::get('/chatbot', [ChatSessionController::class, 'showChatInterface'])->name('chatbot');

    // AliExpress Chatbot Interface
    Route::get('/aliexpress-chatbot', function () {
        return view('aliexpress-chatbot');
    })->name('aliexpress-chatbot');

    // AliExpress Dashboard
    Route::get('/account/aliexpress', [UserProfileController::class, 'aliexpressDashboard'])->name('user.aliexpress.dashboard');

    // User Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // AJAX Routes for product views and saved products
    Route::prefix('account')->group(function () {
        Route::get('/load-more-saved', [UserProfileController::class, 'loadMoreSaved'])->name('load.more.saved');
        Route::get('/aliexpress/load-more-saved', [UserProfileController::class, 'loadMoreAliExpressSaved'])->name('user.aliexpress.load.more.saved');
    });
});

// Admin routes
Route::prefix('admin')->name('admin.')->group(function () {
    // Auth routes (public routes for admin authentication)
    Route::middleware('guest:admin')->group(function () {
        Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [AdminAuthController::class, 'login'])->name('login.submit');
    });

    // Protected admin routes
    Route::middleware(['auth:admin'])->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

        // User Management
        Route::get('/users', [UsersController::class, 'index'])->name('users');
        Route::get('/users/create', [UsersController::class, 'create'])->name('users.create');
        Route::post('/users', [UsersController::class, 'store'])->name('users.store');
        Route::get('/users/{user}/edit', [UsersController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UsersController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UsersController::class, 'destroy'])->name('users.destroy');
        Route::post('/users/{id}/restore', [UsersController::class, 'restore'])->name('users.restore');
        Route::delete('/users/{id}/force-delete', [UsersController::class, 'forceDelete'])->name('users.force-delete');
        Route::get('/users/export', [UsersController::class, 'export'])->name('users.export');

        // Product routes
        Route::get('products/export', [ProductController::class, 'export'])->name('products.export'); // Export route must be defined before resource route
        Route::get('products/trashed', [ProductController::class, 'trashed'])->name('products.trashed');
        Route::post('products/{id}/restore', [ProductController::class, 'restore'])->name('products.restore');
        Route::delete('products/{id}/force-delete', [ProductController::class, 'forceDelete'])->name('products.force-delete');
        Route::resource('products', ProductController::class);

        // Category Management
        Route::get('/categories/trashed', [CategoryController::class, 'trashed'])->name('categories.trashed');
        Route::post('/categories/{id}/restore', [CategoryController::class, 'restore'])->name('categories.restore');
        Route::delete('/categories/{id}/force-delete', [CategoryController::class, 'forceDelete'])->name('categories.force-delete');
        Route::get('/categories/export', [CategoryController::class, 'export'])->name('categories.export');
        Route::resource('/categories', CategoryController::class);

        // Admin management (super_admin only)
        Route::middleware(['admin:super_admin'])->group(function () {
            Route::get('/admins', [DashboardController::class, 'adminsList'])->name('admins');
            Route::get('/admins/create', [DashboardController::class, 'createAdminForm'])->name('admins.create');
            Route::post('/admins', [DashboardController::class, 'storeAdmin'])->name('admins.store');
            Route::get('/admins/{admin}/edit', [DashboardController::class, 'editAdminForm'])->name('admins.edit');
            Route::put('/admins/{admin}', [DashboardController::class, 'updateAdmin'])->name('admins.update');
            Route::delete('/admins/{admin}', [DashboardController::class, 'destroyAdmin'])->name('admins.destroy');
            Route::post('/admins/{admin}/restore', [DashboardController::class, 'restoreAdmin'])->name('admins.restore');
            Route::delete('/admins/{admin}/force-delete', [DashboardController::class, 'forceDeleteAdmin'])->name('admins.force-delete');
            Route::get('/admins/export', [DashboardController::class, 'exportAdmins'])->name('admins.export');

            // Product best seller toggle route
            Route::post('/products/{product}/toggle-best-seller', [ProductController::class, 'toggleBestSeller'])->name('products.toggle-best-seller');

            // Site Settings Routes
            Route::get('/settings', [App\Http\Controllers\Admin\SiteSettingsController::class, 'index'])->name('settings.index');
            Route::post('/settings', [App\Http\Controllers\Admin\SiteSettingsController::class, 'update'])->name('settings.update');
            Route::post('/settings/reset', [App\Http\Controllers\Admin\SiteSettingsController::class, 'reset'])->name('settings.reset');
        });

        // Chat sessions management
        Route::get('/chat-sessions', [App\Http\Controllers\Admin\ChatSessionController::class, 'index'])->name('chat_sessions.index');
        Route::get('/chat-sessions/{id}', [App\Http\Controllers\Admin\ChatSessionController::class, 'show'])->name('chat_sessions.show');
        Route::put('/chat-sessions/{id}/update', [App\Http\Controllers\Admin\ChatSessionController::class, 'update'])->name('chat_sessions.update');
        Route::put('/chat-sessions/{id}/status', [App\Http\Controllers\Admin\ChatSessionController::class, 'updateStatus'])->name('chat_sessions.update-status');
        Route::delete('/chat-sessions/{id}', [App\Http\Controllers\Admin\ChatSessionController::class, 'destroy'])->name('chat_sessions.destroy');

        // Chat session actions
        Route::post('/chat-sessions/{id}/close', [App\Http\Controllers\Admin\ChatSessionController::class, 'close'])->name('chat_sessions.close');
        Route::post('/chat-sessions/{id}/reopen', [App\Http\Controllers\Admin\ChatSessionController::class, 'reopen'])->name('chat_sessions.reopen');
        Route::post('/chat-sessions/{id}/flag', [App\Http\Controllers\Admin\ChatSessionController::class, 'flag'])->name('chat_sessions.flag');
        Route::post('/chat-sessions/{id}/archive', [App\Http\Controllers\Admin\ChatSessionController::class, 'archive'])->name('chat_sessions.archive');
        Route::post('/chat-sessions/{id}/tag', [App\Http\Controllers\Admin\ChatSessionController::class, 'addTag'])->name('chat_sessions.add-tag');
        Route::delete('/chat-sessions/{id}/tag', [App\Http\Controllers\Admin\ChatSessionController::class, 'removeTag'])->name('chat_sessions.remove-tag');
        Route::get('/chat-sessions/{id}/export', [App\Http\Controllers\Admin\ChatSessionController::class, 'export'])->name('chat_sessions.export');

        // Message management in a session
        Route::post('/chat-sessions/{id}/message', [App\Http\Controllers\Admin\ChatSessionController::class, 'addMessage'])->name('chat_sessions.add-message');
        Route::delete('/chat-sessions/{sessionId}/messages/{messageId}', [App\Http\Controllers\Admin\ChatSessionController::class, 'deleteMessage'])->name('chat_sessions.delete-message');
        Route::post('/chat-sessions/{sessionId}/messages/{messageId}/flag', [App\Http\Controllers\Admin\ChatSessionController::class, 'flagMessage'])->name('chat_sessions.flag-message');
        Route::get('/chat-sessions/export/all', [App\Http\Controllers\Admin\ChatSessionController::class, 'exportAll'])->name('chat_sessions.export.all');

        // AliExpress Management Routes
        Route::prefix('aliexpress')->name('aliexpress.')->group(function () {
            // AliExpress Products
            Route::get('products/export', [App\Http\Controllers\Admin\AliExpressProductController::class, 'export'])->name('products.export');
            Route::get('products/trashed', [App\Http\Controllers\Admin\AliExpressProductController::class, 'trashed'])->name('products.trashed');
            Route::post('products/{id}/restore', [App\Http\Controllers\Admin\AliExpressProductController::class, 'restore'])->name('products.restore');
            Route::delete('products/{id}/force-delete', [App\Http\Controllers\Admin\AliExpressProductController::class, 'forceDelete'])->name('products.force-delete');
            Route::resource('products', App\Http\Controllers\Admin\AliExpressProductController::class);

            // AliExpress Categories
            Route::get('categories/export', [App\Http\Controllers\Admin\AliExpressCategoryController::class, 'export'])->name('categories.export');
            Route::resource('categories', App\Http\Controllers\Admin\AliExpressCategoryController::class)->parameters([
                'categories' => 'categoryName'
            ]);

            // AliExpress Chat Sessions
            Route::get('chat-sessions/export', [App\Http\Controllers\Admin\AliExpressChatSessionController::class, 'export'])->name('chat_sessions.export');
            Route::post('chat-sessions/{id}/clear', [App\Http\Controllers\Admin\AliExpressChatSessionController::class, 'clear'])->name('chat_sessions.clear');
            Route::post('chat-sessions/{id}/message', [App\Http\Controllers\Admin\AliExpressChatSessionController::class, 'addMessage'])->name('chat_sessions.add-message');
            Route::delete('chat-sessions/{sessionId}/messages/{messageIndex}', [App\Http\Controllers\Admin\AliExpressChatSessionController::class, 'deleteMessage'])->name('chat_sessions.delete-message');
            Route::resource('chat-sessions', App\Http\Controllers\Admin\AliExpressChatSessionController::class, ['only' => ['index', 'show', 'destroy']])->names('chat_sessions');

            // AliExpress API Testing
            Route::prefix('api')->name('api.')->group(function () {
                Route::get('/', [App\Http\Controllers\Admin\AliExpressApiController::class, 'index'])->name('index');
                Route::post('/test-connection', [App\Http\Controllers\Admin\AliExpressApiController::class, 'testConnection'])->name('test-connection');
                Route::post('/test-search', [App\Http\Controllers\Admin\AliExpressApiController::class, 'testSearch'])->name('test-search');
                Route::post('/test-product-details', [App\Http\Controllers\Admin\AliExpressApiController::class, 'testProductDetails'])->name('test-product-details');
                Route::post('/test-batch-products', [App\Http\Controllers\Admin\AliExpressApiController::class, 'testBatchProducts'])->name('test-batch-products');
                Route::post('/test-helper-products', [App\Http\Controllers\Admin\AliExpressApiController::class, 'testHelperProducts'])->name('test-helper-products');
                Route::post('/test-helper-categories', [App\Http\Controllers\Admin\AliExpressApiController::class, 'testHelperCategories'])->name('test-helper-categories');
                Route::get('/status', [App\Http\Controllers\Admin\AliExpressApiController::class, 'getApiStatus'])->name('status');
            });
        });
    });
});





Route::get('call-helper', function () {
    // Get products from AliExpress API
    $products = AliExpressProducts();

    // Return the products
    return $products;
});

// Ensure chatbot CSS exists for both chatbots
Route::get('/publish-chatbot-styles', function () {
    $cssPath = public_path('css/chatbot.css');

    // Check if the file already exists
    if (!file_exists($cssPath)) {
        // Copy the file from resources to public
        if (file_exists(resource_path('css/chatbot.css'))) {
            copy(resource_path('css/chatbot.css'), $cssPath);
            return "Chatbot styles published successfully!";
        } else {
            return "Chatbot styles source file not found!";
        }
    }

    return "Chatbot styles already published!";
});

require __DIR__ . '/auth.php';
