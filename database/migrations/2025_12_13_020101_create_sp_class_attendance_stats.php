<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Definisikan SQL untuk membuat SP
        $sql = "
            DROP PROCEDURE IF EXISTS sp_get_class_attendance_stats;
            CREATE PROCEDURE sp_get_class_attendance_stats (IN classId INT)
            BEGIN
                SELECT 
                    s.id AS student_id,
                    s.name,
                    s.student_number,
                    COUNT(ar.id) AS total_sessions_recorded,
                    SUM(CASE WHEN ar.status IN ('present', 'late') THEN 1 ELSE 0 END) AS total_present,
                    -- Persentase: Total Hadir / Total Tercatat (kali 100)
                    ROUND(
                        (SUM(CASE WHEN ar.status IN ('present', 'late') THEN 1 ELSE 0 END) / COUNT(ar.id)) * 100
                    ) AS percentage
                FROM students s
                LEFT JOIN attendance_records ar ON s.id = ar.student_id
                WHERE s.class_id = classId
                GROUP BY s.id, s.name, s.student_number
                ORDER BY percentage ASC;
            END
        ";

        // 2. Eksekusi SQL. Untuk SP multi-statement di MySQL, kita harus ubah delimiter.
        // Karena ini adalah migration, kita bisa menjalankan DROP dan CREATE secara berurutan.
        
        DB::unprepared($sql);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // SQL untuk menghapus Stored Procedure
        DB::statement("DROP PROCEDURE IF EXISTS sp_get_class_attendance_stats");
    }
};