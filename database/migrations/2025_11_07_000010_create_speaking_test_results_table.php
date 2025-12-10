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
        Schema::create('speaking_test_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('speaking_test_id')
                ->constrained('speaking_tests')
                ->cascadeOnDelete();

            $table->foreignId('student_id')
                ->constrained('students')
                ->cascadeOnDelete();

            // masing masing skor 0–50, nanti total dihitung manual
            $table->unsignedTinyInteger('content_score')->nullable();
            $table->unsignedTinyInteger('participation_score')->nullable();

            $table->timestamps();
            $table->unique(['speaking_test_id', 'student_id']);
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('speaking_test_results');
    }
};
