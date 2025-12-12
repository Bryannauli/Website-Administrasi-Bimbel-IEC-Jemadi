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
        // ==========================================
        // 1. View: v_weekly_absence
        // ==========================================
        DB::unprepared("
            CREATE OR REPLACE VIEW v_weekly_absence AS
            SELECT 
                DATE(attendance_sessions.date) as date,
                COUNT(attendance_records.id) as total_absence
            FROM attendance_records
            JOIN attendance_sessions ON attendance_records.attendance_session_id = attendance_sessions.id
            WHERE attendance_records.status IN ('absent', 'sick', 'permission')
            GROUP BY DATE(attendance_sessions.date);
        ");

        // ==========================================
        // 2. View: v_attendance_summary
        // ==========================================
        DB::unprepared("
            CREATE OR REPLACE VIEW v_attendance_summary AS
            SELECT
                t2.date,
                SUM(CASE WHEN t1.status = 'present' THEN 1 ELSE 0 END) AS total_present,
                SUM(CASE WHEN t1.status = 'permission' THEN 1 ELSE 0 END) AS total_permission,
                SUM(CASE WHEN t1.status = 'sick' THEN 1 ELSE 0 END) AS total_sick,
                SUM(CASE WHEN t1.status = 'late' THEN 1 ELSE 0 END) AS total_late,
                SUM(CASE WHEN t1.status = 'absent' THEN 1 ELSE 0 END) AS total_absent,
                COUNT(t1.id) AS total_records
            FROM attendance_records t1
            JOIN attendance_sessions t2 ON t1.attendance_session_id = t2.id
            GROUP BY t2.date
        ");

        // ==========================================
        // 3. View: v_today_schedule
        // ==========================================
        DB::unprepared("
            CREATE OR REPLACE VIEW v_today_schedule AS
            SELECT
                s.id AS schedule_id,
                c.id AS class_id,
                c.name AS class_name,
                c.classroom,
                c.start_time,
                c.end_time,
                s.day_of_week,
                ft.name AS form_teacher_name,
                lt.name AS local_teacher_name
            FROM schedules s
            JOIN classes c ON s.class_id = c.id
            LEFT JOIN users ft ON c.form_teacher_id = ft.id
            LEFT JOIN users lt ON c.local_teacher_id = lt.id
            WHERE s.day_of_week = DAYNAME(NOW())
                AND c.is_active = TRUE
            ORDER BY c.start_time, c.name;
        ");

        // ==========================================
        // 4. View: v_student_attendance (FIXED)
        // ==========================================
        DB::unprepared("
            CREATE OR REPLACE VIEW v_student_attendance AS
            SELECT 
                ar.student_id,
                ar.status,
                ar.created_at,
                ar.updated_at,
                s.date AS session_date,
                s.class_id,  -- DITAMBAHKAN
                c.name AS session_name 
            FROM attendance_records ar
            JOIN attendance_sessions s ON ar.attendance_session_id = s.id
            JOIN classes c ON s.class_id = c.id; 
        ");

        // ==========================================
        // 5. View: v_teacher_attendance (FIXED)
        // ==========================================
        // Perbaikan: Join ke table classes (c) untuk ambil c.name sebagai session_name
        DB::unprepared("
            CREATE OR REPLACE VIEW v_teacher_attendance AS
            SELECT 
                tar.id AS record_id,
                tar.teacher_id,
                tar.status,
                tar.created_at,
                tar.attendance_session_id,
                s.date AS session_date,
                c.name AS session_name
            FROM teacher_attendance_records tar
            JOIN attendance_sessions s ON tar.attendance_session_id = s.id
            JOIN classes c ON s.class_id = c.id
            ORDER BY tar.id DESC;
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("DROP VIEW IF EXISTS v_teacher_attendance");
        DB::unprepared("DROP VIEW IF EXISTS v_student_attendance");
        DB::unprepared("DROP VIEW IF EXISTS v_today_schedule");
        DB::unprepared("DROP VIEW IF EXISTS v_attendance_summary"); 
        DB::unprepared("DROP VIEW IF EXISTS v_weekly_absence");
    }
};