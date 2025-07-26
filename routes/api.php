<?php

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::controller(AuthController::class)->group(function () {
        Route::post('/login', 'login')->name('api.auth.login');
        Route::post('/register', 'register')->name('api.auth.register');
    });
});

Route::middleware('auth:sanctum')->group(function () {
    Route::controller(AuthController::class)->group(function () {
        Route::post('/logout', 'logout')->name('api.auth.logout');
    });
});
