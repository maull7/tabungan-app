<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\TransactionApprovalController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MidtransWebhookController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->controller(AuthController::class)->group(function () {
    Route::get('/login', 'showLogin')->name('login');
    Route::post('/login', 'login');
    Route::get('/register', 'showRegister')->name('register');
    Route::post('/register', 'register');
});

Route::post('/midtrans/notifications', MidtransWebhookController::class)->name('midtrans.notifications');

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/', DashboardController::class)->name('dashboard');

    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', AdminDashboardController::class)->name('dashboard');
        Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
        Route::post('/transactions/{transaction}/approve', [TransactionApprovalController::class, 'store'])->name('transactions.approve');
    });

    Route::prefix('transactions')->name('transactions.')->controller(TransactionController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->middleware('throttle:5,1')->name('store');
        Route::post('/withdraw', 'storeWithdrawal')->middleware('throttle:5,1')->name('withdraw');
        Route::get('/{transaction}', 'show')->name('show');
        Route::get('/{transaction}/receipt', 'receipt')->name('receipt');
    });
});
