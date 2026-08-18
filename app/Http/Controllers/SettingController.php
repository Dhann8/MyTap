<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SettingController extends Controller
{
    /**
     * Daftar default settings yang disederhanakan —
     * sumber kebenaran tunggal ada di SettingHelper.php (all_settings / get_setting).
     */
    private array $defaultSettings = [
        'minimal_datang' => '06:00',
        'jam_masuk' => '07:00',
        'toleransi_terlambat' => '15',
        'jam_pulang' => '15:00',

        'wa_gateway_url' => 'http://localhost:3000',
        'wa_api_key' => 'secret_token_123',
        'delay_wa' => '3',
        'auto_send_wa' => '1',
        'template_wa_hadir' => 'Halo {nama}, terima kasih telah melakukan absensi MASUK pada jam {waktu}. Selamat belajar!',

        'app_name' => 'SMA Negeri 1 Digital',
        'theme_mode' => 'light',
        'language' => 'id',

        'wifi_ssid' => '',
        'wifi_password' => '',
        'server_ip' => '192.168.1.1:8000',
    ];

    public function index(): View
    {
        $settings = Setting::pluck('value', 'key')->toArray();
        $settings = array_merge($this->defaultSettings, $settings);

        return view('setting.index', compact('settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->except(['_token']);

        // Pastikan auto_send_wa selalu tersimpan meski tidak di-centang
        if (! isset($data['auto_send_wa'])) {
            $data['auto_send_wa'] = '0';
        }

        foreach ($data as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        // Sinkronkan delay ke Node.js WA Gateway jika tersedia
        if (isset($data['delay_wa'])) {
            try {
                $waUrl = rtrim($data['wa_gateway_url'] ?? get_setting('wa_gateway_url', 'http://localhost:3000'), '/');
                Http::timeout(3)->post("{$waUrl}/config", [
                    'min_delay_sec' => (float) $data['delay_wa'],
                    'max_delay_sec' => (float) $data['delay_wa'] + 2,
                ]);
            } catch (\Exception $e) {
                // Abaikan jika server offline
            }
        }

        return redirect()->route('settings.index')->with('success', 'Pengaturan berhasil disimpan dan diterapkan ke seluruh sistem!');
    }
}
