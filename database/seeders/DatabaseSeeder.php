<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,       // Mengisi data akun Admin/User terlebih dahulu
            AttendanceSeeder::class, // Baru mengisi data absensinya
        ]);
    }
}