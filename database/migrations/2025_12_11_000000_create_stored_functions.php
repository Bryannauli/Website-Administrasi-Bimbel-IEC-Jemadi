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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP FUNCTION IF EXISTS f_GetGrade');
        DB::unprepared('DROP FUNCTION IF EXISTS f_CalcAssessmentAvg');
    }
};