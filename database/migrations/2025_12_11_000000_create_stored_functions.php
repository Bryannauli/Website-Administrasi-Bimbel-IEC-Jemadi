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
        // =================================================================
        // 1. FUNCTION: f_CalcAssessmentAvg
        // Deskripsi: Menghitung rata-rata nilai dari komponen yang ada (tidak null).
        // =================================================================
        DB::unprepared('
            DROP FUNCTION IF EXISTS f_CalcAssessmentAvg;
            
            CREATE FUNCTION f_CalcAssessmentAvg(
                val_vocab INT,
                val_grammar INT,
                val_listening INT,
                val_reading INT,
                val_spelling INT,
                val_speaking INT
            ) 
            RETURNS INT
            DETERMINISTIC
            BEGIN
                DECLARE total_score INT DEFAULT 0;
                DECLARE divisor INT DEFAULT 0;

                -- Cek Vocabulary
                IF val_vocab IS NOT NULL THEN 
                    SET total_score = total_score + val_vocab; 
                    SET divisor = divisor + 1; 
                END IF;

                -- Cek Grammar
                IF val_grammar IS NOT NULL THEN 
                    SET total_score = total_score + val_grammar; 
                    SET divisor = divisor + 1; 
                END IF;

                -- Cek Listening
                IF val_listening IS NOT NULL THEN 
                    SET total_score = total_score + val_listening; 
                    SET divisor = divisor + 1; 
                END IF;

                -- Cek Reading
                IF val_reading IS NOT NULL THEN 
                    SET total_score = total_score + val_reading; 
                    SET divisor = divisor + 1; 
                END IF;

                -- Cek Spelling (Opsional)
                IF val_spelling IS NOT NULL THEN 
                    SET total_score = total_score + val_spelling; 
                    SET divisor = divisor + 1; 
                END IF;

                -- Cek Speaking (Total)
                IF val_speaking IS NOT NULL THEN 
                    SET total_score = total_score + val_speaking; 
                    SET divisor = divisor + 1; 
                END IF;

                -- Mencegah Error Division by Zero (Jika semua NULL)
                IF divisor = 0 THEN 
                    RETURN NULL;
                END IF;

                -- Hitung Rata-rata & Bulatkan (ROUND)
                RETURN ROUND(total_score / divisor);
            END
        ');

        // =================================================================
        // 2. FUNCTION: f_GetGrade
        // Deskripsi: Mengonversi skor angka menjadi Predikat/Grade Text.
        // Aturan:
        // 90 - 100 : Outstanding
        // 80 - 89  : Distinction
        // 70 - 79  : Credit
        // 50 - 69  : Acceptable
        // 40 - 49  : Unsatisfactory
        // <= 39    : Insufficient
        // =================================================================
        DB::unprepared('
            DROP FUNCTION IF EXISTS f_GetGrade;

            CREATE FUNCTION f_GetGrade(score INT) RETURNS VARCHAR(20)
            DETERMINISTIC
            BEGIN
                IF score IS NULL THEN
                    RETURN NULL;
                ELSEIF score >= 90 THEN 
                    RETURN "Outstanding";
                ELSEIF score >= 80 THEN 
                    RETURN "Distinction";
                ELSEIF score >= 70 THEN 
                    RETURN "Credit";
                ELSEIF score >= 50 THEN 
                    RETURN "Acceptable";
                ELSEIF score >= 40 THEN 
                    RETURN "Unsatisfactory";
                ELSE 
                    RETURN "Insufficient";
                END IF;
            END
        ');

        // ==========================================
        // 3. FUNCTION: Get Total Sessions (Pengganti Procedure)
        // ==========================================
        DB::unprepared("
            DROP FUNCTION IF EXISTS f_get_total_sessions;
            DROP PROCEDURE IF EXISTS p_get_total_sessions; -- Hapus procedure lama jika ada
            
            CREATE FUNCTION f_get_total_sessions(p_class_id INT) 
            RETURNS INT
            READS SQL DATA
            BEGIN
                DECLARE v_total INT DEFAULT 0;
                
                SELECT COUNT(*) INTO v_total 
                FROM class_sessions 
                WHERE class_id = p_class_id 
                  AND deleted_at IS NULL;
                  
                RETURN v_total;
            END
        ");

        // ==========================================
        // 4. FUNCTION: Get Student Total Present (Pengganti Procedure)
        // ==========================================
        DB::unprepared("
            DROP FUNCTION IF EXISTS f_get_student_attendance_total;
            DROP PROCEDURE IF EXISTS p_get_student_attendance_total; -- Hapus procedure lama jika ada

            CREATE FUNCTION f_get_student_attendance_total(p_class_id INT, p_student_id INT) 
            RETURNS INT
            READS SQL DATA
            BEGIN
                DECLARE v_total_present INT DEFAULT 0;
                
                SELECT COUNT(ar.id) INTO v_total_present
                FROM attendance_records ar
                INNER JOIN class_sessions cs ON ar.class_session_id = cs.id
                WHERE ar.student_id = p_student_id
                  AND cs.class_id = p_class_id
                  AND ar.status = 'present'
                  AND cs.deleted_at IS NULL;
                  
                RETURN v_total_present;
            END
        ");
        
        // ==========================================
        // 5. FUNCTION: Get Attendance Percentage (UPDATED)
        // Karena kita sudah punya 2 fungsi di atas, fungsi persen jadi lebih simpel & bersih
        // ==========================================
        DB::unprepared("
            DROP FUNCTION IF EXISTS f_get_attendance_percentage;
            CREATE FUNCTION f_get_attendance_percentage(p_class_id INT, p_student_id INT) 
            RETURNS INT
            READS SQL DATA
            BEGIN
                DECLARE v_total_sessions INT;
                DECLARE v_total_present INT;
                
                -- Panggil fungsi yang baru saja kita buat
                SET v_total_sessions = f_get_total_sessions(p_class_id);
                SET v_total_present = f_get_student_attendance_total(p_class_id, p_student_id);
                
                IF v_total_sessions = 0 THEN
                    RETURN 0;
                END IF;

                RETURN ROUND((v_total_present / v_total_sessions) * 100);
            END
        ");

        // ==========================================
        // 6. FUNCTION: Get Attendance Symbol (Untuk Report View)
        // Menerjemahkan status (present, sick, dll) menjadi simbol (/, S, P, dll)
        // ==========================================
        DB::unprepared("
            DROP FUNCTION IF EXISTS f_get_attendance_symbol;
            CREATE FUNCTION f_get_attendance_symbol(p_session_id INT, p_student_id INT) 
            RETURNS VARCHAR(5)
            READS SQL DATA
            BEGIN
                DECLARE v_status VARCHAR(20);
                
                -- 1. Ambil status dari tabel attendance_records
                SELECT status INTO v_status
                FROM attendance_records
                WHERE class_session_id = p_session_id 
                  AND student_id = p_student_id
                LIMIT 1;
                
                -- 2. Kembalikan simbol sesuai mapping
                RETURN CASE 
                    WHEN v_status = 'present' THEN '/'
                    WHEN v_status = 'absent' THEN 'O'
                    WHEN v_status = 'late' THEN 'L'      -- Saya pakai 'L' kapital agar terlihat jelas seperti di screenshot
                    WHEN v_status = 'permission' THEN 'P'
                    WHEN v_status = 'sick' THEN 'S'
                    ELSE '' -- Return string kosong jika belum ada data absen
                END;
            END
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP FUNCTION IF EXISTS f_GetGrade');
        DB::unprepared('DROP FUNCTION IF EXISTS f_CalcAssessmentAvg');
        DB::unprepared('DROP FUNCTION IF EXISTS f_get_total_sessions');
        DB::unprepared('DROP FUNCTION IF EXISTS f_get_student_attendance_total');
        DB::unprepared('DROP FUNCTION IF EXISTS f_get_attendance_percentage');
        DB::unprepared('DROP FUNCTION IF EXISTS f_get_attendance_symbol');
    }
};