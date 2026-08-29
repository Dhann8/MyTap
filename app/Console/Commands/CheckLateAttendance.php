<?php

namespace App\Console\Commands;

use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CheckLateAttendance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:check-late {--force : Force send notification even before late cutoff time}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Periksa dan kirimkan notifikasi WA ke siswa yang terlambat atau belum melakukan absensi masuk.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $force = $this->option('force');
        $this->info('Memulai pengecekan siswa yang belum absen/terlambat...');

        $autoSendLate = get_setting('auto_send_wa_late', '1');
        if ($autoSendLate !== '1' && !$force) {
            $this->warn('Pengiriman pesan terlambat otomatis dinonaktifkan di pengaturan.');
            return self::SUCCESS;
        }

        $waGatewayUrl = rtrim(get_setting('wa_gateway_url', 'http://localhost:3000'), '/');
        $apiKey = get_setting('wa_api_key', 'base64:Sp2BUoC+1/isTIbAHbGqVCmluBXcmT9M1HMDxPsnBwo=');

        try {
            // Panggil endpoint /check-late di server Node.js WA
            $response = Http::timeout(10)->withHeaders([
                'x-api-key' => $apiKey,
            ])->post("{$waGatewayUrl}/check-late", [
                'force' => $force,
            ]);

            if ($response->successful()) {
                $data = $response->json('data');
                $count = $data['count'] ?? 0;
                $msg = $data['message'] ?? "Selesai. {$count} siswa diproses.";
                $this->info("[SUKSES] {$msg}");
                return self::SUCCESS;
            }

            $this->error("[GAGAL] Node server merespons error: " . ($response->json('message') ?? 'Unknown error'));
            return self::FAILURE;
        } catch (\Throwable $e) {
            $this->error("[ERROR] Gagal terhubung ke Server WA Node.js: " . $e->getMessage());
            Log::error("Command attendance:check-late gagal: " . $e->getMessage());
            return self::FAILURE;
        }
    }
}
