<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

class SafeMigrate extends Command
{
    protected $signature = 'migrate:setup {--force} {--seed}';
    protected $description = 'Run database migrations (and optionally seeds) with prompt to create database if missing';

    public function handle()
    {
        $db = Config::get('database.connections.mysql.database');
        $force = $this->option('force');

        try {
            DB::connection()->getPdo();
        } catch (\Throwable $e) {

            $this->warn("Database '$db' tidak ditemukan.");

            if (! $force && ! $this->confirm("Buat database '$db' sekarang?")) {
                $this->info("Migrasi dibatalkan.");
                return Command::FAILURE;
            }
            
            if ($force) {
                $this->info("Membuat database '$db' (dipaksa oleh --force)...");
            }

            // STEP PENTING: Set database menjadi null dulu agar connect ke server MySQL berhasil
            $connection = Config::get('database.connections.mysql');
            $connection['database'] = null;
            Config::set('database.connections.mysql', $connection);

            DB::purge('mysql'); // reset koneksi lama
            DB::reconnect('mysql'); // pakai setting database=null

            DB::statement("CREATE DATABASE `$db`");
            $this->info("✅ Database '$db' berhasil dibuat.");

            // Kembalikan setting database
            $connection['database'] = $db;
            Config::set('database.connections.mysql', $connection);
            DB::purge('mysql');
        }

        // Jalankan migrasi
        $this->info("Menjalankan migrasi...");
        $this->call('migrate', ['--force' => $force]);

        // Cek apakah opsi --seed digunakan
        if ($this->option('seed')) {
            $this->info("Menjalankan seeder...");
            $this->call('db:seed', ['--force' => $force]);
        }

        $this->info("✅ Setup database selesai.");
        return Command::SUCCESS;
    }
}