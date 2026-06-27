<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name'              => 'Admin',
            'email'             => 'admin@gmail.com',
            'password'          => Hash::make('admin123'),
            'uid'               => "Admin",
            'role'              => 'admin',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name'              => 'Ramdhani',
            'email'             => 'imdhan26@gmail.com',
            'password'          => Hash::make('Ramdhani26!'),
            'uid'               => '9380DE34',
            'role'              => 'user',
            'email_verified_at' => now(),
        ]);
        User::create([
            'name'              => 'Ridwan Saepuloh',
            'email'             => 'ridwansaepuloh2008@gmail.com',
            'password'          => Hash::make('154369!'),
            'uid'               => 'A32F0D35',
            'role'              => 'user',
            'email_verified_at' => now(),
        ]);
    }
}