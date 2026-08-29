<?php

use App\Models\Setting;

if (! function_exists('get_setting')) {
    function get_setting(string $key, mixed $default = null): mixed
    {
        static $cache = null;

        $defaultSettings = [
            'minimal_datang' => '06:00',
            'jam_masuk' => '07:00',
            'toleransi_terlambat' => '15',
            'jam_pulang' => '15:00',

            'wa_gateway_url' => 'http://localhost:3000',
            'wa_api_key' => 'base64:Sp2BUoC+1/isTIbAHbGqVCmluBXcmT9M1HMDxPsnBwo=',
            'delay_wa' => '3',
            'auto_send_wa' => '1',
            'auto_send_wa_late' => '1',
            'template_wa_hadir' => 'Halo {nama} ({kelas}), terima kasih telah melakukan absensi MASUK pada jam {waktu} ({tanggal}). Selamat belajar!',
            'template_wa_pulang' => 'Halo {nama} ({kelas}), absensi PULANG Anda pada jam {waktu} ({tanggal}) berhasil dicatat. Hati-hati di perjalanan!',
            'template_wa_terlambat' => 'PEMBERITAHUAN: Siswa atas nama {nama} (Kelas {kelas}) tercatat BELUM HADIR / TERLAMBAT di sekolah hingga jam {waktu} ({tanggal}). Mohon segera konfirmasi kepada pihak sekolah jika berhalangan hadir.',

            'app_name' => 'SMA Negeri 1 Digital',
            'theme_mode' => 'light',
            'language' => 'id',

            'wifi_ssid' => '',
            'wifi_password' => '',
        ];

        // Load all settings from DB once per request (static cache)
        if ($cache === null) {
            try {
                $cache = Setting::pluck('value', 'key')->toArray();
            } catch (Exception $e) {
                $cache = [];
            }
        }

        if (array_key_exists($key, $cache)) {
            return $cache[$key];
        }

        return $default ?? ($defaultSettings[$key] ?? null);
    }
}

if (! function_exists('all_settings')) {
    function all_settings(): array
    {
        $keys = [
            'minimal_datang', 'jam_masuk', 'toleransi_terlambat', 'jam_pulang',
            'wa_gateway_url', 'wa_api_key', 'delay_wa', 'auto_send_wa', 'auto_send_wa_late',
            'template_wa_hadir', 'template_wa_pulang', 'template_wa_terlambat',
            'app_name', 'theme_mode', 'language', 'wifi_ssid', 'wifi_password',
        ];

        $result = [];
        foreach ($keys as $key) {
            $result[$key] = get_setting($key);
        }

        return $result;
    }
}
