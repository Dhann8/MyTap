<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WaServerController;
use Illuminate\Support\Facades\Route;

// Guest Routes (Authentication)
Route::middleware('guest')->controller(AuthController::class)->group(function () {
    Route::get('/', 'showLogin')->name('login');
    Route::post('/login', 'login')->name('login.proses');
});

// RFID Scan Endpoint
Route::post('/scan-rfid', [AttendanceController::class, 'scanRfid'])->name('attendance.scan');

// Authenticated Routes
Route::middleware('auth')->group(function () {
    // Dashboard Utama
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard.index');

    // Manajemen Data Absensi
    Route::prefix('attendance')->name('attendance.')->controller(AttendanceController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/search-autocomplete', 'autocomplete')->name('autocomplete');
        Route::get('/all-data', 'getAllData')->name('all-data');
        Route::get('/{id}', 'show')->name('show');
        Route::delete('/{id}', 'destroy')->name('destroy');
    });

    // Manajemen Data Users
    Route::prefix('users')->name('users.')->controller(UserController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::get('/search-autocomplete', 'autocomplete')->name('autocomplete');
        Route::post('/', 'store')->name('store');
        Route::get('/{id}/edit', 'edit')->name('edit');
        Route::put('/{id}', 'update')->name('update');
        Route::patch('/{id}/status', 'updateStatus')->name('update-status');
        Route::delete('/{id}', 'destroy')->name('destroy');
    });

    // Dashboard WA
    Route::prefix('wa')->name('wa.')->controller(WaServerController::class)->group(function () {
        Route::get('/', 'index')->name('dashboard');
        Route::post('/update-delay', 'updateDelay')->name('update-delay');
        Route::get('/status-json', 'statusJson')->name('status-json');
    });

    // Settings
    Route::prefix('settings')->name('settings.')->controller(SettingController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/update', 'update')->name('update');
        Route::get('/check-late', 'triggerCheckLate')->name('check-late');
        Route::get('/update', function () {
            return redirect()->route('settings.index');
        });
    });

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
