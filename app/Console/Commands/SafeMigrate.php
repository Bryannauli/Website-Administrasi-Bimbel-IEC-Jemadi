<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

class SafeMigrate extends Command
{
    protected $signature = 'migrate:setup {--force}';
    protected $description = 'Run database migrations with prompt to create database if missing';

    public function handle()
    {
        $db = Config::get('database.connections.mysql.database');

        try {
            DB::connection()->getPdo();
        } catch (\Throwable $e) {

            $this->warn("Database '$db' tidak ditemukan.");

            if (! $this->confirm("Buat database '$db' sekarang?")) {
                $this->info("Migrasi dibatalkan.");
                return Command::FAILURE;
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

        $this->call('migrate', ['--force' => $this->option('force')]);
        return Command::SUCCESS;
    }
}
