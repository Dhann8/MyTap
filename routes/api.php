<?php

use App\Http\Controllers\AttendanceController;
use Illuminate\Support\Facades\Route;

Route::controller(AttendanceController::class)->group(function () {
    Route::post('/scan-rfid', 'scanRfid');
    Route::get('/attendance/autocomplete', 'autocomplete');
});

Route::prefix('json')->group(function () {
    Route::get('/users', function () {
        return response()->json(\App\Services\JsonDatabase::getUsers());
    });

    Route::get('/attendances', function () {
        return response()->json(\App\Services\JsonDatabase::getAttendances());
    });

    Route::get('/absensi-log', function () {
        $path = 'absensi_log.json';
        if (\Illuminate\Support\Facades\Storage::disk('local')->exists($path)) {
            $content = \Illuminate\Support\Facades\Storage::disk('local')->get($path);
            return response()->json(json_decode($content, true) ?? []);
        }
        return response()->json([]);
    });
});
