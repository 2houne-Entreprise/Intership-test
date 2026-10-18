<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetOtpController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])
        ->name('register');

    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::get('forgot-password', [PasswordResetOtpController::class, 'requestForm'])
        ->name('password.request');

    Route::post('forgot-password', [PasswordResetOtpController::class, 'send'])
        ->middleware('throttle:password-otp-send')
        ->name('password.email');

    Route::get('password/verify-code', [PasswordResetOtpController::class, 'verifyForm'])
        ->name('password.otp.verify');

    Route::post('password/verify-code', [PasswordResetOtpController::class, 'verify'])
        ->middleware('throttle:password-otp-verify')
        ->name('password.otp.verify.store');

    Route::post('password/resend-code', [PasswordResetOtpController::class, 'resend'])
        ->middleware('throttle:password-otp-send')
        ->name('password.otp.resend');

    Route::get('password/reset', [PasswordResetOtpController::class, 'resetForm'])
        ->name('password.reset');

    Route::post('password/reset', [PasswordResetOtpController::class, 'reset'])
        ->middleware('throttle:password-otp-verify')
        ->name('password.store');
});

Route::middleware('auth')->group(function () {
    Route::get('verify-email', EmailVerificationPromptController::class)
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});
