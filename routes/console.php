<?php

use Illuminate\Support\Facades\Schedule;

// Menjalankan backup otomatis setiap 3 bulan sekali (Quarterly)
// Jadwal: 1 Januari, 1 April, 1 Juli, 1 Oktober jam 00:00
Schedule::command('db:backup')->quarterly();

// Jalankan pembersihan log setiap hari pada jam 01:00 pagi
Schedule::command('logs:cleanup')->dailyAt('01:00');