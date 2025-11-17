<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Admin default
        User::create([
            'username' => 'admin',
            'name' => 'Administrator',
            'photo' => null,
            'email' => null,
            'phone' => null,
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'is_teacher' => false,
            'status' => 'active',
        ]);
    }
}
