<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
        public function up(): void
    {
        DB::unprepared("
            DROP PROCEDURE IF EXISTS get_weekly_absence;
        ");

        DB::unprepared("
            CREATE PROCEDURE get_weekly_absence()
            BEGIN
                WITH week_dates AS (
                    SELECT 
                        DATE_SUB(CURDATE(), INTERVAL (WEEKDAY(CURDATE())) DAY) AS mon,
                        DATE_SUB(CURDATE(), INTERVAL (WEEKDAY(CURDATE())-1) DAY) AS tue,
                        DATE_SUB(CURDATE(), INTERVAL (WEEKDAY(CURDATE())-2) DAY) AS wed,
                        DATE_SUB(CURDATE(), INTERVAL (WEEKDAY(CURDATE())-3) DAY) AS thu,
                        DATE_SUB(CURDATE(), INTERVAL (WEEKDAY(CURDATE())-4) DAY) AS fri,
                        DATE_SUB(CURDATE(), INTERVAL (WEEKDAY(CURDATE())-5) DAY) AS sat
                )

                SELECT 'Mon' AS day_label, COUNT(ar.id) AS total
                FROM week_dates wd
                LEFT JOIN attendance_sessions s ON s.date = wd.mon
                LEFT JOIN attendance_records ar ON ar.attendance_session_id = s.id AND ar.status = 'absent'

                UNION ALL
                SELECT 'Tue', COUNT(ar.id)
                FROM week_dates wd
                LEFT JOIN attendance_sessions s ON s.date = wd.tue
                LEFT JOIN attendance_records ar ON ar.attendance_session_id = s.id AND ar.status = 'absent'

                UNION ALL
                SELECT 'Wed', COUNT(ar.id)
                FROM week_dates wd
                LEFT JOIN attendance_sessions s ON s.date = wd.wed
                LEFT JOIN attendance_records ar ON ar.attendance_session_id = s.id AND ar.status = 'absent'

                UNION ALL
                SELECT 'Thu', COUNT(ar.id)
                FROM week_dates wd
                LEFT JOIN attendance_sessions s ON s.date = wd.thu
                LEFT JOIN attendance_records ar ON ar.attendance_session_id = s.id AND ar.status = 'absent'

                UNION ALL
                SELECT 'Fri', COUNT(ar.id)
                FROM week_dates wd
                LEFT JOIN attendance_sessions s ON s.date = wd.fri
                LEFT JOIN attendance_records ar ON ar.attendance_session_id = s.id AND ar.status = 'absent'

                UNION ALL
                SELECT 'Sat', COUNT(ar.id)
                FROM week_dates wd
                LEFT JOIN attendance_sessions s ON s.date = wd.sat
                LEFT JOIN attendance_records ar ON ar.attendance_session_id = s.id AND ar.status = 'absent';
            END
        ");
    }

    public function down(): void
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS get_weekly_absence;");
    }

};
