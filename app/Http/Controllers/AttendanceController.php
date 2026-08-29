<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\User;
use App\Services\JsonDatabase;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AttendanceController extends Controller
{
    /**
     * Helper aman untuk membaca setting tanpa membuat skrip terhenti (hanging)
     */
    private function safeGetSetting(string $key, string $default): string
    {
        try {
            if (function_exists('get_setting')) {
                return (string) (get_setting($key, $default) ?? $default);
            }
        } catch (\Throwable $e) {
            Log::warning("Gagal membaca setting '{$key}': " . $e->getMessage());
        }
        return $default;
    }

    private function getMappedAttendances(): Collection
    {
        $dbUsers = User::all()->keyBy('id');
        $attendances = JsonDatabase::getAttendances();

        return $attendances->map(function ($att) use ($dbUsers) {
            $dbUser = $dbUsers->get($att['user_id']);

            if ($dbUser) {
                $userObj = (object) [
                    'id' => $dbUser->id,
                    'name' => $dbUser->name,
                    'email' => $dbUser->email,
                    'uid' => $dbUser->uid,
                    'role' => $dbUser->role,
                    'kelas' => $dbUser->kelas,
                    'no_hp' => $dbUser->no_hp,
                ];
            } else {
                $userObj = (object) [
                    'id' => null,
                    'name' => 'User Terhapus',
                    'email' => '-',
                    'uid' => '-',
                    'role' => 'user',
                    'kelas' => '-',
                    'no_hp' => null,
                ];
            }

            return (object) [
                'id' => $att['id'],
                'user_id' => $att['user_id'],
                'date' => $att['date'],
                'time_in' => $att['time_in'],
                'time_out' => $att['time_out'] ?? null,
                'status' => $att['status'],
                'user' => $userObj,
            ];
        });
    }

    public function index(Request $request): View
    {
        $search = $request->query('search');
        $dateFilter = $request->query('date', Carbon::today()->toDateString());

        $attendances = $this->getMappedAttendances();

        if ($dateFilter) {
            $attendances = $attendances->filter(function ($att) use ($dateFilter) {
                return $att->date === $dateFilter;
            });
        }

        if ($search) {
            $attendances = $attendances->filter(function ($att) use ($search) {
                $searchLower = strtolower($search);
                $nameMatch = $att->user && str_contains(strtolower($att->user->name), $searchLower);
                $uidMatch = $att->user && str_contains(strtolower($att->user->uid), $searchLower);

                return $nameMatch || $uidMatch;
            });
        }

        $attendances = $attendances->sortBy(function ($att) {
            return $att->user->name;
        })->values();

        $page = LengthAwarePaginator::resolveCurrentPage() ?: 1;
        $perPage = 10;
        $slicedItems = $attendances->slice(($page - 1) * $perPage, $perPage)->values();

        $paginator = new LengthAwarePaginator(
            $slicedItems,
            $attendances->count(),
            $perPage,
            $page,
            [
                'path' => LengthAwarePaginator::resolveCurrentPath(),
                'query' => $request->query(),
            ]
        );

        $availableClasses = User::whereNotNull('kelas')
            ->where('kelas', '!=', '')
            ->distinct()
            ->pluck('kelas');

        return view('attendances.index', [
            'attendances' => $paginator,
            'availableClasses' => $availableClasses,
            'dateFilter' => $dateFilter,
        ]);
    }

    public function scanRfid(Request $request): JsonResponse
    {
        try {
            $uid = trim($request->input('uid'));

            if (empty($uid)) {
                return response()->json([
                    'success' => false,
                    'message' => 'UID RFID tidak boleh kosong.',
                ], 400);
            }

            // Cari user berdasarkan UID
            $user = User::where('uid', $uid)->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kartu RFID tidak dikenali atau belum terdaftar.',
                ], 404);
            }

            $rfidStatus = $user->rfid_status ?? 'active';
            if ($rfidStatus !== 'active') {
                return response()->json([
                    'success' => false,
                    'message' => 'Kartu RFID Anda dinonaktifkan. Hubungi Admin untuk mengaktifkannya.',
                ], 403);
            }

            $today = Carbon::today()->toDateString();
            $currentTime = Carbon::now()->toTimeString();
            $currentTimeCarbon = Carbon::now();

            // Membaca setting dengan fallback aman
            $minimalDatang = $this->safeGetSetting('minimal_datang', '06:00');
            $jamMasuk       = $this->safeGetSetting('jam_masuk', '07:00');
            $jamPulang      = $this->safeGetSetting('jam_pulang', '15:00');

            $minimalDatangCarbon = Carbon::parse($minimalDatang);
            $jamMasukCarbon       = Carbon::parse($jamMasuk);
            $jamPulangCarbon      = Carbon::parse($jamPulang);

            $attendances = JsonDatabase::getAttendances() ?? collect([]);
            $attendanceTodayKey = $attendances->search(function ($att) use ($user, $today) {
                return isset($att['user_id'], $att['date']) && $att['user_id'] == $user->id && $att['date'] === $today;
            });

            // PROSES ABSEN PULANG
            if ($attendanceTodayKey !== false) {
                $attToday = $attendances[$attendanceTodayKey];
                if (isset($attToday['time_out']) && $attToday['time_out'] !== null) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Halo '.$user->name.', Anda sudah melakukan absensi pulang hari ini.',
                    ], 400);
                }

                if ($currentTimeCarbon->lt($jamPulangCarbon)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Belum waktunya pulang. Jam pulang: '.$jamPulang,
                    ], 400);
                }

                $attToday['time_out'] = $currentTime;
                $attendances[$attendanceTodayKey] = $attToday;
                
                try { JsonDatabase::saveAttendances($attendances); } catch (\Throwable $e) {}

                Attendance::updateOrCreate(
                    ['id' => $attToday['id']],
                    ['time_out' => $currentTime]
                );

                // Notifikasi WhatsApp non-blocking untuk Pulang
                if ($this->safeGetSetting('auto_send_wa', '1') === '1') {
                    $this->kirimNotifikasiWAEvent($user, $attToday['date'], $currentTime, 'Pulang', 'pulang');
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Absen pulang berhasil dicatat!',
                    'data' => [
                        'nama' => $user->name,
                        'kelas' => $user->kelas ?? '-',
                        'tanggal' => $attToday['date'],
                        'jam' => $currentTime,
                        'status' => 'Pulang',
                    ],
                ], 200);
            }

            // PROSES ABSEN MASUK
            if ($currentTimeCarbon->lt($minimalDatangCarbon)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Belum waktunya absen. Absen dimulai jam '.$minimalDatang,
                ], 400);
            }

            $status = $currentTimeCarbon->gt($jamMasukCarbon) ? 'Terlambat' : 'Hadir';

            $maxId = $attendances->max('id') ? $attendances->max('id') : 0;
            $newId = $maxId + 1;
            
            $newAttendance = [
                'id' => $newId,
                'user_id' => $user->id,
                'date' => $today,
                'time_in' => $currentTime,
                'time_out' => null,
                'status' => $status,
            ];

            $attendances->push($newAttendance);
            
            try { JsonDatabase::saveAttendances($attendances); } catch (\Throwable $e) {}

            Attendance::updateOrCreate(
                ['id' => $newId],
                [
                    'user_id' => $user->id,
                    'date' => $today,
                    'time_in' => $currentTime,
                    'time_out' => null,
                    'status' => $status,
                ]
            );

            $this->saveToJson($user->name, $uid, $today, $currentTime);

            // Notifikasi WhatsApp non-blocking untuk Masuk
            if ($this->safeGetSetting('auto_send_wa', '1') === '1') {
                $this->kirimNotifikasiWAEvent($user, $today, $currentTime, $status, 'masuk');
            }

            return response()->json([
                'success' => true,
                'message' => 'Absen masuk berhasil dicatat!',
                'data' => [
                    'nama' => $user->name,
                    'kelas' => $user->kelas ?? '-',
                    'tanggal' => $newAttendance['date'],
                    'jam' => $newAttendance['time_in'],
                    'status' => $newAttendance['status'],
                ],
            ], 200);

        } catch (\Throwable $e) {
            Log::error("Error pada scanRfid: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server: ' . $e->getMessage(),
            ], 500);
        }
    }

    private function saveToJson(string $name, string $uid, string $date, string $time): void
    {
        try {
            $fileName = 'absensi_log.json';
            $newData = [
                'nama' => $name,
                'uid' => $uid,
                'tanggal' => $date,
                'jam_masuk' => $time,
            ];

            if (Storage::disk('local')->exists($fileName)) {
                $oldContent = Storage::disk('local')->get($fileName);
                $arrayData = json_decode($oldContent, true) ?? [];
            } else {
                $arrayData = [];
            }

            $arrayData[] = $newData;
            Storage::disk('local')->put($fileName, json_encode($arrayData, JSON_PRETTY_PRINT));
        } catch (\Throwable $e) {
            Log::warning("Gagal menyimpan file absensi_log.json: " . $e->getMessage());
        }
    }

    private function kirimNotifikasiWAEvent(User $user, string $tanggal, string $jam, string $status, string $type = 'masuk'): void
    {
        $baseUrl = rtrim($this->safeGetSetting('wa_gateway_url', 'http://localhost:3000'), '/');
        $apiKey = $this->safeGetSetting('wa_api_key', 'base64:Sp2BUoC+1/isTIbAHbGqVCmluBXcmT9M1HMDxPsnBwo=');

        try {
            // Kirim JSON event ke Node WA server
            Http::timeout(2)
                ->withHeaders([
                    'x-api-key' => $apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post($baseUrl . '/attendance-event', [
                    'user_id' => $user->id,
                    'uid' => $user->uid,
                    'name' => $user->name,
                    'kelas' => $user->kelas ?? '-',
                    'no_hp' => $user->no_hp,
                    'date' => $tanggal,
                    'time' => $jam,
                    'status' => $status,
                    'type' => $type,
                ]);
        } catch (\Throwable $e) {
            Log::warning("Koneksi ke WA Gateway GAGAL/TIMEOUT untuk {$user->name}: " . $e->getMessage());
        }
    }

    public function autocomplete(Request $request): JsonResponse
    {
        $keyword = $request->query('keyword');
        $date = $request->query('date');

        if (empty($keyword) && empty($date)) {
            return response()->json([]);
        }

        $attendances = $this->getMappedAttendances();

        $results = $attendances
            ->when($keyword, function ($collection, $keyword) {
                return $collection->filter(function ($att) use ($keyword) {
                    $keywordLower = strtolower($keyword);
                    $nameMatch = $att->user && str_contains(strtolower($att->user->name), $keywordLower);
                    $uidMatch = $att->user && str_contains(strtolower($att->user->uid), $keywordLower);

                    return $nameMatch || $uidMatch;
                });
            })
            ->when($date, function ($collection, $date) {
                return $collection->filter(function ($att) use ($date) {
                    return $att->date === $date;
                });
            })
            ->sort(function ($a, $b) {
                return strcmp($b->date, $a->date);
            })
            ->take(10)
            ->values();

        $results = $results->map(function ($attendance) {
            return [
                'id' => $attendance->id,
                'uid' => $attendance->user->uid ?? '-',
                'name' => $attendance->user->name ?? 'User Terhapus',
                'email' => $attendance->user->email ?? '-',
                'kelas' => $attendance->user->kelas ?? '-',
                'date' => $attendance->date,
                'date_formatted' => Carbon::parse($attendance->date)->translatedFormat('d F Y'),
                'time_in' => $attendance->time_in,
                'status' => $attendance->status ?? 'Hadir',
            ];
        });

        return response()->json($results);
    }

    public function show(int|string $id): View
    {
        $attendance = $this->getMappedAttendances()->firstWhere('id', $id);

        if (! $attendance) {
            abort(404);
        }

        return view('attendances.show', compact('attendance'));
    }

    public function destroy(int|string $id): RedirectResponse
    {
        Attendance::where('id', $id)->delete();

        $attendances = JsonDatabase::getAttendances();
        $attendances = $attendances->reject(function ($att) use ($id) {
            return $att['id'] == $id;
        });
        JsonDatabase::saveAttendances($attendances);

        return redirect()->route('attendance.index')->with('success', 'Data absensi berhasil dihapus.');
    }

    public function getAllData(Request $request): JsonResponse
    {
        $attendances = $this->getMappedAttendances();

        if ($request->filled('month')) {
            $attendances = $attendances->filter(function ($att) use ($request) {
                return date('m', strtotime($att->date)) === str_pad($request->month, 2, '0', STR_PAD_LEFT);
            });
        }

        if ($request->filled('year')) {
            $attendances = $attendances->filter(function ($att) use ($request) {
                return date('Y', strtotime($att->date)) === (string) $request->year;
            });
        }

        $attendances = $attendances->sortBy(function ($att) {
            return $att->date.' '.($att->user->name ?? '');
        })->values();

        $result = $attendances->map(function ($att) {
            return [
                'id' => $att->id,
                'date' => $att->date,
                'time_in' => $att->time_in,
                'status' => $att->status,
                'user' => [
                    'name' => $att->user->name ?? 'User Terhapus',
                    'uid' => $att->user->uid ?? '-',
                    'kelas' => $att->user->kelas ?? '-',
                ],
            ];
        });

        return response()->json($result);
    }
}