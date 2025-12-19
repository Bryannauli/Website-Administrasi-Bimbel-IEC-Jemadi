<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // =========================================================================
        // A. VALIDATION TRIGGERS (DATA INTEGRITY: NILAI SISWA)
        // =========================================================================
        
        // 1. Logic Validasi Assessment Forms (Range: 0-100)
        // [UPDATED] Spelling dicek hanya jika TIDAK NULL
        $validateAssessmentSql = '
            BEGIN
                IF NEW.vocabulary < 0 OR NEW.vocabulary > 100 THEN
                    SIGNAL SQLSTATE "45000" SET MESSAGE_TEXT = "Validation Error: Vocabulary score must be between 0 and 100";
                END IF;
                IF NEW.grammar < 0 OR NEW.grammar > 100 THEN
                    SIGNAL SQLSTATE "45000" SET MESSAGE_TEXT = "Validation Error: Grammar score must be between 0 and 100";
                END IF;
                IF NEW.listening < 0 OR NEW.listening > 100 THEN
                    SIGNAL SQLSTATE "45000" SET MESSAGE_TEXT = "Validation Error: Listening score must be between 0 and 100";
                END IF;
                IF NEW.reading < 0 OR NEW.reading > 100 THEN
                    SIGNAL SQLSTATE "45000" SET MESSAGE_TEXT = "Validation Error: Reading score must be between 0 and 100";
                END IF;
                -- Cek Spelling hanya jika ada isinya
                IF NEW.spelling IS NOT NULL AND (NEW.spelling < 0 OR NEW.spelling > 100) THEN
                    SIGNAL SQLSTATE "45000" SET MESSAGE_TEXT = "Validation Error: Spelling score must be between 0 and 100";
                END IF;
            END
        ';

        // Terapkan Trigger Assessment (Insert & Update)
        DB::unprepared("DROP TRIGGER IF EXISTS tr_val_assessment_insert");
        DB::unprepared("CREATE TRIGGER tr_val_assessment_insert BEFORE INSERT ON assessment_forms FOR EACH ROW $validateAssessmentSql");

        DB::unprepared("DROP TRIGGER IF EXISTS tr_val_assessment_update");
        DB::unprepared("CREATE TRIGGER tr_val_assessment_update BEFORE UPDATE ON assessment_forms FOR EACH ROW $validateAssessmentSql");


        // 2. Logic Validasi Speaking Results (Range: 0-50)
        $validateSpeakingSql = '
            BEGIN
                IF NEW.content_score < 0 OR NEW.content_score > 50 THEN
                    SIGNAL SQLSTATE "45000" SET MESSAGE_TEXT = "Validation Error: Speaking Content score must be between 0 and 50";
                END IF;
                IF NEW.participation_score < 0 OR NEW.participation_score > 50 THEN
                    SIGNAL SQLSTATE "45000" SET MESSAGE_TEXT = "Validation Error: Speaking Participation score must be between 0 and 50";
                END IF;
            END
        ';

        // Terapkan Trigger Speaking (Insert & Update)
        DB::unprepared("DROP TRIGGER IF EXISTS tr_val_speaking_insert");
        DB::unprepared("CREATE TRIGGER tr_val_speaking_insert BEFORE INSERT ON speaking_test_results FOR EACH ROW $validateSpeakingSql");

        DB::unprepared("DROP TRIGGER IF EXISTS tr_val_speaking_update");
        DB::unprepared("CREATE TRIGGER tr_val_speaking_update BEFORE UPDATE ON speaking_test_results FOR EACH ROW $validateSpeakingSql");


        // =========================================================================
        // B. SESSION INTEGRITY TRIGGERS (MENCEGAH DUPLIKASI SESI)
        // =========================================================================

        // 3. Prevent Insert: Mencegah pembuatan sesi kelas duplikat di tanggal sama
        DB::unprepared("DROP TRIGGER IF EXISTS tr_prevent_duplicate_session_insert");
        DB::unprepared("
            CREATE TRIGGER tr_prevent_duplicate_session_insert
            BEFORE INSERT ON class_sessions
            FOR EACH ROW
            BEGIN
                IF EXISTS (
                    SELECT 1 
                    FROM class_sessions 
                    WHERE class_id = NEW.class_id 
                        AND date = NEW.date
                ) THEN
                    SIGNAL SQLSTATE '45000'
                    SET MESSAGE_TEXT = 'Database Error: A session for this class already exists on this date.';
                END IF;
            END
        ");

        // 4. Prevent Update: Mencegah pemindahan tanggal sesi ke tanggal yang sudah ada
        DB::unprepared("DROP TRIGGER IF EXISTS tr_prevent_duplicate_session_update");
        DB::unprepared("
            CREATE TRIGGER tr_prevent_duplicate_session_update
            BEFORE UPDATE ON class_sessions
            FOR EACH ROW
            BEGIN
                -- Hanya cek jika tanggal berubah
                IF NEW.date != OLD.date THEN
                    IF EXISTS (
                        SELECT 1 
                        FROM class_sessions 
                        WHERE class_id = NEW.class_id 
                            AND date = NEW.date
                            AND id != NEW.id -- Kecuali diri sendiri
                    ) THEN
                        SIGNAL SQLSTATE '45000'
                        SET MESSAGE_TEXT = 'Database Error: Cannot move session. Another session already exists on the target date.';
                    END IF;
                END IF;
            END
        ");

        // =========================================================================
        // C. WORKFLOW INTEGRITY (MENCEGAH SUBMIT JIKA DATA 0)
        // =========================================================================
        
        // 5. Mencegah status berubah ke 'submitted' jika BELUM ADA SATUPUN siswa yang lengkap
        // Ini adalah benteng terakhir. Validasi detail per siswa dilakukan di Stored Procedure.
        // Trigger ini hanya mencegah submit "Blank Form".
        DB::unprepared("DROP TRIGGER IF EXISTS tr_prevent_premature_submission");
        DB::unprepared("
            CREATE TRIGGER tr_prevent_premature_submission
            BEFORE UPDATE ON assessment_sessions
            FOR EACH ROW
            BEGIN
                DECLARE complete_count INT DEFAULT 0;

                -- Hanya cek jika status berubah menjadi 'submitted'
                IF NEW.status = 'submitted' AND OLD.status != 'submitted' THEN
                    
                    -- [UPDATED] Query disesuaikan dengan struktur baru
                    SELECT COUNT(*) INTO complete_count
                    FROM assessment_forms af
                    -- Join ke speaking results untuk memastikan speaking juga ada
                    INNER JOIN speaking_test_results str 
                        ON str.assessment_session_id = NEW.id 
                        AND str.student_id = af.student_id
                    WHERE af.assessment_session_id = NEW.id
                        AND af.vocabulary IS NOT NULL
                        AND af.grammar IS NOT NULL
                        AND af.listening IS NOT NULL
                        AND af.reading IS NOT NULL
                        -- Speaking components check
                        AND str.content_score IS NOT NULL
                        AND str.participation_score IS NOT NULL;
                        -- Note: Spelling TIDAK dicek disini karena opsional

                    -- Jika tidak ada satupun siswa yang datanya lengkap, tolak update
                    IF complete_count = 0 THEN
                        SIGNAL SQLSTATE '45000' 
                        SET MESSAGE_TEXT = 'Data Integrity Violation: Cannot submit assessment. At least one student must have complete grades (Written & Speaking).';
                    END IF;
                END IF;
            END
        ");

        // =========================================================================
        // D. AUTOMATIC SYNC: SPEAKING TOTAL (BARU & PENTING)
        // Deskripsi: Menjumlahkan Content + Participation secara otomatis ke tabel assessment_forms
        // =========================================================================
        
        $syncSpeakingSql = "
            BEGIN
                UPDATE assessment_forms 
                SET speaking = IFNULL(NEW.content_score, 0) + IFNULL(NEW.participation_score, 0)
                WHERE assessment_session_id = NEW.assessment_session_id 
                    AND student_id = NEW.student_id;
            END
        ";

        DB::unprepared("DROP TRIGGER IF EXISTS tr_sync_speaking_total_insert");
        DB::unprepared("CREATE TRIGGER tr_sync_speaking_total_insert AFTER INSERT ON speaking_test_results FOR EACH ROW $syncSpeakingSql");

        DB::unprepared("DROP TRIGGER IF EXISTS tr_sync_speaking_total_update");
        DB::unprepared("CREATE TRIGGER tr_sync_speaking_total_update AFTER UPDATE ON speaking_test_results FOR EACH ROW $syncSpeakingSql");
    }

    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS tr_val_assessment_insert');
        DB::unprepared('DROP TRIGGER IF EXISTS tr_val_assessment_update');
        DB::unprepared('DROP TRIGGER IF EXISTS tr_val_speaking_insert');
        DB::unprepared('DROP TRIGGER IF EXISTS tr_val_speaking_update');
        DB::unprepared('DROP TRIGGER IF EXISTS tr_prevent_duplicate_session_insert');
        DB::unprepared('DROP TRIGGER IF EXISTS tr_prevent_duplicate_session_update');
        DB::unprepared('DROP TRIGGER IF EXISTS tr_prevent_premature_submission');
        DB::unprepared('DROP TRIGGER IF EXISTS tr_sync_speaking_total_insert');
        DB::unprepared('DROP TRIGGER IF EXISTS tr_sync_speaking_total_update');
    }
};