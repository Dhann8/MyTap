<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WaServerController extends Controller
{
    private string $waServerUrl;

    public function __construct()
    {
        $this->waServerUrl = rtrim(get_setting('wa_gateway_url', env('WA_SERVER_URL', 'http://localhost:3000')), '/');
    }

    // Tampilkan Dashboard Monitoring
    public function index(): View
    {
        try {
            $response = Http::timeout(3)->get("{$this->waServerUrl}/queue");
            $data = $response->successful() ? $response->json('data') : null;
        } catch (\Exception $e) {
            $data = null;
        }

        return view('admin.wa-dashboard', compact('data'));
    }

    public function updateDelay(Request $request): RedirectResponse
    {
        $request->validate([
            'min_delay_sec' => 'required|numeric|min:1',
            'max_delay_sec' => 'required|numeric|gte:min_delay_sec',
        ], [
            'min_delay_sec.min' => 'Delay minimal minimal 1 detik.',
            'max_delay_sec.gte' => 'Delay maksimal tidak boleh lebih kecil dari delay minimal.',
        ]);

        try {
            $response = Http::timeout(5)->post("{$this->waServerUrl}/config", [
                'min_delay_sec' => $request->min_delay_sec,
                'max_delay_sec' => $request->max_delay_sec,
            ]);

            if ($response->successful()) {
                return redirect()->back()->with('success', 'Jeda/Delay pengiriman WA berhasil diperbarui!');
            }

            return redirect()->back()->with('error', $response->json('message') ?? 'Gagal memperbarui konfigurasi.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal terhubung ke Server WA Node.js');
        }
    }

    public function statusJson(): JsonResponse
    {
        try {
            $response = Http::timeout(2)->get(rtrim(get_setting('wa_gateway_url', env('WA_SERVER_URL', 'http://localhost:3000')), '/').'/queue');

            return response()->json($response->json());
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Node.js Server Offline'], 500);
        }
    }
}
