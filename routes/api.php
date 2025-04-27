<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PasswordResetController;

// Registration flow
Route::post('/register', [AuthController::class, 'register']);
Route::get('/email/verify/{hashedUserId}/{token}', [AuthController::class, 'verifyEmail'])
    ->name('verification.verify');

// Login flow
Route::post('/login', [AuthController::class, 'login']);

// Password reset flow
Route::post('/password/forgot', [PasswordResetController::class, 'sendResetOtp']);
Route::post('/password/reset', [PasswordResetController::class, 'resetPassword']);

// Authenticated routes
Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    // Add other protected routes here
});