<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Student;
use App\Models\ClassModel;
use Faker\Factory as Faker;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $classes = ClassModel::all();

        if ($classes->isEmpty()) {
            $this->command->info('Tidak ada kelas ditemukan. Jalankan ClassSeeder dulu!');
            return;
        }

        // Inisialisasi Faker dengan locale Indonesia
        $faker = Faker::create('id_ID');

        foreach ($classes as $class) {
            
            // =================================================================
            // NEW LOGIC: SKIP CLASS YANG DIKHUSUSKAN UNTUK EMPTY ASSESSMENT
            // =================================================================
            if (str_contains($class->name, 'Empty Assessment')) {
                $this->command->warn("Skipping student generation for class: {$class->name} (Reserved for Empty Class Example).");
                continue; // Lompati iterasi ini (tidak ada siswa yang dibuat untuk kelas ini)
            }
            // =================================================================

            // Random jumlah siswa antara 6 sampai 15 per kelas
            $jumlahSiswa = rand(6, 15);

            for ($i = 1; $i <= $jumlahSiswa; $i++) {
                
                $gender = $faker->randomElement(['male', 'female']);
                $name = ($gender == 'male') ? $faker->name('male') : $faker->name('female');

                Student::create([
                    // numerify('#######') menghasilkan 7 digit angka acak (misal: 1928374)
                    'student_number' => $faker->unique()->numerify('#######'),
                    'name'           => $name,
                    'gender'         => $gender,
                    'phone'          => $faker->phoneNumber(),
                    'address'        => $faker->address(),
                    'is_active'      => true,
                    'class_id'       => $class->id,
                ]);
            }
        }
    }
}