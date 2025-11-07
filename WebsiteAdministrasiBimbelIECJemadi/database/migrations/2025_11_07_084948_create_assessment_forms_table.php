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
            $table->foreignId('class_id')
                ->nullable()
                ->constrained('classes')
                ->nullOnDelete();
            
            $table->foreignId('student_id')
                    ->constrained('students')
                    ->cascadeOnDelete();

            $table->enum('type', ['mid', 'final']);
            $table->date('date');

            // Skor tiap skill (0–100), nullable karena bisa belum diisi
            $table->unsignedTinyInteger('vocabulary')->nullable();
            $table->unsignedTinyInteger('grammar')->nullable();
            $table->unsignedTinyInteger('listening')->nullable();
            $table->unsignedTinyInteger('speaking')->nullable(); // bisa diisi total dari speaking_tests
            $table->unsignedTinyInteger('reading')->nullable();
            $table->unsignedTinyInteger('spelling')->nullable();

            $table->timestamps();
            $table->unique(['class_id', 'student_id', 'type']);
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
