<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassModel; 
use App\Models\Schedule; 
use App\Models\User; // Import Model User untuk ambil data guru
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

class AdminClassController extends Controller
{
    /**
     * Menampilkan daftar kelas dari Database
     */
    public function index(Request $request)
    {
        // 1. Load Data Kelas beserta relasinya (Guru & Jadwal)
        $query = ClassModel::with(['formTeacher', 'localTeacher', 'schedules']);

        // 2. Logika Search 
        if ($request->has('search') && $request->search != '') {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('classroom', 'LIKE', "%{$searchTerm}%");
            });
        }
        
        // --- LOGIKA FILTER BARU ---
        // 3. Filter Academic Year
        if ($request->filled('academic_year')) {
            $query->where('academic_year', $request->academic_year);
        }

        // 4. Filter Category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        
        // 5. Filter Status (BARU)
        if (!$request->has('status')) {
            // Default behavior: Tampilkan hanya Active
            $query->where('is_active', true);
        } elseif ($request->filled('status')) {
            // Jika ada parameter status dan tidak kosong
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }
        
        // --- LOGIKA SORT ---
        $sort = $request->query('sort', 'newest'); 
        switch ($sort) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        // Ambil daftar kategori unik yang ada di database
        $categories = ['pre_level', 'level', 'step', 'private'];

        // Ambil daftar tahun akademik unik yang ada di database
        $years = ClassModel::select('academic_year')->distinct()->pluck('academic_year')->sortDesc();

        // 6. Pagination
        $classes = $query->paginate(10);
        $classes->appends($request->all());

        // 7. Ambil List Guru untuk Dropdown
        $teachers = User::where('is_teacher', true)->orderBy('name', 'asc')->get();

        // 8. Kirim data ke View
        return view('admin.classes.class', compact(
            'classes', 
            'teachers', 
            'years', 
            'categories'
        ));
    }

    /**
     * Menyimpan Data Kelas Baru (Create)
     */
    public function store(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'category' => 'required|string',
            'name' => 'required|string|max:100',
            'classroom' => 'required|string|max:50',
            'start_month' => 'required|string',
            'end_month' => 'required|string',
            'academic_year' => 'required',
            'form_teacher_id' => 'nullable|exists:users,id',
            'local_teacher_id' => 'nullable|exists:users,id',
            'start_time' => 'required', 
            'end_time' => 'required',
            'days' => 'required|array',
            'days.*' => 'string|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            // Validasi teacher_types (opsional tapi disarankan)
            'teacher_types' => 'nullable|array',
        ]);

        // 2. Simpan Data Kelas Utama
        $class = ClassModel::create([
            'category' => $request->category,
            'name' => $request->name,
            'classroom' => $request->classroom,
            'start_month' => $request->start_month,
            'end_month' => $request->end_month,
            'academic_year' => $request->academic_year,
            'form_teacher_id' => $request->form_teacher_id,
            'local_teacher_id' => $request->local_teacher_id,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'is_active' => true,
        ]);

        // 3. Simpan Jadwal Hari ke Tabel Schedules beserta Tipe Guru
        // Ambil data teacher_types dari request, default array kosong jika tidak ada
        $teacherTypes = $request->input('teacher_types', []);

        foreach ($request->days as $day) {
            // Cek tipe guru untuk hari tersebut (default 'form')
            $type = $teacherTypes[$day] ?? 'form';

            Schedule::create([
                'class_id' => $class->id,
                'day_of_week' => $day,
                'teacher_type' => $type, // Simpan ke database
            ]);
        }

        return redirect()->route('admin.classes.index')->with('success', 'Class created successfully!');
    }

    /**
     * Update Data Kelas (Edit)
     */
    public function update(Request $request, $id)
    {
        $class = ClassModel::findOrFail($id);

        try {
            // Validasi
            $request->validate([
                'name' => 'required|string|max:100',
                'classroom' => 'required|string|max:50',
                'form_teacher_id' => 'nullable|exists:users,id',
                'local_teacher_id' => 'nullable|exists:users,id',
                'days' => 'nullable|array',
                'category' => 'required|string',
                'start_month' => 'required|string',
                'end_month' => 'required|string',
                'academic_year' => 'required',
                'teacher_types' => 'nullable|array',
            ]);
        
            // Update Data Utama
            $class->update([
                'category' => $request->category,
                'name' => $request->name,
                'classroom' => $request->classroom,
                'start_month' => $request->start_month,
                'end_month' => $request->end_month,
                'academic_year' => $request->academic_year,
                'form_teacher_id' => $request->form_teacher_id,
                'local_teacher_id' => $request->local_teacher_id,
                'start_time' => $request->start_time ?? $class->start_time,
                'end_time' => $request->end_time ?? $class->end_time,
                'is_active' => $request->status == 'active' ? true : false,
            ]);
        
            // Sync Jadwal Hari: Hapus yang lama, insert yang baru
            $class->schedules()->delete(); 
        
            if ($request->has('days')) {
                $teacherTypes = $request->input('teacher_types', []);

                foreach ($request->days as $day) {
                    $type = $teacherTypes[$day] ?? 'form';
                    
                    Schedule::create([
                        'class_id' => $class->id,
                        'day_of_week' => $day,
                        'teacher_type' => $type, // Simpan ke database
                    ]);
                }
            }
        
            return redirect()->route('admin.classes.index')->with('success', 'Class updated successfully!');

        } catch (ValidationException $e) {
            return back()
                ->withErrors($e->errors())
                ->withInput($request->all() + ['id' => $id]) 
                ->with('edit_failed', true); 
                
        } catch (\Throwable $e) {
            return back()
                ->withInput($request->all() + ['id' => $id])
                ->with('error', 'Update failed: ' . $e->getMessage())
                ->with('edit_failed', true);
        }
    }
    
    public function detailClass(Request $request, $id)
    {
        // 1. Load Data Kelas
        $class = ClassModel::with(['schedules', 'formTeacher', 'localTeacher', 'students'])
            ->findOrFail($id);
        
        $class->students_count = $class->students->count();

        // 2. Logic Enroll (Available Students)
        $query = \App\Models\Student::where('is_active', true)
                        ->whereNull('class_id')
                        ->orderBy('name', 'asc');

        if ($request->filled('search_student')) {
            $search = $request->search_student;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                ->orWhere('student_number', 'LIKE', "%{$search}%");
            });
        }
        $availableStudents = $query->get();

        // 3. History Sesi (Columns)
        $teachingLogs = \App\Models\AttendanceSession::where('class_id', $id)
            ->with(['teacherRecords.teacher', 'records'])
            ->orderBy('date', 'desc')
            ->get();

        $lastSession = $teachingLogs->first();

        // 4. Panggil Stored Procedure untuk mengambil data statistik
        $studentStats = DB::select('CALL p_get_class_attendance_stats(?)', [$id]);
        
        $attendanceMatrix = []; 
        foreach ($teachingLogs as $session) {
            foreach ($session->records as $record) {
                $attendanceMatrix[$record->student_id][$session->id] = $record->status;
            }
        }

        // ==========================================
        // PERBAIKAN: DEFINISIKAN DATA PENDUKUNG FORM
        // ==========================================
        // Data ini diperlukan untuk modal edit kelas di halaman detail
        $categories = ['pre_level', 'level', 'step', 'private'];
        $years = ClassModel::select('academic_year')->distinct()->pluck('academic_year')->sortDesc();
        $teachers = User::where('is_teacher', true)->orderBy('name', 'asc')->get();

        return view('admin.classes.detail-class', compact(
            'class', 
            'availableStudents', 
            'teachingLogs', 
            'lastSession',
            'studentStats',
            'attendanceMatrix',
            'categories', // Sekarang variabel ini sudah didefinisikan
            'years',      // Sekarang variabel ini sudah didefinisikan
            'teachers'    // Sekarang variabel ini sudah didefinisikan
        ));
    }

    public function toggleStatus($id)
    {
        $class = ClassModel::findOrFail($id);
        
        // Toggle status: jika true jadi false, jika false jadi true
        $class->update([
            'is_active' => !$class->is_active
        ]);

        $statusMessage = $class->is_active ? 'activated' : 'deactivated';

        return back()->with('success', "Class successfully {$statusMessage}!");
    }

    public function delete($id)
        {
            try {
                // Cari kelas
                $class = ClassModel::findOrFail($id);

                // Soft Delete (Data disembunyikan, tidak hilang dari DB)
                $class->delete(); 
                
                // Opsional: Jika ingin menghapus jadwalnya juga secara soft delete
                // $class->schedules()->delete();

                return redirect()->route('admin.classes.index')
                    ->with('success', 'Class moved to trash successfully.');
                    
            } catch (\Exception $e) {
                return back()->with('error', 'Failed to delete class: ' . $e->getMessage());
            }
        }

    public function assignStudent(Request $request, $classId)
    {
        // Validasi input berupa ARRAY student_ids
        $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:students,id'
        ]);

        // Lakukan update massal
        // Kita update semua siswa yang ID-nya ada di dalam array yang dikirim
        Student::whereIn('id', $request->student_ids)->update(['class_id' => $classId]);

        $count = count($request->student_ids);
        return back()->with('success', "Successfully enrolled {$count} students to this class.");
    }

    // METHOD BARU: Keluarkan Murid dari Kelas
    public function unassignStudent($studentId)
    {
        $student = Student::findOrFail($studentId);
        
        // Set class_id jadi NULL
        $student->update(['class_id' => null]);

        return back()->with('success', 'Student removed from class (moved to unassigned).');
    }

    public function assignTeacher(Request $request, $id)
    {
        $request->validate([
            'teacher_id' => 'required|exists:users,id',
            'type' => 'required|in:form,local'
        ]);

        $class = ClassModel::findOrFail($id);
        
        // Tentukan kolom mana yang diupdate berdasarkan input 'type'
        $column = ($request->type === 'form') ? 'form_teacher_id' : 'local_teacher_id';
        
        $class->update([
            $column => $request->teacher_id
        ]);

        return back()->with('success', 'Teacher assigned successfully!');
    }

    public function unassignTeacher($classId, $type)
    {
        $class = ClassModel::findOrFail($classId);

        if ($type === 'form') {
            $class->update(['form_teacher_id' => null]);
        } elseif ($type === 'local') {
            $class->update(['local_teacher_id' => null]);
        }

        return back()->with('success', ucfirst($type) . ' Teacher has been unassigned.');
    }
}