<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil satu user/siswa yang memiliki UID RFID untuk dicontohkan absensinya
        // Jika belum ada siswa, seeder ini akan mengambil ID user pertama (ID: 1)
        $user = User::whereNotNull('uid')->first() ?? User::first();

        if ($user) {
            // Data Absen 1: Hadir Hari Ini
            Attendance::create([
                'user_id' => $user->id,
                'date'    => Carbon::today()->toDateString(),
                'time_in' => '07:15:00',
                'status'  => 'Hadir',
            ]);

            // Data Absen 2: Sakit Kemarin
            Attendance::create([
                'user_id' => $user->id,
                'date'    => Carbon::yesterday()->toDateString(),
                'time_in' => '00:00:00', // Biasanya kosong/nol jika tidak hadir
                'status'  => 'Sakit',
            ]);

            // Data Absen 3: Izin 2 Hari yang Lalu
            Attendance::create([
                'user_id' => $user->id,
                'date'    => Carbon::today()->subDays(2)->toDateString(),
                'time_in' => '00:00:00',
                'status'  => 'Izin',
            ]);
        }
    }
}