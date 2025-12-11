<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. PROCEDURE UTAMA: Dashboard Stats
        DB::unprepared('
            DROP PROCEDURE IF EXISTS p_GetDashboardStats;
            CREATE PROCEDURE p_GetDashboardStats(
                OUT total_students INT,
                OUT total_teachers INT,
                OUT total_classes INT,
                OUT total_boys INT,
                OUT total_girls INT
            )
            BEGIN
                SELECT COUNT(*) INTO total_students FROM students WHERE is_active = 1 AND deleted_at IS NULL;
                SELECT COUNT(*) INTO total_teachers FROM users WHERE is_teacher = 1 AND is_active = 1 AND deleted_at IS NULL;
                SELECT COUNT(*) INTO total_classes FROM classes WHERE is_active = 1 AND deleted_at IS NULL;
                SELECT COUNT(*) INTO total_boys FROM students WHERE gender = "male" AND is_active = 1 AND deleted_at IS NULL;
                SELECT COUNT(*) INTO total_girls FROM students WHERE gender = "female" AND is_active = 1 AND deleted_at IS NULL;
            END
        ');

        // 2. PROCEDURE TAMBAHAN: Attendance Stats
        DB::unprepared("
            DROP PROCEDURE IF EXISTS p_GetAttendanceStats;
            CREATE PROCEDURE p_GetAttendanceStats(IN date_filter DATE)
            BEGIN
                SELECT
                    IFNULL(ROUND(SUM(ar.status = 'present') / COUNT(*) * 100, 0), 0) AS present,
                    IFNULL(ROUND(SUM(ar.status = 'permission') / COUNT(*) * 100, 0), 0) AS permission,
                    IFNULL(ROUND(SUM(ar.status = 'sick') / COUNT(*) * 100, 0), 0) AS sick,
                    IFNULL(ROUND(SUM(ar.status = 'late') / COUNT(*) * 100, 0), 0) AS late,
                    IFNULL(ROUND(SUM(ar.status = 'absent') / COUNT(*) * 100, 0), 0) AS absent
                FROM attendance_records ar
                INNER JOIN attendance_sessions s ON ar.attendance_session_id = s.id
                WHERE (date_filter IS NULL OR s.date = date_filter);
                -- Baris 'AND ar.deleted_at IS NULL' SUDAH DIHAPUS
            END
        ");
    }

    public function down(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS p_GetDashboardStats');
        DB::unprepared('DROP PROCEDURE IF EXISTS p_GetAttendanceStats');
    }
};