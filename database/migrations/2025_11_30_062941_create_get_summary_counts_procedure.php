<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared("
            DROP PROCEDURE IF EXISTS get_summary_counts;
        ");

        DB::unprepared("
            CREATE PROCEDURE get_summary_counts()
            BEGIN
                SELECT 'students' AS type, COUNT(*) AS total FROM students
                UNION ALL
                SELECT 'teachers', COUNT(*) FROM users WHERE role = 'teacher'
                UNION ALL
                SELECT 'employees', COUNT(*) FROM users
                UNION ALL
                SELECT 'classes', COUNT(*) FROM classes;
            END
        ");

    }

    public function down(): void
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS get_summary_counts;");
    }
};
