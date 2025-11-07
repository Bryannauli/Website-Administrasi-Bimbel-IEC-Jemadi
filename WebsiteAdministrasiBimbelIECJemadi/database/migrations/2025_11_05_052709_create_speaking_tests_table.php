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
            $table->foreignId('class_id')
                ->nullable()
                ->constrained('classes')
                ->nullOnDelete();

            $table->enum('type', ['mid', 'final']);
            $table->date('date');
            $table->string('topic', 200)->nullable();

            // Interviewer (guru)
            $table->foreignId('interviewer_id')
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete();

            $table->timestamps();
            $table->unique(['class_id', 'type']);
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
