<?php
// routes/web.php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderHistoryController;

// Public routes (dapat diakses tanpa login)
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/featured-products', [ProductController::class, 'featured'])->name('products.featured');

// Guest routes (hanya bisa diakses jika belum login)
Route::middleware(['web', 'guest:customer'])->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    // Dalam routes/web.php, tambahkan di middleware(['web', 'guest:customer'])
Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendPasswordResetOtp'])->name('password.send-otp');
Route::get('/reset-password', [AuthController::class, 'showResetPassword'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

// Logout route (untuk authenticated users)
Route::middleware(['web', 'auth:customer'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

    Route::post('/checkout/track', [CheckoutController::class, 'track'])->name('checkout.track');
    Route::post('/checkout/resend-payment-proof', [CheckoutController::class, 'resendPaymentProof'])->name('checkout.resend-payment-proof');

// Protected routes (harus login sebagai customer)
Route::middleware(['web', 'auth:customer'])->group(function () {
    // Product detail hanya bisa diakses setelah login
    Route::get('/products/{product:slug}', [ProductController::class, 'show'])->name('products.show');
        
    // Cart routes
    Route::prefix('cart')->name('cart.')->group(function () {
        Route::get('/', [CartController::class, 'index'])->name('index');
        Route::post('/add', [CartController::class, 'add'])->name('add');
        Route::put('/update', [CartController::class, 'update'])->name('update');
        Route::delete('/remove', [CartController::class, 'remove'])->name('remove');
        Route::delete('/clear', [CartController::class, 'clear'])->name('clear');
        Route::get('/count', [CartController::class, 'getCartCount'])->name('count');
    });
        
    // Checkout routes
    Route::prefix('checkout')->name('checkout.')->group(function () {
        Route::get('/', [CheckoutController::class, 'index'])->name('index');
        Route::post('/', [CheckoutController::class, 'store'])->name('store');
        Route::get('/{order}/payment', [CheckoutController::class, 'payment'])->name('payment');
        Route::post('/{order}/payment-method', [CheckoutController::class, 'paymentMethod'])->name('payment-method');
        Route::get('/{order}/payment-proof', [CheckoutController::class, 'paymentProof'])->name('payment-proof');
        Route::post('/{order}/payment-proof', [CheckoutController::class, 'storePaymentProof'])->name('store-payment-proof');
        Route::get('/{order}/success', [CheckoutController::class, 'success'])->name('success');
        // web.php

    });
    
    // Order History routes
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [OrderHistoryController::class, 'index'])->name('index');
        Route::get('/{order}', [OrderHistoryController::class, 'show'])->name('show');
        Route::put('/{order}/cancel', [OrderHistoryController::class, 'cancel'])->name('cancel');
    });

    // Voucher routes
    Route::prefix('vouchers')->name('vouchers.')->group(function () {
        Route::post('/apply', [CheckoutController::class, 'applyVoucher'])->name('apply');
        Route::delete('/remove', [CheckoutController::class, 'removeVoucher'])->name('remove');
        Route::post('/validate', [CheckoutController::class, 'validateVoucher'])->name('validate');
    });
    
});


