<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');

        $attendances = Attendance::with(['user'])
            ->when($search, function ($query, $search) {
                return $query->whereHas('user', function ($q) use ($search) {
                    $q->where(function ($innerQuery) use ($search) {
                        $innerQuery->where('name', 'like', "%{$search}%")
                                   ->orWhere('uid', 'like', "%{$search}%");
                    });
                });
            })
            ->latest('date')
            ->latest('time_in')
            ->paginate(10)
            ->withQueryString();

        return view('pages.attendance.index', compact('attendances'));
    }

    public function scanRfid(Request $request)
    {
        $request->validate([
            'uid' => 'required|string',
        ]);

        $user = User::where('uid', $request->uid)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Kartu RFID tidak dikenali atau belum terdaftar.',
            ], 404);
        }

        $today = Carbon::today()->toDateString();

        $alreadyTapped = Attendance::where('user_id', $user->id)
            ->where('date', $today)
            ->exists();

        if ($alreadyTapped) {
            return response()->json([
                'success' => false,
                'message' => 'Halo ' . $user->name . ', Anda sudah melakukan absensi hari ini.',
            ], 400);
        }

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date'    => $today,
            'time_in' => Carbon::now()->toTimeString(), 
            'status'  => 'Hadir',
        ]);

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

public function autocomplete(Request $request)
{
    $keyword = $request->query('keyword');
    $date = $request->query('date');

    if (empty($keyword) && empty($date)) {
        return response()->json([]);
    }

    $attendances = Attendance::with(['user'])
        ->when($keyword, function ($query, $keyword) {
            return $query->whereHas('user', function ($q) use ($keyword) {
                $q->where(function ($innerQuery) use ($keyword) {
                    $innerQuery->where('name', 'like', "%{$keyword}%")
                               ->orWhere('uid', 'like', "%{$keyword}%");
                });
            });
        })
        ->when($date, function ($query, $date) {
            return $query->where('date', $date);
        })
        ->latest('date')
        ->take(10) 
        ->get();

    $results = $attendances->map(function ($attendance) {
        return [
            'id'              => $attendance->id,
            'uid'             => $attendance->user->uid ?? '-',
            'name'            => $attendance->user->name ?? 'User Terhapus',
            'email'           => $attendance->user->email ?? '-',
            'date'            => $attendance->date,
            'date_formatted'  => \Carbon\Carbon::parse($attendance->date)->translatedFormat('d F Y'),
            'time_in'         => $attendance->time_in,
            'status'          => $attendance->status ?? 'Hadir',
        ];
    });

    return response()->json($results);
}
}