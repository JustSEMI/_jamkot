<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\PanelController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\SettingsController;
use Illuminate\Support\Facades\Route;



// LOGIN PAGE
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// ANTI BRUTE FORCE LOGIN
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'authenticate'])
        ->middleware('throttle:5,1') 
        ->name('login.post');
    //Route::get('/register', [AuthController::class, 'register'])->name('register');
    //Route::post('/register', [AuthController::class, 'store']);
});

// PROTEKSI HALAMAN DASHBOARD & PANEL
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/panel', [PanelController::class, 'index'])->name('panel');
    Route::get('/jadwal', [ScheduleController::class, 'index'])->name('jadwal');
    Route::post('/jadwal', [ScheduleController::class, 'store'])->name('jadwal.store');
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings/reset', [SettingsController::class, 'resetData'])->name('settings.reset');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// PASSWORD RESET
Route::get('/forgotpw', [ResetPasswordController::class, 'showForgotForm'])->name('password.request');
Route::post('/forgotpw', [ResetPasswordController::class, 'checkEmail'])->name('password.check');
Route::get('/resetpw', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/resetpw', [ResetPasswordController::class, 'updatePassword'])->name('password.update');