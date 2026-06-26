<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    /**
     * Tampilan Dashboard Web: Menampilkan data log absensi.
     */
    public function index(Request $request)
    {
        $search = $request->query('search');

        // Mengambil log absensi beserta relasi data usernya harian
        $attendances = Attendance::with(['user'])
            ->when($search, function ($query, $search) {
                return $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('uid', 'like', "%{$search}%");
                });
            })
            ->latest('date')
            ->latest('time_in')
            ->paginate(10)
            ->withQueryString();

        return view('pages.attendance.index', compact('attendances'));
    }

    /**
     * Jalur API Alat RFID: Mencatat kehadiran saat kartu di-tap.
     */
    public function scanRfid(Request $request)
    {
        // 1. Validasi string UID dari alat RFID
        $request->validate([
            'uid' => 'required|string',
        ]);

        // 2. Cari user berdasarkan kartu UID yang dikirim
        $user = User::where('uid', $request->uid)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Kartu RFID tidak dikenali atau belum terdaftar.',
            ], 404);
        }

        $today = Carbon::today()->toDateString();

        // 3. Sistem pengaman: Mencegah double tap di hari yang sama
        $alreadyTapped = Attendance::where('user_id', $user->id)
            ->where('date', $today)
            ->exists();

        if ($alreadyTapped) {
            return response()->json([
                'success' => false,
                'message' => 'Halo ' . $user->name . ', Anda sudah melakukan absensi hari ini.',
            ], 400);
        }

        // 4. Buat data kehadiran baru (Murni masuk, tanpa data pulangnya)
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date'    => $today,
            'time_in' => Carbon::now()->toTimeString(), // Mengambil format H:i:s jam lokal saat ini
            'status'  => 'Hadir',
        ]);

        // 5. Kirim respon balik ke mesin mikrokontroler RFID
        return response()->json([
            'success' => true,
            'message' => 'Absen masuk berhasil dicatat!',
            'data'    => [
                'nama'    => $user->name,
                'tanggal' => $attendance->date,
                'jam'     => $attendance->time_in,
                'status'  => $attendance->status,
            ],
        ]);
    }
}