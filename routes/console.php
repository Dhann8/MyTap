<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Jadwalkan pengecekan siswa terlambat setiap menit pada hari sekolah
Schedule::command('attendance:check-late')->everyMinute()->weekdays();
