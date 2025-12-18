<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin default
        User::create([
            'username' => 'admin',
            'name' => 'Ms. Fita',
            'email' => null,
            'phone' => null,
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'is_teacher' => true,
            'is_active' => true,
        ]);

        // Akun tes teacher
        User::create([
            'username' => 'teacher',
            'name' => 'Ms. Valerine',
            'email' => null,
            'phone' => null,
            'password' => Hash::make('password'),
            'role' => 'teacher',
            'is_teacher' => true,
            'is_active' => true,
        ]);

        // Guru 1: Form Teacher Senior
        User::create([
            'username'   => 'teacher1',
            'name'       => 'Mr. Richard',
            'email'      => null,
            'phone'      => null,
            'password'   => Hash::make('password'),
            'role'       => 'teacher',
            'is_teacher' => true,
            'is_active'  => true,
        ]);

        User::create([
            'username'   => 'teacher2',
            'name'       => 'Mr. Jimmy',
            'email'      => null,
            'phone'      => null,
            'password'   => Hash::make('password'),
            'role'       => 'teacher',
            'is_teacher' => true,
            'is_active'  => true,
        ]);

        User::create([
            'username'   => 'teacher3',
            'name'       => 'Ms. Angeline',
            'email'      => null,
            'phone'      => null,
            'password'   => Hash::make('password'),
            'role'       => 'teacher',
            'is_teacher' => true,
            'is_active'  => true,
        ]);
    }
}