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
        Schema::create('speaking_tests', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('assessment_session_id')
                ->constrained('assessment_sessions')
                ->cascadeOnDelete()
                ->unique();

            $table->date('date');
            $table->string('topic', 200)->nullable();

            // Interviewer (guru)
            $table->foreignId('interviewer_id')
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('speaking_tests');
    }
};
