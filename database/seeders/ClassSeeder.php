<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ClassModel;
use App\Models\User;
use App\Models\Schedule;

class ClassSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ambil semua data guru
        $teachers = User::where('role', 'teacher')->get();

        // Pastikan minimal ada 2 guru untuk diacak
        if ($teachers->count() == 0) {
            $this->command->info('Tidak ada guru ditemukan. Jalankan TeacherSeeder dulu!');
            return;
        }

        // --- KELAS 1: Level 5 (Senin & Rabu) ---
        // Skenario: Senin (Form Teacher), Rabu (Local Teacher)
        $t1_form = $teachers->random(); 
        $t1_local = $teachers->where('id', '!=', $t1_form->id)->random() ?? $teachers->random();

        $class1 = ClassModel::create([
            'category'       => 'level',
            'name'           => 'Level 5',
            'classroom'      => 'Italy',
            'form_teacher_id'=> $t1_form->id,
            'local_teacher_id' => $t1_local->id,
            'start_time'     => '09:00:00',
            'end_time'       => '10:30:00',
            'start_month'    => 'July',
            'end_month'      => 'December',
            'academic_year'  => 2025,
            'is_active'      => true,
        ]);
        
        // UPDATE DISINI: Menambahkan teacher_type
        Schedule::create([
            'class_id' => $class1->id, 
            'day_of_week' => 'Monday', 
            'teacher_type' => 'form' // Senin diajar Form Teacher
        ]);
        Schedule::create([
            'class_id' => $class1->id, 
            'day_of_week' => 'Wednesday', 
            'teacher_type' => 'local' // Rabu diajar Local Teacher
        ]);


        // --- KELAS 2: Pre-Level 5 (Selasa & Kamis) ---
        // Skenario: Form Teacher mengajar kedua hari (misal Local Teacher hanya pendamping/jarang)
        $t2_form = $teachers->random();
        $t2_local = $teachers->where('id', '!=', $t2_form->id)->random();

        $class2 = ClassModel::create([
            'category'       => 'pre_level',
            'name'           => 'Pre-Level 5',
            'classroom'      => 'France',
            'form_teacher_id'=> $t2_form->id,
            'local_teacher_id' => $t2_local->id,
            'start_time'     => '16:00:00',
            'end_time'       => '17:30:00',
            'start_month'    => 'July',
            'end_month'      => 'December',
            'academic_year'  => 2025,
            'is_active'      => true,
        ]);

        Schedule::create([
            'class_id' => $class2->id, 
            'day_of_week' => 'Tuesday', 
            'teacher_type' => 'form'
        ]);
        Schedule::create([
            'class_id' => $class2->id, 
            'day_of_week' => 'Thursday', 
            'teacher_type' => 'local' // Ganti ke local agar variatif
        ]);


        // --- KELAS 3: Step 2 (Jumat & Sabtu) ---
        // Skenario: HANYA punya Form Teacher (Local Teacher null)
        // Maka jadwalnya WAJIB 'form' semua.
        $t3_form = $teachers->random();
        
        $class3 = ClassModel::create([
            'category'       => 'step',
            'name'           => 'Step 2',
            'classroom'      => 'China',
            'form_teacher_id'=> $t3_form->id,
            'local_teacher_id' => null, // Tidak ada local teacher
            'start_time'     => '16:00:00',
            'end_time'       => '18:00:00',
            'start_month'    => 'January',
            'end_month'      => 'June',
            'academic_year'  => 2025,
            'is_active'      => true,
        ]);

        Schedule::create([
            'class_id' => $class3->id, 
            'day_of_week' => 'Friday', 
            'teacher_type' => 'form'
        ]);
        Schedule::create([
            'class_id' => $class3->id, 
            'day_of_week' => 'Saturday', 
            'teacher_type' => 'form'
        ]);
    }
}