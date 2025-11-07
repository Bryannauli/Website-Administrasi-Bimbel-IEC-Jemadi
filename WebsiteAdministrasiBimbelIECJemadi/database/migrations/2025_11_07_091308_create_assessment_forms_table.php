<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('assessment_forms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_session_id')
                ->constrained('assessment_sessions')
                ->cascadeOnDelete();
            
            $table->foreignId('student_id')
                    ->constrained('students')
                    ->cascadeOnDelete();

            // Skor tiap skill (0–100), nullable karena bisa belum diisi
            $table->unsignedTinyInteger('vocabulary')->nullable();
            $table->unsignedTinyInteger('grammar')->nullable();
            $table->unsignedTinyInteger('listening')->nullable();
            $table->unsignedTinyInteger('speaking')->nullable(); // bisa diisi total dari speaking_tests
            $table->unsignedTinyInteger('reading')->nullable();
            $table->unsignedTinyInteger('spelling')->nullable();

            $table->timestamps();
            $table->unique(['assessment_session_id', 'student_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessment_forms');
    }
};
