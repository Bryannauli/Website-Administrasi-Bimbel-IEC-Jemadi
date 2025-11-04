<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

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
            'email' => 'admin@example.com',
            'phone' => '08123456789',
            'password' => bcrypt('admin123'),
            'role' => 'admin',
            'status' => 'active',
        ]);
    }
}
