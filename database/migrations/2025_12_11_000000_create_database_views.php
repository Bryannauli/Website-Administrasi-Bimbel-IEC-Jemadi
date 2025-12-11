<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared("
            CREATE OR REPLACE VIEW v_weekly_absence AS
            SELECT 
                DATE(attendance_sessions.date) as date,
                COUNT(attendance_records.id) as total_absence
            FROM attendance_records
            JOIN attendance_sessions ON attendance_records.attendance_session_id = attendance_sessions.id
            WHERE attendance_records.status IN ('absent', 'sick', 'permission')
            -- Hapus pengecekan deleted_at karena tabel ini tidak pakai soft delete
            GROUP BY DATE(attendance_sessions.date);
        ");
    }

    public function down(): void
    {
        DB::unprepared("DROP VIEW IF EXISTS v_weekly_absence");
    }
};