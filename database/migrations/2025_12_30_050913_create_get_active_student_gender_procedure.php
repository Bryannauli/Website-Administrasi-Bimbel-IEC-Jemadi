<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Hapus dulu jika sudah ada
        DB::unprepared("DROP PROCEDURE IF EXISTS get_active_student_gender;");

        // Buat prosedur
        DB::unprepared("
            CREATE PROCEDURE get_active_student_gender()
            BEGIN
                SELECT gender, COUNT(*) AS total
                FROM students
                WHERE is_active = 1
                GROUP BY gender;
            END
        ");
    }

    public function down(): void
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS get_active_student_gender;");
    }
};
