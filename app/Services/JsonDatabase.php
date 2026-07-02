<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Collection;

class JsonDatabase
{
    protected static $usersFile = 'users.json';
    protected static $attendancesFile = 'attendances.json';

    /**
     * Get all users from JSON.
     * Initializes the file with default data if it doesn't exist.
     */
    public static function getUsers(): Collection
    {
        if (!Storage::disk('local')->exists(self::$usersFile)) {
            self::seedUsers();
        }

        $json = Storage::disk('local')->get(self::$usersFile);
        $data = json_decode($json, true) ?? [];

        return collect($data);
    }

    /**
     * Save users collection to JSON.
     */
    public static function saveUsers(Collection $users): void
    {
        Storage::disk('local')->put(
            self::$usersFile,
            json_encode($users->values()->all(), JSON_PRETTY_PRINT)
        );
    }

    /**
     * Get all attendances from JSON.
     * Initializes the file with default data if it doesn't exist.
     */
    public static function getAttendances(): Collection
    {
        if (!Storage::disk('local')->exists(self::$attendancesFile)) {
            self::seedAttendances();
        }

        $json = Storage::disk('local')->get(self::$attendancesFile);
        $data = json_decode($json, true) ?? [];

        return collect($data);
    }

    /**
     * Save attendances collection to JSON.
     */
    public static function saveAttendances(Collection $attendances): void
    {
        Storage::disk('local')->put(
            self::$attendancesFile,
            json_encode($attendances->values()->all(), JSON_PRETTY_PRINT)
        );
    }

    /**
     * Seed initial users into users.json.
     */
    /**
     * Seed initial users into users.json.
     */
    protected static function seedUsers(): void
    {
        // Ambil data user dari database SQLite jika ada
        $dbUsers = \App\Models\User::all();
        $defaultUsers = [];

        foreach ($dbUsers as $dbUser) {
            $defaultUsers[] = [
                'id'          => $dbUser->id,
                'name'        => $dbUser->name,
                'email'       => $dbUser->email,
                'password'    => $dbUser->password, // Pertahankan password hash
                'uid'         => $dbUser->uid,
                'role'        => $dbUser->role,
                'kelas'       => $dbUser->kelas,
                'no_hp'       => $dbUser->no_hp,
                'rfid_status' => $dbUser->rfid_status ?? 'active',
            ];
        }

        // Jika database kosong, buat user default Admin saja
        if (empty($defaultUsers)) {
            $adminUser = [
                'id'          => 1,
                'name'        => 'Admin',
                'email'       => 'admin@gmail.com',
                'password'    => Hash::make('admin123'),
                'uid'         => 'Admin',
                'role'        => 'admin',
                'kelas'       => null,
                'no_hp'       => null,
                'rfid_status' => 'active',
            ];
            
            $defaultUsers[] = $adminUser;

            // Sinkronisasi data admin ke SQLite database
            \App\Models\User::updateOrCreate(
                ['email' => $adminUser['email']],
                [
                    'id'          => $adminUser['id'],
                    'name'        => $adminUser['name'],
                    'password'    => $adminUser['password'],
                    'uid'         => $adminUser['uid'],
                    'role'        => $adminUser['role'],
                    'kelas'       => $adminUser['kelas'],
                    'no_hp'       => $adminUser['no_hp'],
                    'rfid_status' => $adminUser['rfid_status'],
                ]
            );
        }

        Storage::disk('local')->put(
            self::$usersFile,
            json_encode($defaultUsers, JSON_PRETTY_PRINT)
        );
    }

    /**
     * Seed initial attendances into attendances.json.
     */
    protected static function seedAttendances(): void
    {
        $attendances = [];

        // Ambil data absensi yang sudah ada di database SQLite
        $dbAttendances = \App\Models\Attendance::all();
        foreach ($dbAttendances as $dbAtt) {
            $attendances[] = [
                'id'      => $dbAtt->id,
                'user_id' => $dbAtt->user_id,
                'date'    => $dbAtt->date,
                'time_in' => $dbAtt->time_in,
                'status'  => $dbAtt->status,
            ];
        }

        Storage::disk('local')->put(
            self::$attendancesFile,
            json_encode($attendances, JSON_PRETTY_PRINT)
        );
    }

    /**
     * Synchronize all users from SQLite database to users.json.
     */
    public static function syncFromDatabase(): void
    {
        $users = \App\Models\User::all()->map(function($user) {
            return [
                'id'          => $user->id,
                'name'        => $user->name,
                'email'       => $user->email,
                'password'    => $user->password,
                'uid'         => $user->uid,
                'role'        => $user->role,
                'kelas'       => $user->kelas,
                'no_hp'       => $user->no_hp,
                'rfid_status' => $user->rfid_status ?? 'active',
            ];
        })->values();

        Storage::disk('local')->put(
            self::$usersFile,
            json_encode($users, JSON_PRETTY_PRINT)
        );
    }

    /**
     * Synchronize all attendances from SQLite database to attendances.json.
     */
    public static function syncAttendancesFromDatabase(): void
    {
        $attendances = \App\Models\Attendance::all()->map(function($att) {
            return [
                'id'      => $att->id,
                'user_id' => $att->user_id,
                'date'    => $att->date,
                'time_in' => $att->time_in,
                'status'  => $att->status,
            ];
        })->values();

        Storage::disk('local')->put(
            self::$attendancesFile,
            json_encode($attendances, JSON_PRETTY_PRINT)
        );
    }
}
