<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Membuat Stored Procedure
        $sql = "
            CREATE PROCEDURE sp_get_attendance_summary (IN studentId INT)
            BEGIN
                SELECT
                    COUNT(*) AS total_days,
                    SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) AS present,
                    SUM(CASE WHEN status = 'absent' THEN 1 ELSE 0 END) AS absent,
                    SUM(CASE WHEN status = 'late' THEN 1 ELSE 0 END) AS late,
                    SUM(CASE WHEN status = 'permission' THEN 1 ELSE 0 END) AS permission,
                    SUM(CASE WHEN status = 'sick' THEN 1 ELSE 0 END) AS sick,
                    (SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) / COUNT(*)) * 100 AS present_percent
                FROM attendance_records
                WHERE student_id = studentId;
            END
        ";
        DB::statement($sql);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Menghapus Stored Procedure
        DB::statement("DROP PROCEDURE IF EXISTS sp_get_attendance_summary");
    }
};