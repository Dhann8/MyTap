<?php

use App\Http\Controllers\AttendanceController;
use App\Models\Setting;
use App\Services\JsonDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::controller(AttendanceController::class)->group(function () {
    Route::match(['get', 'post'], '/scan-rfid', 'scanRfid');
    Route::get('/attendance/autocomplete', 'autocomplete');
    Route::post('/attendance/check-late', function (Request $request) {
        $force = $request->boolean('force', true);
        $waUrl = rtrim(get_setting('wa_gateway_url', 'http://localhost:3000'), '/');
        $apiKey = get_setting('wa_api_key', 'base64:Sp2BUoC+1/isTIbAHbGqVCmluBXcmT9M1HMDxPsnBwo=');
        try {
            $response = \Illuminate\Support\Facades\Http::timeout(5)
                ->withHeaders(['x-api-key' => $apiKey])
                ->post("{$waUrl}/check-late", ['force' => $force]);
            return response()->json($response->json(), $response->status());
        } catch (\Throwable $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    });
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
        try {
            $ip = Setting::getByKey('server_ip', '10.117.3.92:8000');
            $wifiSsid = Setting::getByKey('wifi_ssid', 'Nama_WiFi_Default');
            $wifiPass = Setting::getByKey('wifi_password', 'Password_WiFi_Default');
        } catch (\Throwable $e) {
            // Fallback jika query database error
            $ip = '10.117.3.92:8000';
            $wifiSsid = 'Nama_WiFi_Default';
            $wifiPass = 'Password_WiFi_Default';
        }
        
        // Memastikan format URL menyertakan http://
        $serverUrl = str_starts_with($ip, 'http') ? $ip : 'http://' . $ip;

        return response()->json([
            'status'        => 'success',
            'server_ip'     => $ip,
            'server_url'    => $serverUrl,
            'wifi_ssid'     => $wifiSsid,
            'wifi_password' => $wifiPass,
        ], 200);
    });

    Route::post('/device-config', function (Request $request) {
        $wifiKeys = ['wifi_ssid', 'wifi_password', 'server_ip'];

        $validated = $request->validate([
            'wifi_ssid'     => 'required|string|max:64',
            'wifi_password' => 'required|string|max:64',
            'server_ip'     => ['required', 'string', 'max:50'],
        ]);

        // Hapus semua data WiFi lama terlebih dahulu
        Setting::whereIn('key', $wifiKeys)->delete();

        // Insert data baru
        foreach ($validated as $key => $value) {
            Setting::create(['key' => $key, 'value' => $value]);
        }

        $serverUrl = str_starts_with($validated['server_ip'], 'http') 
            ? $validated['server_ip'] 
            : 'http://' . $validated['server_ip'];

        return response()->json([
            'status'  => 'success',
            'message' => 'Konfigurasi WiFi berhasil diperbarui.',
            'data'    => [
                'server_ip'     => $validated['server_ip'],
                'server_url'    => $serverUrl,
                'wifi_ssid'     => $validated['wifi_ssid'],
                'wifi_password' => $validated['wifi_password'],
            ],
        ], 200);
    });
});