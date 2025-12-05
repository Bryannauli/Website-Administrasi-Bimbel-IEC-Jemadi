<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
        {
            DB::unprepared("
                DROP PROCEDURE IF EXISTS get_student_summary;
            ");

            DB::unprepared("
                CREATE PROCEDURE get_student_summary()
                BEGIN
                    SELECT 
                        COUNT(*) AS total_students,
                        SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) AS total_active,
                        SUM(CASE WHEN is_active = 0 THEN 1 ELSE 0 END) AS total_inactive
                    FROM students;
                END
            ");
        }

    public function down(): void
        {
            DB::unprepared("DROP PROCEDURE IF EXISTS get_student_summary");
        }

};
