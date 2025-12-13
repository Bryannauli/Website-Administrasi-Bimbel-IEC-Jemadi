<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. TRIGGER VALIDASI: ASSESSMENT FORMS (Nilai Tertulis Max 100)
        // Kita butuh 2 event: BEFORE INSERT dan BEFORE UPDATE
        
        $validateAssessmentSql = '
            BEGIN
                -- Cek Vocabulary
                IF NEW.vocabulary < 0 OR NEW.vocabulary > 100 THEN
                    SIGNAL SQLSTATE "45000" 
                    SET MESSAGE_TEXT = "Validation Error: Vocabulary score must be between 0 and 100";
                END IF;

                -- Cek Grammar
                IF NEW.grammar < 0 OR NEW.grammar > 100 THEN
                    SIGNAL SQLSTATE "45000" 
                    SET MESSAGE_TEXT = "Validation Error: Grammar score must be between 0 and 100";
                END IF;

                -- Cek Listening
                IF NEW.listening < 0 OR NEW.listening > 100 THEN
                    SIGNAL SQLSTATE "45000" 
                    SET MESSAGE_TEXT = "Validation Error: Listening score must be between 0 and 100";
                END IF;

                -- Cek Reading
                IF NEW.reading < 0 OR NEW.reading > 100 THEN
                    SIGNAL SQLSTATE "45000" 
                    SET MESSAGE_TEXT = "Validation Error: Reading score must be between 0 and 100";
                END IF;

                -- Cek Spelling (Handle NULL karena nullable)
                IF NEW.spelling IS NOT NULL AND (NEW.spelling < 0 OR NEW.spelling > 100) THEN
                    SIGNAL SQLSTATE "45000" 
                    SET MESSAGE_TEXT = "Validation Error: Spelling score must be between 0 and 100";
                END IF;
            END
        ';

        // Pasang Trigger INSERT
        DB::unprepared("
            DROP TRIGGER IF EXISTS tr_val_assessment_insert;
            CREATE TRIGGER tr_val_assessment_insert
            BEFORE INSERT ON assessment_forms
            FOR EACH ROW
            $validateAssessmentSql
        ");

        // Pasang Trigger UPDATE
        DB::unprepared("
            DROP TRIGGER IF EXISTS tr_val_assessment_update;
            CREATE TRIGGER tr_val_assessment_update
            BEFORE UPDATE ON assessment_forms
            FOR EACH ROW
            $validateAssessmentSql
        ");


        // 2. TRIGGER VALIDASI: SPEAKING RESULTS (Nilai Speaking Max 50)
        
        $validateSpeakingSql = '
            BEGIN
                -- Cek Content
                IF NEW.content_score < 0 OR NEW.content_score > 50 THEN
                    SIGNAL SQLSTATE "45000" 
                    SET MESSAGE_TEXT = "Validation Error: Speaking Content score must be between 0 and 50";
                END IF;

                -- Cek Participation
                IF NEW.participation_score < 0 OR NEW.participation_score > 50 THEN
                    SIGNAL SQLSTATE "45000" 
                    SET MESSAGE_TEXT = "Validation Error: Speaking Participation score must be between 0 and 50";
                END IF;
            END
        ';

        // Pasang Trigger INSERT
        DB::unprepared("
            DROP TRIGGER IF EXISTS tr_val_speaking_insert;
            CREATE TRIGGER tr_val_speaking_insert
            BEFORE INSERT ON speaking_test_results
            FOR EACH ROW
            $validateSpeakingSql
        ");

        // Pasang Trigger UPDATE
        DB::unprepared("
            DROP TRIGGER IF EXISTS tr_val_speaking_update;
            CREATE TRIGGER tr_val_speaking_update
            BEFORE UPDATE ON speaking_test_results
            FOR EACH ROW
            $validateSpeakingSql
        ");
    }

    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS tr_val_assessment_insert');
        DB::unprepared('DROP TRIGGER IF EXISTS tr_val_assessment_update');
        DB::unprepared('DROP TRIGGER IF EXISTS tr_val_speaking_insert');
        DB::unprepared('DROP TRIGGER IF EXISTS tr_val_speaking_update');
    }
};