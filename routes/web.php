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
    
    // API untuk fetch notification details (lebih aman dari broadcast)
    Route::get('api/notifications/latest', function () {
        return response()->json([
            'unread_count' => auth()->user()->unreadNotifications->count(),
            'notifications' => auth()->user()->notifications()->take(10)->get()
        ]);
    })->name('api.notifications.latest');

    // Route Notifikasi
    Route::post('/notifications/read', function (\Illuminate\Http\Request $request) {
        $request->user()->unreadNotifications->markAsRead();
        return back();
    })->name('notifications.read')->middleware('auth');

    // Route Logout
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    
    // Testing Notification
    Route::get('/test-notification', function () {
        $user = auth()->user();
        $transaction = new \App\Models\Transaction([
            'trx_id' => 'TEST-' . time(),
            'va_number' => '1234567890',
            'paid_amount' => 100000,
        ]);
        
        $user->notify(new \App\Notifications\PaymentReceivedNotification($transaction));
        
        return response()->json([
            'status' => 'success',
            'message' => 'Notification sent to user ID: ' . $user->id,
            'channel' => 'App.Models.User.' . $user->id
        ]);
    })->name('test.notification');
});


// Espay Callback
Route::post('/api/v1.0/transfer-va/payment', [EspayController::class, 'receive'])
    ->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class);

// Testing
Route::post('/api/v1.0/testing-with-body', [EspayController::class, 'testingWithBody'])
    ->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class);
