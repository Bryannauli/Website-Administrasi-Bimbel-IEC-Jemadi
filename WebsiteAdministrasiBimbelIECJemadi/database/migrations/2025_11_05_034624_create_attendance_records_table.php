<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_records', function (Blueprint $table) {
            $table->id();

            // Relasi ke session absensi
            $table->foreignId('attendance_session_id')
                    ->constrained('attendance_sessions')
                    ->cascadeOnDelete();

            // Relasi ke student
            $table->foreignId('student_id')
                    ->constrained('students')
                    ->cascadeOnDelete();

            // Status kehadiran
            $table->enum('status', [
                'present',      // Hadir
                'absent',       // Absen tanpa alasan
                'late',         // Telat
                'permission',   // Izin
                'sick'          // Sakit
            ]);

            $table->timestamps();
            // Mencegah duplikat student_id untuk attendance_session_id yang sama
            $table->unique(['attendance_session_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_records');
    }
};
