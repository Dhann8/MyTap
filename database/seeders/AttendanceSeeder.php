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
        // Ambil semua user dengan role 'user' (siswa)
        $students = User::where('role', 'user')->get();

        // Pastikan jumlah siswa di database minimal ada 3 orang
        if ($students->count() >= 2) {
            
            // Data Absen 1: Siswa pertama (Agum Renggi) -> Hadir Hari Ini
            Attendance::create([
                'user_id' => $students[0]->id,
                'date'    => Carbon::today()->toDateString(),
                'time_in' => '07:05:00',
                'status'  => 'Hadir',
            ]);

            // Data Absen 2: Siswa kedua (Geisika Yoan Dinata) -> Sakit Kemarin
            Attendance::create([
                'user_id' => $students[1]->id,
                'date'    => Carbon::yesterday()->toDateString(),
                'time_in' => '00:00:00',
                'status'  => 'Sakit',
            ]);
        }
    }
}