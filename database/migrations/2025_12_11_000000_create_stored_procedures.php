<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ==========================================
        // 1. PROCEDURE: Dashboard Stats
        // ==========================================
        // Menghitung total siswa, guru, dan kelas untuk Card Dashboard
        DB::unprepared('
            DROP PROCEDURE IF EXISTS p_GetDashboardStats;
            CREATE PROCEDURE p_GetDashboardStats(
                OUT total_students INT,
                OUT total_teachers INT,
                OUT total_classes INT
            )
            BEGIN
                SELECT COUNT(*) INTO total_students FROM students WHERE is_active = 1 AND deleted_at IS NULL;
                SELECT COUNT(*) INTO total_teachers FROM users WHERE is_teacher = 1 AND is_active = 1 AND deleted_at IS NULL;
                SELECT COUNT(*) INTO total_classes FROM classes WHERE is_active = 1 AND deleted_at IS NULL;
            END
        ');

        // ==========================================
        // 2. PROCEDURE: Attendance Stats (Global/Class)
        // ==========================================
        // Menghitung persentase kehadiran berdasarkan tanggal tertentu
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
            END
        ");

        // ==========================================
        // 3. PROCEDURE: Student Attendance Summary
        // ==========================================
        // Menghitung rekap detail kehadiran spesifik per siswa (Untuk Halaman Detail Siswa)
        DB::unprepared("
            DROP PROCEDURE IF EXISTS p_get_attendance_summary;
            CREATE PROCEDURE p_get_attendance_summary (IN studentId INT)
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
        ");
    }

    public function down(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS p_GetDashboardStats');
        DB::unprepared('DROP PROCEDURE IF EXISTS p_GetAttendanceStats');
        DB::unprepared('DROP PROCEDURE IF EXISTS p_get_attendance_summary');
    }
};