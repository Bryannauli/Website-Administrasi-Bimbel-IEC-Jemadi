<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('speaking_test_logs', function (Blueprint $table) {
            $table->id();

            // Target: Sesi Speaking (SET NULL agar log aman)
            $table->foreignId('speaking_test_id')
                    ->nullable()
                    ->constrained('speaking_tests')
                    ->nullOnDelete();

            // Actor: User
            $table->foreignId('user_id')
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete();

            $table->string('action'); // CREATE, UPDATE, SOFT_DELETE, etc
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('speaking_test_logs');
    }
};