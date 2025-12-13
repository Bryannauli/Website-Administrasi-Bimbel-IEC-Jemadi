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
        // 3. PROCEDURE: Student Attendance Summary (Per Siswa)
        // ==========================================
        DB::unprepared("
            DROP PROCEDURE IF EXISTS p_get_attendance_summary;
            CREATE PROCEDURE p_get_attendance_summary (IN studentIdIn INT)
            BEGIN
                SELECT
                    COUNT(ar.id) AS total_days,
                    SUM(CASE WHEN ar.status = 'present' THEN 1 ELSE 0 END) AS present,
                    SUM(CASE WHEN ar.status = 'absent' THEN 1 ELSE 0 END) AS absent,
                    SUM(CASE WHEN ar.status = 'late' THEN 1 ELSE 0 END) AS late,
                    SUM(CASE WHEN ar.status = 'permission' THEN 1 ELSE 0 END) AS permission,
                    SUM(CASE WHEN ar.status = 'sick' THEN 1 ELSE 0 END) AS sick,
                    IFNULL((SUM(CASE WHEN ar.status = 'present' THEN 1 ELSE 0 END) / COUNT(ar.id)) * 100, 0) AS present_percent
                FROM attendance_records ar
                JOIN attendance_sessions s ON ar.attendance_session_id = s.id
                JOIN students stu ON ar.student_id = stu.id
                WHERE ar.student_id = studentIdIn
                    AND s.class_id = stu.class_id; 
            END
        ");

        // ==========================================
        // 4. PROCEDURE: Class Attendance Stats (Untuk Modal Detail Kelas)
        // ==========================================
        DB::unprepared("
            DROP PROCEDURE IF EXISTS p_get_class_attendance_stats;
            CREATE PROCEDURE p_get_class_attendance_stats (IN classId INT)
            BEGIN
                SELECT 
                    s.id AS student_id,
                    s.name,
                    s.student_number,
                    s.is_active, 
                    COUNT(ar.id) AS total_sessions_recorded,
                    SUM(CASE WHEN ar.status IN ('present', 'late') THEN 1 ELSE 0 END) AS total_present,
                    ROUND(
                        (SUM(CASE WHEN ar.status IN ('present', 'late') THEN 1 ELSE 0 END) / COUNT(ar.id)) * 100
                    ) AS percentage
                FROM students s
                LEFT JOIN attendance_records ar ON s.id = ar.student_id
                WHERE s.class_id = classId
                GROUP BY s.id, s.name, s.student_number, s.is_active
                ORDER BY percentage ASC;
            END
        ");

        // ==========================================
        // 5. PROCEDURE: Student Global Stats
        // ==========================================
        DB::unprepared('
            DROP PROCEDURE IF EXISTS p_get_student_global_stats;
            CREATE PROCEDURE p_get_student_global_stats(
                OUT total_students INT,
                OUT total_active INT,
                OUT total_inactive INT
            )
            BEGIN
                SELECT COUNT(*) INTO total_students FROM students WHERE deleted_at IS NULL;
                SELECT COUNT(*) INTO total_active FROM students WHERE is_active = 1 AND deleted_at IS NULL;
                SELECT COUNT(*) INTO total_inactive FROM students WHERE is_active = 0 AND deleted_at IS NULL;
            END
        ');

        // ==========================================
        // 6. PROCEDURE: p_UpdateStudentGrade (FIXED: NO is_submitted)
        // ==========================================
        DB::unprepared('
            DROP PROCEDURE IF EXISTS p_UpdateStudentGrade;
            CREATE PROCEDURE p_UpdateStudentGrade(
                IN p_session_id INT,
                IN p_student_id INT,
                IN p_form_id INT,          
                IN p_speaking_test_id INT,
                
                IN p_vocab INT,
                IN p_grammar INT,
                IN p_listening INT,
                IN p_reading INT,
                IN p_spelling INT,
                
                IN p_s_content INT,
                IN p_s_partic INT
            )
            BEGIN
                DECLARE total_speaking INT;
                
                DECLARE EXIT HANDLER FOR SQLEXCEPTION
                BEGIN
                    ROLLBACK;
                    RESIGNAL;
                END;

                START TRANSACTION;
                
                -- 1. Simpan Detail Speaking
                INSERT INTO speaking_test_results (speaking_test_id, student_id, content_score, participation_score)
                VALUES (p_speaking_test_id, p_student_id, p_s_content, p_s_partic)
                ON DUPLICATE KEY UPDATE
                    content_score = p_s_content,
                    participation_score = p_s_partic;
                
                -- Hitung Total Speaking
                SET total_speaking = p_s_content + p_s_partic;

                -- 2. Simpan Nilai Tertulis (TANPA kolom is_submitted)
                IF EXISTS (SELECT 1 FROM assessment_forms WHERE student_id = p_student_id AND assessment_session_id = p_session_id) THEN
                    -- UPDATE Existing Record
                    UPDATE assessment_forms
                    SET
                        vocabulary = p_vocab,
                        grammar = p_grammar,
                        listening = p_listening,
                        reading = p_reading,
                        spelling = p_spelling,
                        speaking = total_speaking
                    WHERE student_id = p_student_id AND assessment_session_id = p_session_id;
                ELSE
                    -- INSERT New Record
                    INSERT INTO assessment_forms (student_id, assessment_session_id, vocabulary, grammar, listening, reading, spelling, speaking)
                    VALUES (p_student_id, p_session_id, p_vocab, p_grammar, p_listening, p_reading, p_spelling, total_speaking);
                END IF;
                    
                COMMIT;
            END
        ');
    }

    public function down(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS p_UpdateStudentGrade');
        DB::unprepared('DROP PROCEDURE IF EXISTS p_GetDashboardStats');
        DB::unprepared('DROP PROCEDURE IF EXISTS p_GetAttendanceStats');
        DB::unprepared('DROP PROCEDURE IF EXISTS p_get_attendance_summary');
        DB::unprepared('DROP PROCEDURE IF EXISTS p_get_class_attendance_stats');
        DB::unprepared('DROP PROCEDURE IF EXISTS p_get_student_global_stats');
    }
};