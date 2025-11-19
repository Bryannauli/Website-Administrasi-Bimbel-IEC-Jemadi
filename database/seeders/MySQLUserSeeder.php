<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MySQLUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement("
            CREATE USER IF NOT EXISTS 'iec_user'@'%' IDENTIFIED BY 'iec12345';
        ");

        DB::statement("
            GRANT SELECT, INSERT, UPDATE, DELETE ON iec_jemadi.* TO 'iec_user'@'%';
        ");

        DB::statement("
            FLUSH PRIVILEGES;
        ");
    }
}
