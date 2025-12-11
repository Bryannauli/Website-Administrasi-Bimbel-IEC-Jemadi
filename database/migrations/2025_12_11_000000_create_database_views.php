<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Membuat atau mengganti kedua views (v_weekly_absence & v_attendance_summary).
     */
    public function up(): void
    {
        // 1. View: v_weekly_absence (Logika yang sudah ada, menggunakan DB::unprepared)
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

        // 2. View: v_attendance_summary (Logika tambahan, nama diubah dan menggunakan DB::unprepared)
        DB::unprepared("
            CREATE VIEW v_attendance_summary AS
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
    }

    /**
     * Reverse the migrations (Menghapus kedua Views jika dilakukan rollback).
     */
    public function down(): void
    {
        // Hapus v_attendance_summary (Nama yang sudah diubah)
        DB::unprepared("DROP VIEW IF EXISTS v_attendance_summary"); 
        
        // Hapus v_weekly_absence
        DB::unprepared("DROP VIEW IF EXISTS v_weekly_absence");
    }
};