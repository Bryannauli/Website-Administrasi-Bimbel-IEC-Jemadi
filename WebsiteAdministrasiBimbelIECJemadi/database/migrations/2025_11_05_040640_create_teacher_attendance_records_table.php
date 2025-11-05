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
                    ->constrained('users')
                    ->cascadeOnDelete();

            $table->enum('status', [
                'present', 'absent', 'late', 'permission', 'sick', 'substitute'
            ]);

            // catatan aktivitas kelas (matero yang diajarkan)
            $table->text('comment')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teacher_attendance_records');
    }
};
