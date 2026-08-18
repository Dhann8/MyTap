<?php

use App\Http\Controllers\AttendanceController;
use App\Models\Setting;
use App\Services\JsonDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::controller(AttendanceController::class)->group(function () {
    Route::post('/scan-rfid', 'scanRfid');
    Route::get('/attendance/autocomplete', 'autocomplete');
});

Route::prefix('json')->group(function () {
    Route::get('/users', function () {
        return response()->json(JsonDatabase::getUsers());
    });

    Route::get('/attendances', function () {
        return response()->json(JsonDatabase::getAttendances());
    });

    Route::get('/absensi-log', function () {
        $path = 'absensi_log.json';
        if (Storage::disk('local')->exists($path)) {
            $content = Storage::disk('local')->get($path);

            return response()->json(json_decode($content, true) ?? []);
        }

        return response()->json([]);
    });

    Route::get('/device-config', function () {
        return response()->json([
            'status' => 'success',
            'server_ip' => Setting::getByKey('server_ip', '192.168.1.1:8000'),
            'wifi_ssid' => Setting::getByKey('wifi_ssid', 'Nama_WiFi_Default'),
            'wifi_password' => Setting::getByKey('wifi_password', 'Password_WiFi_Default'),
        ], 200);
    });

    Route::post('/device-config', function (Request $request) {
        $wifiKeys = ['wifi_ssid', 'wifi_password', 'server_ip'];

        $validated = $request->validate([
            'wifi_ssid' => 'required|string|max:64',
            'wifi_password' => 'required|string|max:64',
            'server_ip' => ['required', 'string', 'max:21', 'regex:/^\d{1,3}(\.\d{1,3}){3}:\d{1,5}$/'],
        ]);

        // Hapus semua data WiFi lama terlebih dahulu (pastikan hanya 1 record)
        Setting::whereIn('key', $wifiKeys)->delete();

        // Insert data baru
        foreach ($validated as $key => $value) {
            Setting::create(['key' => $key, 'value' => $value]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Konfigurasi WiFi berhasil diperbarui.',
            'data' => [
                'server_ip' => $validated['server_ip'],
                'wifi_ssid' => $validated['wifi_ssid'],
                'wifi_password' => $validated['wifi_password'],
            ],
        ], 200);
    });
});
