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
        // 3 variabel yang nanti akan diisi hasilnya
        DB::unprepared('
            DROP PROCEDURE IF EXISTS p_GetDashboardStats;
            CREATE PROCEDURE p_GetDashboardStats(
                OUT total_students INT,
                OUT total_teachers INT,
                OUT total_classes INT
            )
            BEGIN
                -- Hitung semua baris di tabel students, lalu simpan hasilnya ke variabel total_students
                -- Hanya hitung siswa yang aktif dan tidak dihapus (deleted_at IS NULL)
                SELECT COUNT(*) INTO total_students FROM students WHERE is_active = 1 AND deleted_at IS NULL;

                -- Hitung semua baris di tabel users yang merupakan guru, lalu simpan hasilnya ke variabel total_teachers
                -- Hanya hitung guru yang aktif dan tidak dihapus (deleted_at IS NULL)
                SELECT COUNT(*) INTO total_teachers FROM users WHERE is_teacher = 1 AND is_active = 1 AND deleted_at IS NULL;

                -- Hitung semua baris di tabel classes, lalu simpan hasilnya ke variabel total_classes
                -- Hanya hitung kelas yang aktif dan tidak dihapus (deleted_at IS NULL)
                SELECT COUNT(*) INTO total_classes FROM classes WHERE is_active = 1 AND deleted_at IS NULL;
            END
        ');

        // ==========================================
        // 2. PROCEDURE: Student Attendance Summary (Per Siswa) (ADMIN)
        // ==========================================
        DB::unprepared("
            DROP PROCEDURE IF EXISTS p_get_attendance_summary;
            CREATE PROCEDURE p_get_attendance_summary (IN studentIdIn INT)
            BEGIN
                SELECT
                    -- hitung berapa banyak data absensi yg ditemukan untuk siswa ini disimpan dalam total_days
                    COUNT(ar.id) AS total_days,

                    -- hitung jika present dihitung 1, selain itu 0, lalu dijumlahkan
                    SUM(CASE WHEN ar.status = 'present' THEN 1 ELSE 0 END) AS present,
                    SUM(CASE WHEN ar.status = 'absent' THEN 1 ELSE 0 END) AS absent,
                    SUM(CASE WHEN ar.status = 'late' THEN 1 ELSE 0 END) AS late,
                    SUM(CASE WHEN ar.status = 'permission' THEN 1 ELSE 0 END) AS permission,
                    SUM(CASE WHEN ar.status = 'sick' THEN 1 ELSE 0 END) AS sick,

                    -- hitung persentase kehadiran sebagai present / total_days * 100
                    IFNULL((SUM(CASE WHEN ar.status = 'present' THEN 1 ELSE 0 END) / COUNT(ar.id)) * 100, 0) AS present_percent

                FROM attendance_records ar
                -- join ke tabel class_sessions untuk mendapatkan informasi sesi kelas
                JOIN class_sessions s ON ar.class_session_id = s.id

                -- join ke tabel students untuk mendapatkan class_id siswa
                JOIN students stu ON ar.student_id = stu.id

                -- hanya ambil data untuk studentIdIn
                WHERE ar.student_id = studentIdIn

                -- pastikan hanya menghitung sesi dari kelas dimana siswa tersebut terdaftar
                    AND s.class_id = stu.class_id; 
            END
        ");

        // ==========================================
        // 3. PROCEDURE: Class Attendance Stats (ADMIN & TEACHER) [UPDATED]
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
                    
                    -- total sesi yang sudah direkam untuk kelas ini
                    COUNT(cs.id) AS total_sessions_recorded,
                    
                    -- hitung total kehadiran (present + late)
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
        // 4. PROCEDURE: Create Class [UPDATED]
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
            -- simpan guru yg uji speaking
                DECLARE v_interviewer_id BIGINT;

                -- Error Handling
                -- Batalkan transaksi jika ada error
                DECLARE EXIT HANDLER FOR SQLEXCEPTION
                BEGIN
                    ROLLBACK;
                    RESIGNAL;
                END;
                -- mulai transaksi
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
                -- p_schedules adalah parameter JSON yang berisi array jadwal
                    p_schedules, 
                    '$[*]' COLUMNS (
                        day VARCHAR(20) PATH '$.day',
                        type VARCHAR(20) PATH '$.type'
                    )
                ) AS jt;

                -- C. Otomatisasi Assessment (Include Speaking Info di sini)
                -- Tentukan interviewer_id dari local_teacher_id
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
        // 5. PROCEDURE: Teacher Global Stats (ADMIN)
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
        // 6. PROCEDURE: Get Session Attendance List (TEACHER & ADMIN)
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
        // 7. PROCEDURE: Get Assessment Sheet (ADMIN) [BARU]
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

        // ==========================================
        // 8. PROCEDURE: Save Assessment Batch (ADMIN & TEACHER) [VALIDATED]
        // Deskripsi: Simpan Header & Nilai. 
        // UPDATE: Menolak penyimpanan jika ada siswa yang nilainya tidak lengkap (Kecuali Spelling).
        // ==========================================
        DB::unprepared("
            DROP PROCEDURE IF EXISTS p_SaveAssessmentBatch;
            CREATE PROCEDURE p_SaveAssessmentBatch(
                IN p_session_id INT,
                IN p_written_date DATE,
                IN p_speaking_date DATE,
                IN p_interviewer_id BIGINT,
                IN p_topic VARCHAR(255),
                IN p_status VARCHAR(20),
                IN p_marks_json JSON
            )
            BEGIN
                DECLARE v_invalid_count INT DEFAULT 0;

                DECLARE EXIT HANDLER FOR SQLEXCEPTION
                BEGIN
                    ROLLBACK;
                    RESIGNAL;
                END;

                START TRANSACTION;

                -- ==========================================================
                -- A. VALIDASI DATA (BENTENG TERAKHIR)
                -- Cek apakah ada siswa yang punya data parsial tapi tidak lengkap wajibnya
                -- ==========================================================
                SELECT COUNT(*) INTO v_invalid_count
                FROM JSON_TABLE(
                    p_marks_json,
                    '$[*]' COLUMNS (
                        vocab INT PATH '$.vocabulary',
                        grammar INT PATH '$.grammar',
                        listening INT PATH '$.listening',
                        reading INT PATH '$.reading',
                        spelling INT PATH '$.spelling',
                        s_content INT PATH '$.speaking_content',
                        s_partic INT PATH '$.speaking_participation'
                    )
                ) AS jt
                WHERE 
                    -- 1. Cek apakah Siswa ini 'Disentuh' (Ada minimal 1 nilai terisi, termasuk spelling)
                    (
                        jt.vocab IS NOT NULL OR 
                        jt.grammar IS NOT NULL OR 
                        jt.listening IS NOT NULL OR 
                        jt.reading IS NOT NULL OR 
                        jt.spelling IS NOT NULL OR 
                        jt.s_content IS NOT NULL OR 
                        jt.s_partic IS NOT NULL
                    )
                    AND
                    -- 2. Jika Disentuh, Cek Kelengkapan Field WAJIB (Spelling TIDAK dicek disini)
                    (
                        jt.vocab IS NULL OR 
                        jt.grammar IS NULL OR 
                        jt.listening IS NULL OR 
                        jt.reading IS NULL OR 
                        jt.s_content IS NULL OR 
                        jt.s_partic IS NULL
                    );

                -- Jika ditemukan data tidak valid, lempar Error ke Laravel
                IF v_invalid_count > 0 THEN
                    SIGNAL SQLSTATE '45000' 
                    SET MESSAGE_TEXT = 'Database Integrity Error: One or more students have incomplete mandatory grades. Spelling is optional, but other fields are required if the student is graded.';
                END IF;

                -- ==========================================================
                -- B. PROSES SIMPAN (Jika Lolos Validasi)
                -- ==========================================================

                -- 1. Update Header Assessment (Session)
                UPDATE assessment_sessions 
                SET 
                    written_date = p_written_date,
                    speaking_date = p_speaking_date,
                    interviewer_id = p_interviewer_id,
                    speaking_topic = p_topic,
                    status = IF(p_status IS NOT NULL, p_status, status), 
                    updated_at = NOW()
                WHERE id = p_session_id;

                -- 2. Bulk Upsert ke Table speaking_test_results
                -- Kita ekstrak content_score dan participation_score dari JSON.
                -- Simpan ke tabel speaking_test_results.
                -- Jika sudah ada, update nilainya.
                INSERT INTO speaking_test_results (assessment_session_id, student_id, content_score, participation_score, created_at, updated_at)
                SELECT 
                    p_session_id,
                    jt.student_id,
                    jt.s_content,
                    jt.s_partic,
                    NOW(),
                    NOW()
                FROM JSON_TABLE(
                    p_marks_json,
                    '$[*]' COLUMNS (
                        student_id INT PATH '$.student_id',
                        s_content INT PATH '$.speaking_content',
                        s_partic INT PATH '$.speaking_participation'
                    )
                ) AS jt
                ON DUPLICATE KEY UPDATE
                    content_score = VALUES(content_score),
                    participation_score = VALUES(participation_score),
                    updated_at = NOW();

                -- 3. Bulk Upsert ke Table assessment_forms
                INSERT INTO assessment_forms (assessment_session_id, student_id, vocabulary, grammar, listening, reading, spelling, speaking, created_at, updated_at)
                SELECT 
                    p_session_id,
                    jt.student_id,
                    jt.vocab,
                    jt.grammar,
                    jt.listening,
                    jt.reading,
                    jt.spelling,
                    (IFNULL(jt.s_content, 0) + IFNULL(jt.s_partic, 0)), 
                    NOW(),
                    NOW()
                FROM JSON_TABLE(
                    p_marks_json,
                    '$[*]' COLUMNS (
                        student_id INT PATH '$.student_id',
                        vocab INT PATH '$.vocabulary',
                        grammar INT PATH '$.grammar',
                        listening INT PATH '$.listening',
                        reading INT PATH '$.reading',
                        spelling INT PATH '$.spelling',
                        s_content INT PATH '$.speaking_content',
                        s_partic INT PATH '$.speaking_participation'
                    )
                ) AS jt
                ON DUPLICATE KEY UPDATE
                    vocabulary = VALUES(vocabulary),
                    grammar = VALUES(grammar),
                    listening = VALUES(listening),
                    reading = VALUES(reading),
                    spelling = VALUES(spelling),
                    speaking = VALUES(speaking),
                    updated_at = NOW();

                COMMIT;
            END
        ");

        // ==========================================
        // 9. PROCEDURE: Save Attendance Batch (TEACHER) (NEW & OPTIMIZED)
        // Deskripsi: Simpan Absensi Banyak Siswa SEKALIGUS (Batch) via JSON.
        // ==========================================
        DB::unprepared("
            DROP PROCEDURE IF EXISTS p_SaveAttendanceBatch;
            CREATE PROCEDURE p_SaveAttendanceBatch(
                IN p_session_id INT,
                IN p_attendance_json JSON
            )
            BEGIN
                DECLARE EXIT HANDLER FOR SQLEXCEPTION
                BEGIN
                    ROLLBACK;
                    RESIGNAL;
                END;

                START TRANSACTION;

                -- Bulk Upsert ke Table attendance_records menggunakan JSON_TABLE
                INSERT INTO attendance_records (class_session_id, student_id, status, created_at, updated_at)
                SELECT 
                    p_session_id,
                    jt.student_id,
                    jt.status,
                    NOW(),
                    NOW()
                FROM JSON_TABLE(
                    p_attendance_json,
                    '$[*]' COLUMNS (
                        student_id INT PATH '$.student_id',
                        status VARCHAR(20) PATH '$.status'
                    )
                ) AS jt
                 -- dilihat aoakah duplikat datanya
                ON DUPLICATE KEY UPDATE
                    status = VALUES(status),
                    updated_at = NOW();

                COMMIT;
            END
        ");
    }

    public function down(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS p_CreateClass'); 
        DB::unprepared('DROP PROCEDURE IF EXISTS p_GetDashboardStats');
        DB::unprepared('DROP PROCEDURE IF EXISTS p_get_attendance_summary');
        DB::unprepared('DROP PROCEDURE IF EXISTS p_get_class_attendance_stats');
        DB::unprepared('DROP PROCEDURE IF EXISTS p_get_teacher_global_stats');
        DB::unprepared('DROP PROCEDURE IF EXISTS p_GetSessionAttendanceList');
        DB::unprepared('DROP PROCEDURE IF EXISTS p_GetAssessmentSheet');
        DB::unprepared('DROP PROCEDURE IF EXISTS p_SaveAssessmentBatch');
        DB::unprepared('DROP PROCEDURE IF EXISTS p_SaveAttendanceBatch');
    }
};