<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\WebController;

// ─── Public Frontend ──────────────────────────────────────────────────────────
Route::get('/', [WebController::class, 'home'])->name('home');
Route::get('/about', [WebController::class, 'about'])->name('about');
Route::get('/contact', [WebController::class, 'contact'])->name('contact');
Route::post('/contact', [WebController::class, 'contactPost'])
    ->name('contact.post')
    ->middleware('throttle:6,1');  // Max 6 submissions per minute

Route::get('/up', [WebController::class, 'up'])->name('up');

Route::get('/philosophy', [WebController::class, 'philosophy'])->name('philosophy');
Route::get('/ritual', [WebController::class, 'ritual'])->name('ritual');

// Products
Route::get('/products', [WebController::class, 'products'])->name('products');
Route::get('/product', [WebController::class, 'product'])->name('product');

// Cart & Checkout
Route::get('/cart', [WebController::class, 'cart'])->name('cart');

// Legal
Route::get('/terms', [WebController::class, 'terms'])->name('terms');

// Profile
Route::get('/profile', [WebController::class, 'profile'])->name('profile');

// Social Auth
Route::get('/auth/google', [\App\Http\Controllers\SocialAuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [\App\Http\Controllers\SocialAuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');
Route::get('/auth/google/debug', [\App\Http\Controllers\SocialAuthController::class, 'debugGoogleAuth']);
Route::get('/auth/facebook', [\App\Http\Controllers\SocialAuthController::class, 'redirectToFacebook'])->name('auth.facebook');
Route::get('/auth/facebook/callback', [\App\Http\Controllers\SocialAuthController::class, 'handleFacebookCallback'])->name('auth.facebook.callback');

// ─── Admin ────────────────────────────────────────────────────────────────────
Route::prefix('admin')->name('admin.')->group(function () {
    // Login
    Route::get('/login', [\App\Http\Controllers\AdminController::class, 'showLogin'])->name('login');
    Route::post('/login', [\App\Http\Controllers\AdminController::class, 'login'])
        ->name('login.post')
        ->middleware('throttle:5,15');  // Max 5 login attempts per 15 minutes

    // Dashboard & core management
    Route::get('/', [WebController::class, 'adminDashboard'])->name('dashboard');
    Route::get('/products', [WebController::class, 'adminProducts'])->name('products');
    Route::get('/orders', [WebController::class, 'adminOrders'])->name('orders');
    Route::get('/users', [WebController::class, 'adminUsers'])->name('users');
    Route::get('/reviews', [WebController::class, 'adminReviews'])->name('reviews');
    Route::get('/categories', [WebController::class, 'adminCategories'])->name('categories');
    Route::get('/coupons', [WebController::class, 'adminCoupons'])->name('coupons');
    Route::get('/register', [WebController::class, 'adminRegister'])->name('register');

});
