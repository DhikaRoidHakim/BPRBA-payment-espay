<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EspayController;
use App\Http\Controllers\VirtualAccountController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\AuthController;



Route::middleware('guest')->group(function () {
    // Route Login
    Route::get('login', [AuthController::class, 'showFromLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->name('login.process');
});


Route::middleware('auth')->group(function () {

    // Route Virtual Account
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('va', VirtualAccountController::class);

    // Route Transaksi
    Route::get('transactions', [TransactionController::class, 'index'])->name('transactions.index');
    Route::get('transactions/{id}', [TransactionController::class, 'show'])->name('transactions.show');

    // Route Logout
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
});


// Espay Callback
Route::post('/api/v1.0/transfer-va/payment', [EspayController::class, 'receive'])
    ->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class);

// Testing
Route::post('/api/v1.0/testing-with-body', [EspayController::class, 'testingWithBody'])
    ->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class);
