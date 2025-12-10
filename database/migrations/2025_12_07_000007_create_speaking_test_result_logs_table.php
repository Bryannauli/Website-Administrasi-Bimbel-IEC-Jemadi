<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('speaking_test_result_logs', function (Blueprint $table) {
            $table->id();

            // Target: Hasil Nilai (SET NULL)
            $table->foreignId('speaking_test_result_id')
                    ->nullable()
                    ->constrained('speaking_test_results')
                    ->nullOnDelete();

            // Actor
            $table->foreignId('user_id')
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete();

            $table->string('action');
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('speaking_test_result_logs');
    }
};