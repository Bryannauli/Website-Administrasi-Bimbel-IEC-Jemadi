<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ActivityLog; //
use Carbon\Carbon;

class CleanupActivityLogs extends Command
{
    // Nama perintah
    protected $signature = 'logs:cleanup';
    protected $description = 'Menghapus log aktivitas yang sudah lebih dari 6 bulan';

    public function handle()
    {
        // 1. Tentukan batas waktu (Retention Policy)
        // Kita hapus log yang usianya > 6 bulan dari sekarang
        $retentionDate = Carbon::now()->subMonths(6);

        // 2. Jalankan query penghapusan
        $deletedCount = ActivityLog::where('created_at', '<', $retentionDate)->delete();

        if ($deletedCount > 0) {
            $this->info("Berhasil menghapus {$deletedCount} log lama.");
        } else {
            $this->info("Tidak ada log lama yang perlu dihapus.");
        }
    }
}