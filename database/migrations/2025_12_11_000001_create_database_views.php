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
                DATE(class_sessions.date) as date,
                COUNT(attendance_records.id) as total_absence
            FROM attendance_records
            JOIN class_sessions ON attendance_records.class_session_id = class_sessions.id
            WHERE attendance_records.status IN ('absent', 'sick', 'permission')
            GROUP BY DATE(class_sessions.date);
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
            JOIN class_sessions t2 ON t1.class_session_id = t2.id
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
        // 4. View: v_student_attendance
        // ==========================================
        DB::unprepared("
            CREATE OR REPLACE VIEW v_student_attendance AS
            SELECT 
                ar.student_id,
                ar.status,
                ar.created_at,
                ar.updated_at,
                s.date AS session_date,
                s.class_id,
                c.name AS session_name 
            FROM attendance_records ar
            JOIN class_sessions s ON ar.class_session_id = s.id
            JOIN classes c ON s.class_id = c.id; 
        ");

        // ==========================================
        // 5. View: v_teacher_attendance
        // ==========================================
        DB::unprepared("
            CREATE OR REPLACE VIEW v_teacher_attendance AS
            SELECT 
                s.id AS session_id,
                s.teacher_id,
                s.date AS session_date,
                s.created_at,
                c.name AS class_name
            FROM class_sessions s
            JOIN classes c ON s.class_id = c.id
            WHERE s.teacher_id IS NOT NULL;
        ");

        // ==========================================
        // 6. View: v_student_grades [UPDATED]
        // ==========================================
        DB::unprepared("
            CREATE OR REPLACE VIEW v_student_grades AS
            SELECT
                af.id AS form_id,
                af.student_id,
                s.name AS student_name,
                s.student_number,
                
                -- Info Sesi
                af.assessment_session_id,
                asess.type AS assessment_type,
                asess.written_date AS assessment_date, -- [FIXED] asess.date diganti ke asess.written_date
                asess.class_id,
                c.name AS class_name,
                
                -- Nilai Written
                af.vocabulary,
                af.grammar,
                af.listening,
                af.reading,
                af.spelling,
                af.speaking, -- Total Speaking (Hasil tambah)
                
                -- Nilai Speaking Detail (Langsung dari Result ke Session)
                str.content_score AS speaking_content,
                str.participation_score AS speaking_participation,
                
                -- Info Speaking Header (Sekarang ada di assessment_sessions)
                asess.speaking_date,
                asess.speaking_topic,
                asess.interviewer_id,
                u.name AS interviewer_name,
                
                -- Kalkulasi Akhir
                f_CalcAssessmentAvg(
                    af.vocabulary, af.grammar, af.listening, af.reading, af.spelling, af.speaking
                ) AS final_score,
                f_GetGrade(
                    f_CalcAssessmentAvg(
                        af.vocabulary, af.grammar, af.listening, af.reading, af.spelling, af.speaking
                    )
                ) AS grade_text,
                af.updated_at

            FROM assessment_forms af
            JOIN students s ON af.student_id = s.id
            JOIN assessment_sessions asess ON af.assessment_session_id = asess.id
            JOIN classes c ON asess.class_id = c.id
            
            -- [UPDATE] Join langsung ke Speaking Result berdasarkan Session ID
            LEFT JOIN speaking_test_results str 
                ON asess.id = str.assessment_session_id 
                AND af.student_id = str.student_id
            
            -- [UPDATE] Join Interviewer langsung dari Session
            LEFT JOIN users u ON asess.interviewer_id = u.id
            
            ORDER BY s.student_number ASC;
        ");

        // ==========================================
        // 7. View: v_class_activity_logs
        // ==========================================
        DB::unprepared("
            CREATE OR REPLACE VIEW v_class_activity_logs AS
            SELECT 
                s.id AS session_id,
                s.class_id,             
                s.date,
                s.comment AS comment,
                u.name AS teacher_name,
                u.id AS teacher_id,
                COUNT(r.id) AS total_students,
                SUM(CASE WHEN r.status IN ('present', 'late') THEN 1 ELSE 0 END) AS present_count,
                CASE 
                    WHEN COUNT(r.id) > 0 THEN ROUND((SUM(CASE WHEN r.status IN ('present', 'late') THEN 1 ELSE 0 END) / COUNT(r.id)) * 100)
                    ELSE 0 
                END AS attendance_percentage
            FROM class_sessions s  
            LEFT JOIN users u ON s.teacher_id = u.id 
            LEFT JOIN attendance_records r ON s.id = r.class_session_id 
            GROUP BY s.id, s.class_id, s.date, s.comment, u.name, u.id;
        ");

        // ==========================================
        // 8. View: v_teacher_teaching_history
        // ==========================================
        DB::unprepared("
            CREATE OR REPLACE VIEW v_teacher_teaching_history AS
            SELECT 
                cs.id AS session_id,
                cs.teacher_id,
                u.name AS teacher_name,
                cs.class_id,
                c.name AS class_name,
                c.category,
                c.form_teacher_id,
                c.local_teacher_id, 
                cs.date,
                c.start_time,
                c.end_time,
                cs.created_at,
                c.deleted_at
            FROM class_sessions cs
            JOIN classes c ON cs.class_id = c.id
            JOIN users u ON cs.teacher_id = u.id
        ");

        // ==========================================
        // 9. View: v_unified_trash (BARU - UNTUK TRASH BIN)
        // ==========================================
        DB::unprepared("
            CREATE OR REPLACE VIEW v_unified_trash AS
            -- 1. Guru
            SELECT 
                id,
                name,
                'teacher' AS type,
                deleted_at
            FROM users 
            WHERE is_teacher = 1 AND deleted_at IS NOT NULL

            UNION ALL

            -- 2. Siswa
            SELECT 
                id,
                name,
                'student' AS type,
                deleted_at
            FROM students 
            WHERE deleted_at IS NOT NULL

            UNION ALL

            -- 3. Kelas
            SELECT 
                id,
                name,
                'class' AS type,
                deleted_at
            FROM classes 
            WHERE deleted_at IS NOT NULL
        ");

        // ==========================================
        // 10. View: v_attendance_report
        // ==========================================
        DB::unprepared("
            DROP VIEW IF EXISTS v_attendance_report; -- Hapus view yang lama/salah konsep
            DROP VIEW IF EXISTS v_class_attendance_summary;

            CREATE VIEW v_class_attendance_summary AS
            SELECT 
                s.id AS student_id,
                s.class_id,
                s.student_number,
                s.name AS student_name,
                
                -- Panggil Function Hitung-hitungan Anda disini
                f_get_student_attendance_total(s.class_id, s.id) AS total_present,
                f_get_attendance_percentage(s.class_id, s.id) AS attendance_percentage

            FROM students s
            WHERE s.deleted_at IS NULL AND s.is_active = 1
            ORDER BY s.student_number ASC;
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("DROP VIEW IF EXISTS v_unified_trash"); // Drop Trash View
        DB::unprepared("DROP VIEW IF EXISTS v_class_activity_logs");
        DB::unprepared("DROP VIEW IF EXISTS v_student_grades");
        DB::unprepared("DROP VIEW IF EXISTS v_teacher_attendance");
        DB::unprepared("DROP VIEW IF EXISTS v_student_attendance");
        DB::unprepared("DROP VIEW IF EXISTS v_today_schedule");
        DB::unprepared("DROP VIEW IF EXISTS v_attendance_summary"); 
        DB::unprepared("DROP VIEW IF EXISTS v_weekly_absence");
        DB::unprepared("DROP VIEW IF EXISTS v_teacher_teaching_history");
    }
};