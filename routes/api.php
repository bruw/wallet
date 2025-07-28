<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Wallet\WalletController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::controller(AuthController::class)->group(function () {
        Route::post('/login', 'login')->name('api.auth.login');
        Route::post('/register', 'register')->name('api.auth.register');
    });
});

Route::middleware('auth:sanctum')->group(function () {
    Route::controller(AuthController::class)->group(function () {
        Route::delete('/logout', 'logout')->name('api.auth.logout');
    });
    Route::controller(WalletController::class)->group(function () {
        Route::get('/wallet', 'view')->name('api.wallets.view');
        Route::post('/wallet/deposit', 'deposit')->name('api.wallets.deposits.create');
    });
});
