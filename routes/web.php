<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EspayController;
use App\Http\Controllers\VirtualAccountController;
use App\Http\Controllers\TransactionController;




Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::resource('va', VirtualAccountController::class);


Route::get('transactions', [TransactionController::class, 'index'])->name('transactions.index');
Route::get('transactions/{id}', [TransactionController::class, 'show'])->name('transactions.show');

// Espay Callback
Route::post('/api/v1.0/transfer-va/payment', [EspayController::class, 'receive'])
    ->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class);

// Testing With Body
Route::post('/api/v1.0/testing-with-body', [EspayController::class, 'testingWithBody'])
    ->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class);
