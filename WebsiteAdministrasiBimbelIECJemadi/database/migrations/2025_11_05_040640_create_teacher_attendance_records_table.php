<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teacher_attendance_records', function (Blueprint $table) {
            $table->id();

            $table->foreignId('attendance_session_id')
                    ->constrained('attendance_sessions')
                    ->cascadeOnDelete();

            $table->foreignId('teacher_id')
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete();

            // default untuk form/local, jika ada sub, bisa pakai (+)
            $table->enum('status', [
                'present', 
                'absent', 
                'late', 
                'permission', 
                'sick', 
                'substitute'        // Guru pengganti yang hadir -- atau nanti pakai present saja
            ]);

            // catatan aktivitas kelas (materi yang diajarkan)
            $table->text('comment')->nullable();

            $table->timestamps();
            // Mencegah duplikat teacher_id untuk attendance_session_id yang sama
            $table->unique(
                ['attendance_session_id', 'teacher_id'], 
                'unique_teacher_attendance_records'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teacher_attendance_records');
    }
};
