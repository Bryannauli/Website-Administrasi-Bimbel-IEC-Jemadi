<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // FUNCTION: f_GetGrade (Placeholder / Kosong)
        // Dosen minta wajib ada fungsi, kita buat kerangkanya dulu.
        DB::unprepared('
            DROP FUNCTION IF EXISTS f_GetGrade;
            CREATE FUNCTION f_GetGrade(score INT) RETURNS VARCHAR(2)
            DETERMINISTIC
            BEGIN
                -- Nanti logika nilai (A/B/C) ditaruh sini
                RETURN "-"; 
            END
        ');
    }

    public function down(): void
    {
        DB::unprepared('DROP FUNCTION IF EXISTS f_GetGrade');
    }
};