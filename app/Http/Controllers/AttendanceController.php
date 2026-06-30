<?php

namespace App\Http\Controllers;

use App\Services\JsonDatabase;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Pagination\LengthAwarePaginator;

class AttendanceController extends Controller
{
    private function getMappedAttendances()
    {
        $dbUsers = \App\Models\User::all()->keyBy('id');
        $attendances = JsonDatabase::getAttendances();

        return $attendances->map(function ($att) use ($dbUsers) {
            $dbUser = $dbUsers->get($att['user_id']);

            if ($dbUser) {
                $userObj = (object) [
                    'id'    => $dbUser->id,
                    'name'  => $dbUser->name,
                    'email' => $dbUser->email,
                    'uid'   => $dbUser->uid,
                    'role'  => $dbUser->role,
                    'kelas' => $dbUser->kelas,
                ];
            } else {
                $userObj = (object) [
                    'id'    => null,
                    'name'  => 'User Terhapus',
                    'email' => '-',
                    'uid'   => '-',
                    'role'  => 'user',
                    'kelas' => '-',
                ];
            }

            return (object) [
                'id'      => $att['id'],
                'user_id' => $att['user_id'],
                'date'    => $att['date'],
                'time_in' => $att['time_in'],
                'status'  => $att['status'],
                'user'    => $userObj,
            ];
        });
    }

    public function index(Request $request)
    {
        $search = $request->query('search');
        $attendances = $this->getMappedAttendances();

        if ($search) {
            $attendances = $attendances->filter(function ($att) use ($search) {
                $searchLower = strtolower($search);
                $nameMatch = $att->user && str_contains(strtolower($att->user->name), $searchLower);
                $uidMatch = $att->user && str_contains(strtolower($att->user->uid), $searchLower);
                return $nameMatch || $uidMatch;
            });
        }

        $attendances = $attendances->sortBy(function ($att) {
            return strtolower($att->user->name ?? '');
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
                'path'  => LengthAwarePaginator::resolveCurrentPath(),
                'query' => $request->query()
            ]
        );

        $availableClasses = \App\Models\User::whereNotNull('kelas')
            ->where('kelas', '!=', '')
            ->distinct()
            ->pluck('kelas');

        return view('pages.attendance.index', [
            'attendances' => $paginator,
            'availableClasses' => $availableClasses
        ]);
    }

    public function scanRfid(Request $request)
    {
        $request->validate([
            'uid' => 'required|string',
        ]);

        $users = JsonDatabase::getUsers();
        $user = $users->first(function ($u) use ($request) {
            return strcasecmp($u['uid'], $request->uid) === 0;
        });

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Kartu RFID tidak dikenali atau belum terdaftar.',
            ], 404);
        }

        $rfidStatus = $user['rfid_status'] ?? 'active';
        if ($rfidStatus !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Kartu RFID Anda dinonaktifkan. Hubungi Admin untuk mengaktifkannya.',
            ], 403);
        }

        $today = Carbon::today()->toDateString();
        $attendances = JsonDatabase::getAttendances();
        $alreadyTapped = $attendances->contains(function ($att) use ($user, $today) {
            return $att['user_id'] == $user['id'] && $att['date'] === $today;
        });

        if ($alreadyTapped) {
            return response()->json([
                'success' => false,
                'message' => 'Halo ' . $user['name'] . ', Anda sudah melakukan absensi hari ini.',
            ], 400);
        }

        $currentTime = Carbon::now()->toTimeString();
        $newId = $attendances->max('id') ? $attendances->max('id') + 1 : 1;
        $newAttendance = [
            'id'      => $newId,
            'user_id' => $user['id'],
            'date'    => $today,
            'time_in' => $currentTime, 
            'status'  => 'Hadir',
        ];

        $attendances->push($newAttendance);
        JsonDatabase::saveAttendances($attendances);

        \App\Models\Attendance::updateOrCreate(
            ['id' => $newId],
            [
                'user_id' => $user['id'],
                'date'    => $today,
                'time_in' => $currentTime,
                'status'  => 'Hadir',
            ]
        );

        $this->saveToJson($user['name'], $request->uid, $today, $currentTime);

        return response()->json([
            'success' => true,
            'message' => 'Absen masuk berhasil dicatat!',
            'data'    => [
                'nama'    => $user['name'],
                'tanggal' => $newAttendance['date'],
                'jam'     => $newAttendance['time_in'],
                'status'  => $newAttendance['status'],
            ],
        ], 200); 
    }

    private function saveToJson($name, $uid, $date, $time)
    {
        $fileName = 'absensi_log.json';
        $newData = [
            'nama'      => $name,
            'uid'       => $uid,
            'tanggal'   => $date,
            'jam_masuk' => $time
        ];

        if (Storage::disk('local')->exists($fileName)) {
            $oldContent = Storage::disk('local')->get($fileName);
            $arrayData = json_decode($oldContent, true) ?? [];
        } else {
            $arrayData = [];
        }

        $arrayData[] = $newData;
        Storage::disk('local')->put($fileName, json_encode($arrayData, JSON_PRETTY_PRINT));
    }

    public function autocomplete(Request $request)
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

    public function show($id)
    {
        $attendance = $this->getMappedAttendances()->firstWhere('id', $id);

        if (!$attendance) {
            abort(404);
        }

        return view('pages.attendance.show', compact('attendance'));
    }

    public function destroy($id)
    {
        \App\Models\Attendance::where('id', $id)->delete();

        $attendances = JsonDatabase::getAttendances();
        $attendances = $attendances->reject(function ($att) use ($id) {
            return $att['id'] == $id;
        });
        JsonDatabase::saveAttendances($attendances);

        return redirect()->route('attendance.index')->with('success', 'Data absensi berhasil dihapus.');
    }
}