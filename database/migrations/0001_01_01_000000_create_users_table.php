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
        // =========================
        // Table: users
        // =========================
        DB::unprepared("
            CREATE TABLE users (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(255) NOT NULL UNIQUE,
                name VARCHAR(255) NOT NULL,

                email VARCHAR(255) UNIQUE NULL,
                phone VARCHAR(255) UNIQUE NULL,
                address VARCHAR(255) NULL,
                email_verified_at TIMESTAMP NULL,

                password VARCHAR(255) NOT NULL,
                role ENUM('admin', 'teacher') NOT NULL,
                is_teacher TINYINT(1) NOT NULL DEFAULT 0,
                is_active TINYINT(1) NOT NULL DEFAULT 1,

                remember_token VARCHAR(100) NULL,

                created_at TIMESTAMP NULL DEFAULT NULL,
                updated_at TIMESTAMP NULL DEFAULT NULL,
                deleted_at TIMESTAMP NULL DEFAULT NULL
            ) ENGINE=InnoDB
            DEFAULT CHARSET=utf8mb4
            COLLATE=utf8mb4_unicode_ci;
        ");

        // =========================
        // Table: password_reset_tokens
        // =========================
        DB::unprepared("
            CREATE TABLE password_reset_tokens (
                email VARCHAR(255) NOT NULL PRIMARY KEY,
                token VARCHAR(255) NOT NULL,
                created_at TIMESTAMP NULL DEFAULT NULL
            ) ENGINE=InnoDB
            DEFAULT CHARSET=utf8mb4
            COLLATE=utf8mb4_unicode_ci;
        ");

        // =========================
        // Table: sessions
        // =========================
        DB::unprepared("
            CREATE TABLE sessions (
                id VARCHAR(255) NOT NULL PRIMARY KEY,
                user_id BIGINT UNSIGNED NULL,
                ip_address VARCHAR(45) NULL,
                user_agent TEXT NULL,
                payload LONGTEXT NOT NULL,
                last_activity INT NOT NULL,

                INDEX idx_sessions_user_id (user_id),
                INDEX idx_sessions_last_activity (last_activity),

                CONSTRAINT fk_sessions_user
                    FOREIGN KEY (user_id)
                    REFERENCES users(id)
                    ON DELETE CASCADE
                    ON UPDATE CASCADE
            ) ENGINE=InnoDB
            DEFAULT CHARSET=utf8mb4
            COLLATE=utf8mb4_unicode_ci;
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("DROP TABLE IF EXISTS sessions;");
        DB::unprepared("DROP TABLE IF EXISTS password_reset_tokens;");
        DB::unprepared("DROP TABLE IF EXISTS users;");
    }
};
