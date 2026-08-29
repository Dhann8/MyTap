<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse; // <-- Tambahkan ini
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SettingController extends Controller
{
    private array $defaultSettings = [
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
        'server_ip' => '10.117.3.92:8000',
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

        if (! isset($data['auto_send_wa'])) {
            $data['auto_send_wa'] = '0';
        }

        if (! isset($data['auto_send_wa_late'])) {
            $data['auto_send_wa_late'] = '0';
        }

        foreach ($data as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        if (isset($data['delay_wa'])) {
            try {
                $waUrl = rtrim($data['wa_gateway_url'] ?? get_setting('wa_gateway_url', 'http://localhost:3000'), '/');
                $apiKey = $data['wa_api_key'] ?? get_setting('wa_api_key', '');
                Http::timeout(3)->withHeaders([
                    'x-api-key' => $apiKey,
                ])->post("{$waUrl}/config", [
                    'min_delay_sec' => (float) $data['delay_wa'],
                    'max_delay_sec' => (float) $data['delay_wa'] + 2,
                ]);
            } catch (\Exception $e) {
                // Abaikan jika server offline
            }
        }

        return redirect()->route('settings.index')->with('success', 'Pengaturan berhasil disimpan dan diterapkan ke seluruh sistem!');
    }

    /**
     * Trigger manual untuk memeriksa dan mengirimkan notifikasi siswa yang terlambat
     */
    public function triggerCheckLate(Request $request): RedirectResponse
    {
        $waUrl = rtrim(get_setting('wa_gateway_url', 'http://localhost:3000'), '/');
        $apiKey = get_setting('wa_api_key', '');

        try {
            $response = Http::timeout(5)->withHeaders([
                'x-api-key' => $apiKey,
            ])->post("{$waUrl}/check-late", [
                'force' => true,
            ]);

            if ($response->successful()) {
                $data = $response->json('data');
                $count = $data['count'] ?? 0;
                $msg = $data['message'] ?? "Pengecekan keterlambatan selesai. {$count} notifikasi dikirim.";
                return redirect()->route('settings.index')->with('success', $msg);
            }

            return redirect()->route('settings.index')->with('error', 'Gagal memicu pengecekan: ' . ($response->json('message') ?? 'Server WA Error'));
        } catch (\Throwable $e) {
            return redirect()->route('settings.index')->with('error', 'Gagal terhubung ke Server WhatsApp: ' . $e->getMessage());
        }
    }

    /**
     * Endpoint API JSON untuk dibaca oleh ESP32
     */
    public function getDeviceConfig(): JsonResponse
    {
        $settings = Setting::pluck('value', 'key')->toArray();
        $settings = array_merge($this->defaultSettings, $settings);

        // Format server_ip agar dipastikan mengandung 'http://'
        $serverIp = $settings['server_ip'];
        if (!str_starts_with($serverIp, 'http://') && !str_starts_with($serverIp, 'https://')) {
            $serverIp = 'http://' . $serverIp;
        }

        return response()->json([
            'status'        => 'success',
            'wifi_ssid'     => $settings['wifi_ssid'],
            'wifi_password' => $settings['wifi_password'],
            'server_url'    => $serverIp,
        ], 200);
    }
}