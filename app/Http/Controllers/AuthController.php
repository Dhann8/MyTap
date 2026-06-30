<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\JsonDatabase;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
   
    public function dashboard()
    {
        $allUsers = JsonDatabase::getUsers()->where('role', 'user')->whereNotNull('uid');
        $totalSiswa = $allUsers->count();
        
        $rfidAktif     = $allUsers->where('rfid_status', 'active')->count();
        $rfidTidakAktif = $allUsers->where('rfid_status', '!=', 'active')->count();

        $todayStr = Carbon::today()->toDateString();
        $attendancesToday = JsonDatabase::getAttendances()->where('date', $todayStr);
        $hadirHariIni = $attendancesToday->where('status', 'Hadir')->count();
        $tidakHadir = $totalSiswa - $hadirHariIni;
        $persenKehadiran = $totalSiswa > 0 ? round(($hadirHariIni / $totalSiswa) * 100) : 0;

        $startDate = now()->subDays(29)->toDateString();
        $dates = [];
        for ($i = 29; $i >= 0; $i--) {
            $dates[] = now()->subDays($i)->toDateString();
        }

        $users = JsonDatabase::getUsers()->where('role', 'user');
        $attendances = JsonDatabase::getAttendances()->where('date', '>=', $startDate);
        $classes = $users->pluck('kelas')->filter()->unique()->values()->all();

        $classDailyData = [];
        foreach ($classes as $cls) {
            $classUsers = $users->where('kelas', $cls)->pluck('id')->all();
            
            $dailyCounts = [];
            foreach ($dates as $d) {
                $count = $attendances->where('date', $d)
                    ->whereIn('user_id', $classUsers)
                    ->where('status', 'Hadir')
                    ->count();
                $dailyCounts[] = $count;
            }
            $classDailyData[$cls] = $dailyCounts;
        }
        $studentMonthlyData = [];
        foreach ($classes as $cls) {
            $classUsers = $users->where('kelas', $cls);
            
            $studentsList = [];
            foreach ($classUsers as $usr) {
                $userAtts = $attendances->where('user_id', $usr['id']);
                
                $hadir = $userAtts->where('status', 'Hadir')->count();
                $sakit = $userAtts->where('status', 'Sakit')->count();
                $izin = $userAtts->where('status', 'Izin')->count();
                $alpa = $userAtts->where('status', 'Alpa')->count();
                
                $studentsList[] = [
                    'name'  => $usr['name'],
                    'hadir' => $hadir,
                    'sakit' => $sakit,
                    'izin'  => $izin,
                    'alpa'  => $alpa,
                ];
            }
            $studentMonthlyData[$cls] = $studentsList;
        }

        return view('pages.dashboard', compact(
            'totalSiswa', 
            'hadirHariIni', 
            'tidakHadir', 
            'persenKehadiran',
            'rfidAktif',
            'rfidTidakAktif',
            'dates',
            'classes',
            'classDailyData',
            'studentMonthlyData'
        ));
    }

    public function showLogin()
    {
        return view('pages.auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);
        $jsonUser = JsonDatabase::getUsers()->firstWhere('email', $request->email);

        if ($jsonUser && Hash::check($request->password, $jsonUser['password'])) {
            $dbUser = User::updateOrCreate(
                ['email' => $jsonUser['email']],
                [
                    'name'     => $jsonUser['name'],
                    'password' => $jsonUser['password'],
                    'uid'      => $jsonUser['uid'],
                    'role'     => $jsonUser['role'],
                ]
            );

            Auth::login($dbUser);
            $request->session()->regenerate();

            return redirect()->route('dashboard.index')
                             ->with('success', 'Selamat datang kembali!');
        }

        return back()->withErrors([
            'email' => 'Email atau password yang Anda masukkan salah.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil keluar.');
    }
}