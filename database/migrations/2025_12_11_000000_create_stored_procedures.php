<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ==========================================
        // 1. PROCEDURE: Dashboard Stats (ADMIN)
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
        // 2. PROCEDURE: Attendance Stats (Global/Class) (ADMIN)
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
                INNER JOIN class_sessions s ON ar.class_session_id = s.id
                WHERE (date_filter IS NULL OR s.date = date_filter);
            END
        ");

        // ==========================================
        // 3. PROCEDURE: Student Attendance Summary (Per Siswa) (ADMIN)
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
                JOIN class_sessions s ON ar.class_session_id = s.id
                JOIN students stu ON ar.student_id = stu.id
                WHERE ar.student_id = studentIdIn
                    AND s.class_id = stu.class_id; 
            END
        ");

        // ==========================================
        // 4. PROCEDURE: Class Attendance Stats (ADMIN & TEACHER) [UPDATED]
        // Deskripsi: Menampilkan statistik kehadiran per siswa dalam satu kelas.
        // UPDATE: Menampilkan siswa yang Unassigned TAPI punya history kehadiran di kelas ini.
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
                    s.deleted_at,  -- <<< INI WAJIB ADA AGAR WARNA DI MODAL BERUBAH
                    
                    COUNT(cs.id) AS total_sessions_recorded,
                    SUM(CASE WHEN ar.status IN ('present', 'late') THEN 1 ELSE 0 END) AS total_present,
                    ROUND(
                        (SUM(CASE WHEN ar.status IN ('present', 'late') THEN 1 ELSE 0 END) / NULLIF(COUNT(cs.id), 0)) * 100
                    ) AS percentage

                FROM students s
                LEFT JOIN attendance_records ar ON s.id = ar.student_id
                LEFT JOIN class_sessions cs ON ar.class_session_id = cs.id AND cs.class_id = classId

                WHERE 
                    s.class_id = classId 
                    OR 
                    cs.id IS NOT NULL

                GROUP BY s.id, s.name, s.student_number, s.is_active, s.deleted_at
                ORDER BY s.student_number ASC; 
            END
        ");

        // ==========================================
        // 5. PROCEDURE: Student Global Stats (ADMIN)
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
        // 6. PROCEDURE: p_UpdateStudentGrade [UPDATED]
        // Deskripsi: Update nilai siswa (Written & Speaking) dalam satu transaksi.
        // Perubahan: Menghapus parameter p_speaking_test_id.
        // ==========================================
        DB::unprepared('
            DROP PROCEDURE IF EXISTS p_UpdateStudentGrade;
            CREATE PROCEDURE p_UpdateStudentGrade(
                IN p_session_id INT,
                IN p_student_id INT,
                IN p_form_id INT,          
                -- IN p_speaking_test_id INT,  <-- DIHAPUS
                
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
                
                -- 1. Simpan Detail Speaking (Langsung pakai Session ID)
                INSERT INTO speaking_test_results (assessment_session_id, student_id, content_score, participation_score, created_at, updated_at)
                VALUES (p_session_id, p_student_id, p_s_content, p_s_partic, NOW(), NOW())
                ON DUPLICATE KEY UPDATE
                    content_score = p_s_content,
                    participation_score = p_s_partic,
                    updated_at = NOW();
                
                -- Hitung Total Speaking
                SET total_speaking = IFNULL(p_s_content, 0) + IFNULL(p_s_partic, 0);
                IF p_s_content IS NULL AND p_s_partic IS NULL THEN
                    SET total_speaking = NULL;
                END IF;

                -- 2. Simpan Nilai Tertulis
                IF EXISTS (SELECT 1 FROM assessment_forms WHERE student_id = p_student_id AND assessment_session_id = p_session_id) THEN
                    -- UPDATE Existing Record
                    UPDATE assessment_forms
                    SET
                        vocabulary = p_vocab,
                        grammar = p_grammar,
                        listening = p_listening,
                        reading = p_reading,
                        spelling = p_spelling,
                        speaking = total_speaking,
                        updated_at = NOW()
                    WHERE student_id = p_student_id AND assessment_session_id = p_session_id;
                ELSE
                    -- INSERT New Record
                    INSERT INTO assessment_forms (student_id, assessment_session_id, vocabulary, grammar, listening, reading, spelling, speaking, created_at, updated_at)
                    VALUES (p_student_id, p_session_id, p_vocab, p_grammar, p_listening, p_reading, p_spelling, total_speaking, NOW(), NOW());
                END IF;
                    
                COMMIT;
            END
        ');

        // ==========================================
        // 7. PROCEDURE: Create Class [UPDATED]
        // Deskripsi: Membuat kelas beserta jadwal ujian otomatis.
        // Perubahan: Menghapus insert ke tabel speaking_tests.
        // ==========================================
        DB::unprepared("
            DROP PROCEDURE IF EXISTS p_CreateClass;
            CREATE PROCEDURE p_CreateClass(
                IN p_category VARCHAR(50),
                IN p_name VARCHAR(100),
                IN p_classroom VARCHAR(50),
                IN p_start_month VARCHAR(20),
                IN p_end_month VARCHAR(20),
                IN p_academic_year VARCHAR(20),
                IN p_form_teacher_id BIGINT,
                IN p_local_teacher_id BIGINT,
                IN p_start_time TIME,
                IN p_end_time TIME,
                IN p_schedules JSON,  
                OUT p_new_class_id BIGINT
            )
            BEGIN
                DECLARE v_interviewer_id BIGINT;

                DECLARE EXIT HANDLER FOR SQLEXCEPTION
                BEGIN
                    ROLLBACK;
                    RESIGNAL;
                END;

                START TRANSACTION; 

                -- A. Insert Data Kelas
                INSERT INTO classes (
                    category, name, classroom, start_month, end_month, 
                    academic_year, form_teacher_id, local_teacher_id, 
                    start_time, end_time, is_active, created_at, updated_at
                ) VALUES (
                    p_category, p_name, p_classroom, p_start_month, p_end_month, 
                    p_academic_year, p_form_teacher_id, p_local_teacher_id, 
                    p_start_time, p_end_time, 1, NOW(), NOW()
                );

                SET p_new_class_id = LAST_INSERT_ID();

                -- B. Insert Data Jadwal dari JSON
                INSERT INTO schedules (class_id, day_of_week, teacher_type, created_at, updated_at)
                SELECT 
                    p_new_class_id, 
                    jt.day, 
                    jt.type, 
                    NOW(), 
                    NOW()
                FROM JSON_TABLE(
                    p_schedules, 
                    '$[*]' COLUMNS (
                        day VARCHAR(20) PATH '$.day',
                        type VARCHAR(20) PATH '$.type'
                    )
                ) AS jt;

                -- C. Otomatisasi Assessment (Include Speaking Info di sini)
                SET v_interviewer_id = p_local_teacher_id;

                -- Mid Term (Insert interviewer default ke session)
                INSERT INTO assessment_sessions (class_id, type, date, speaking_date, speaking_topic, interviewer_id, status, created_at, updated_at)
                VALUES (p_new_class_id, 'mid', NULL, NULL, NULL, v_interviewer_id, 'draft', NOW(), NOW());

                -- Final Exam
                INSERT INTO assessment_sessions (class_id, type, date, speaking_date, speaking_topic, interviewer_id, status, created_at, updated_at)
                VALUES (p_new_class_id, 'final', NULL, NULL, NULL, v_interviewer_id, 'draft', NOW(), NOW());

                COMMIT; 
            END
        ");

        // ==========================================
        // 8. PROCEDURE: Teacher Global Stats (ADMIN)
        // ==========================================
        DB::unprepared('
            DROP PROCEDURE IF EXISTS p_get_teacher_global_stats;
            CREATE PROCEDURE p_get_teacher_global_stats(
                OUT total_teachers INT,
                OUT total_active INT,
                OUT total_inactive INT
            )
            BEGIN
                SELECT COUNT(*) INTO total_teachers FROM users WHERE is_teacher = 1 AND deleted_at IS NULL;
                SELECT COUNT(*) INTO total_active FROM users WHERE is_teacher = 1 AND is_active = 1 AND deleted_at IS NULL;
                SELECT COUNT(*) INTO total_inactive FROM users WHERE is_teacher = 1 AND is_active = 0 AND deleted_at IS NULL;
            END
        ');

        // ==========================================
        // 9. PROCEDURE: Get Session Attendance List (TEACHER & ADMIN)
        // ==========================================
        DB::unprepared("
            DROP PROCEDURE IF EXISTS p_GetSessionAttendanceList;
            CREATE PROCEDURE p_GetSessionAttendanceList(
                IN p_class_id INT,
                IN p_session_id INT
            )
            BEGIN
                SELECT 
                    s.id,
                    s.name,
                    s.student_number,
                    CASE 
                        WHEN ar.status = 'permission' THEN 'permitted'
                        ELSE ar.status 
                    END as current_status
                FROM students s
                LEFT JOIN attendance_records ar 
                    ON s.id = ar.student_id AND ar.class_session_id = p_session_id
                WHERE s.class_id = p_class_id
                    AND s.is_active = 1
                    AND s.deleted_at IS NULL
                ORDER BY s.student_number ASC;
            END
        ");

        // ==========================================
        // 10. PROCEDURE: Upsert Attendance (TEACHER & ADMIN)
        // ==========================================
        DB::unprepared("
            DROP PROCEDURE IF EXISTS p_UpsertAttendance;
            CREATE PROCEDURE p_UpsertAttendance(
                IN p_session_id INT,
                IN p_student_id INT,
                IN p_status VARCHAR(20)
            )
            BEGIN
                INSERT INTO attendance_records (class_session_id, student_id, status, created_at, updated_at)
                VALUES (p_session_id, p_student_id, p_status, NOW(), NOW())
                ON DUPLICATE KEY UPDATE
                    status = p_status,
                    updated_at = NOW();
            END
        ");

        // ==========================================
        // 11. PROCEDURE: Get Assessment Sheet (ADMIN) [BARU]
        // Deskripsi: Mengambil data siswa + nilai untuk lembar penilaian.
        // Menggabungkan siswa aktif di kelas tsb ATAU siswa non-aktif yang sudah punya nilai.
        // ==========================================
        DB::unprepared("
            DROP PROCEDURE IF EXISTS p_GetAssessmentSheet;
            CREATE PROCEDURE p_GetAssessmentSheet(
                IN p_class_id INT,
                IN p_session_id INT
            )
            BEGIN
                SELECT 
                    -- Info Siswa
                    s.id AS student_id,
                    s.name,
                    s.student_number,
                    s.is_active,
                    s.deleted_at,
                    s.class_id AS current_class_id,

                    -- Nilai (Dari View v_student_grades)
                    vg.form_id,
                    vg.vocabulary,
                    vg.grammar,
                    vg.listening,
                    vg.reading,
                    vg.spelling,
                    vg.speaking_content,
                    vg.speaking_participation,
                    (IFNULL(vg.speaking_content, 0) + IFNULL(vg.speaking_participation, 0)) AS speaking_total,
                    vg.final_score,
                    vg.grade_text

                FROM students s
                -- Join ke View Nilai (Filter berdasarkan Session ID)
                LEFT JOIN v_student_grades vg 
                    ON s.id = vg.student_id AND vg.assessment_session_id = p_session_id

                WHERE 
                    -- 1. Siswa yang MASIH ada di kelas ini
                    s.class_id = p_class_id
                    
                    OR
                    
                    -- 2. Siswa yang SUDAH PUNYA nilai di sesi ini (meskipun sudah pindah/keluar/dihapus)
                    vg.form_id IS NOT NULL

                ORDER BY s.student_number ASC;
            END
        ");
    }

    public function down(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS p_CreateClass'); 
        DB::unprepared('DROP PROCEDURE IF EXISTS p_UpdateStudentGrade');
        DB::unprepared('DROP PROCEDURE IF EXISTS p_GetDashboardStats');
        DB::unprepared('DROP PROCEDURE IF EXISTS p_GetAttendanceStats');
        DB::unprepared('DROP PROCEDURE IF EXISTS p_get_attendance_summary');
        DB::unprepared('DROP PROCEDURE IF EXISTS p_get_class_attendance_stats');
        DB::unprepared('DROP PROCEDURE IF EXISTS p_get_student_global_stats');
        DB::unprepared('DROP PROCEDURE IF EXISTS p_get_teacher_global_stats');
        DB::unprepared('DROP PROCEDURE IF EXISTS p_GetSessionAttendanceList');
        DB::unprepared('DROP PROCEDURE IF EXISTS p_UpsertAttendance');
        DB::unprepared('DROP PROCEDURE IF EXISTS p_GetAssessmentSheet');
    }
};