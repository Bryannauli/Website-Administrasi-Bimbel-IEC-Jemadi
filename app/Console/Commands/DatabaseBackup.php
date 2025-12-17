<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class DatabaseBackup extends Command
{
    /**
     * Nama perintah yang dipanggil di terminal: php artisan db:backup
     */
    protected $signature = 'db:backup';

    /**
     * Deskripsi perintah
     */
    protected $description = 'Mencadangkan database ke folder storage/app/backups';

    public function handle()
    {
        // 1. Persiapkan Nama File dan Folder
        $date = Carbon::now()->format('Y-m-d_H-i-s');
        $filename = "backup-iec-jemadi-{$date}.sql";
        $storagePath = storage_path("app/backups");

        // Pastikan folder backups ada
        if (!file_exists($storagePath)) {
            mkdir($storagePath, 0755, true);
        }

        $filePath = $storagePath . DIRECTORY_SEPARATOR . $filename;

        // 2. Ambil Kredensial dari .env (Gunakan Root atau Admin)
        $database = env('DB_DATABASE');
        $username = env('DB_USERNAME'); 
        $password = env('DB_PASSWORD');
        $host     = env('DB_HOST');

        // 3. Susun Perintah mysqldump
        // --routines & --triggers digunakan agar SP, Function, dan Trigger ikut dicadangkan
        $command = sprintf(
            'mysqldump --user=%s %s --host=%s --routines --triggers %s > %s',
            escapeshellarg($username),
            $password ? "--password=" . escapeshellarg($password) : "",
            escapeshellarg($host),
            escapeshellarg($database),
            escapeshellarg($filePath)
        );

        // 4. Eksekusi Perintah Sistem
        $output = [];
        $resultCode = 0;
        exec($command, $output, $resultCode);

        // 5. Output Hasil
        if ($resultCode === 0) {
            $message = "Database backup created successfully: {$filename}";
            $this->info($message);
            Log::info($message); // Catat ke system log Laravel
            return Command::SUCCESS;
        } else {
            $error = "Backup failed! Ensure 'mysqldump' is installed and credentials are correct.";
            $this->error($error);
            Log::error($error);
            return Command::FAILURE;
        }
    }
}