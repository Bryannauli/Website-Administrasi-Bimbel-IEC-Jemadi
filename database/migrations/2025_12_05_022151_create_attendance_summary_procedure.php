<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared("
            DROP PROCEDURE IF EXISTS get_attendance_summary;
        ");

        DB::unprepared("
            CREATE PROCEDURE get_attendance_summary(IN date_filter DATE)
            BEGIN
                SELECT
                    ROUND(SUM(ar.status = 'present') / COUNT(*) * 100, 0) AS present,
                    ROUND(SUM(ar.status = 'permission') / COUNT(*) * 100, 0) AS permission,
                    ROUND(SUM(ar.status = 'sick') / COUNT(*) * 100, 0) AS sick,
                    ROUND(SUM(ar.status = 'late') / COUNT(*) * 100, 0) AS late,
                    ROUND(SUM(ar.status = 'absent') / COUNT(*) * 100, 0) AS absent
                FROM attendance_records ar
                INNER JOIN attendance_sessions s ON ar.attendance_session_id = s.id
                WHERE date_filter IS NULL OR s.date = date_filter;
            END
        ");
    }

    public function down(): void
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS get_attendance_summary;");
    }
};
