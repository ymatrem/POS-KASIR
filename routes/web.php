<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CashierController;

// Auth Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'authenticate'])->name('authenticate')->middleware('guest');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register')->middleware('guest');
Route::post('/register', [AuthController::class, 'register'])->name('register.store')->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Protected Routes
Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::get('/api/chart-data', [DashboardController::class, 'getChartData']);
    Route::get('/api/payment-data', [DashboardController::class, 'getPaymentData']);

    Route::resource('categories', CategoryController::class);
    Route::resource('menus', MenuController::class);
    Route::resource('orders', OrderController::class);
    
    // Upload image route
    Route::post('/menus/upload-image', [MenuController::class, 'uploadImage'])->name('menus.upload-image');

    // Cashier Routes - Only for cashier role
    Route::middleware('cashier')->group(function () {
        Route::get('/cashier', [CashierController::class, 'index'])->name('cashier.index');
        Route::post('/cashier/add-to-cart', [CashierController::class, 'addToCart'])->name('cashier.add-to-cart');
        Route::put('/cashier/update-cart/{menuId}', [CashierController::class, 'updateCart'])->name('cashier.update-cart');
        Route::delete('/cashier/remove-from-cart/{menuId}', [CashierController::class, 'removeFromCart'])->name('cashier.remove-from-cart');
        Route::get('/cashier/get-cart', [CashierController::class, 'getCart'])->name('cashier.get-cart');
        Route::post('/cashier/checkout', [CashierController::class, 'checkout'])->name('cashier.checkout');
        Route::post('/cashier/clear-cart', [CashierController::class, 'clearCart'])->name('cashier.clear-cart');
        Route::get('/cashier/print-receipt/{orderId}', [CashierController::class, 'printReceipt'])->name('cashier.print-receipt');
    });
});
