<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.proses');
});
Route::post('/scan-rfid', [AttendanceController::class, 'scanRfid'])->name('attendance.scan');
Route::middleware(['auth'])->group(function () {
    
    // Dashboard Utama
    Route::prefix('dashboard')->name('dashboard.')->group(function () {
        Route::get('/', [AuthController::class, 'dashboard'])->name('index');
    });

    // Manajemen Data Absensi
    Route::prefix('attendance')->name('attendance.')->group(function () {
        Route::controller(AttendanceController::class)->group(function () {
            Route::get('/', 'index')->name('index'); 
            Route::get('/search-autocomplete', 'autocomplete')->name('autocomplete'); 
            Route::get('/{id}', 'show')->name('show');
        });
    });

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});