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

        // 3. Prevent Insert: Mencegah pembuatan sesi baru jika tanggal & kelas sudah ada
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
    }

    public function down(): void
    {
        // 1. Drop Validation Triggers
        DB::unprepared('DROP TRIGGER IF EXISTS tr_val_assessment_insert');
        DB::unprepared('DROP TRIGGER IF EXISTS tr_val_assessment_update');
        DB::unprepared('DROP TRIGGER IF EXISTS tr_val_speaking_insert');
        DB::unprepared('DROP TRIGGER IF EXISTS tr_val_speaking_update');

        // 2. Drop Session Integrity Triggers
        DB::unprepared('DROP TRIGGER IF EXISTS tr_prevent_duplicate_session_insert');
        DB::unprepared('DROP TRIGGER IF EXISTS tr_prevent_duplicate_session_update');
    }
};