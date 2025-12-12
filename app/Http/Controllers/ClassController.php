<?php

namespace App\Http\Controllers;

use App\Models\ClassModel; 
use App\Models\Schedule; 
use App\Models\User; // Import Model User untuk ambil data guru
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class ClassController extends Controller
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
            
            // Guru bersifat Opsional (Nullable)
            'form_teacher_id' => 'nullable|exists:users,id',
            'local_teacher_id' => 'nullable|exists:users,id',
            
            'start_time' => 'required', 
            'end_time' => 'required',
            
            // Validasi Array Hari (Checkbox)
            'days' => 'required|array',
            'days.*' => 'string|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
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
            'is_active' => true, // Default Active
        ]);

        // 3. Simpan Jadwal Hari ke Tabel Schedules
        foreach ($request->days as $day) {
            Schedule::create([
                'class_id' => $class->id,
                'day_of_week' => $day,
            ]);
        }

        return redirect()->route('admin.classes.index')->with('success', 'Class updated successfully!');
    }

    /**
     * Update Data Kelas (Edit)
     */
    public function update(Request $request, $id)
    {
        $class = ClassModel::findOrFail($id);

        try {
            // Validasi
            $data = $request->validate([
                'name' => 'required|string|max:100',
                'classroom' => 'required|string|max:50',
                'form_teacher_id' => 'nullable|exists:users,id',
                'local_teacher_id' => 'nullable|exists:users,id',
                'days' => 'nullable|array',
                'category' => 'required|string',
                'start_month' => 'required|string',
                'end_month' => 'required|string',
                'academic_year' => 'required',
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
                
                // Handle jika nama field di form beda (time_start vs start_time)
                'start_time' => $request->start_time ?? $class->start_time,
                'end_time' => $request->end_time ?? $class->end_time,
                
                // Perbaikan kecil: Pastikan 'is_active' dihandle dari request,
                // atau gunakan nilai yang sudah ada jika tidak ada di request.
                'is_active' => $request->status == 'active' ? true : false,
            ]);
        
            // Sync Jadwal Hari: Hapus yang lama, insert yang baru
            $class->schedules()->delete(); 
        
            if ($request->has('days')) {
                foreach ($request->days as $day) {
                    Schedule::create([
                        'class_id' => $class->id,
                        'day_of_week' => $day,
                    ]);
                }
            }
        
            // Perbaikan: Ganti pesan success
            return redirect()->route('admin.classes.index')->with('success', 'Class updated successfully!');

        } catch (ValidationException $e) {
            // TANGKAP KEGAGALAN VALIDASI
            return back()
                ->withErrors($e->errors())
                // Penting: Kirim 'id' lama agar Alpine JS bisa menyusun updateUrl
                ->withInput($request->all() + ['id' => $id]) 
                ->with('edit_failed', true); // FLAG UTAMA UNTUK EDIT MODAL
                
        } catch (\Throwable $e) {
            // Tangani kegagalan lainnya
            Log::error('Class update failed: '.$e->getMessage());
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
        // Ambil 15 sesi terakhir agar tabel tidak terlalu lebar (bisa disesuaikan)
        $teachingLogs = \App\Models\AttendanceSession::where('class_id', $id)
            ->with(['teacherRecords.teacher', 'records'])
            ->orderBy('date', 'desc')
            ->limit(15) 
            ->get();

        $lastSession = $teachingLogs->first();

        // 4. Buat Matrix Absensi & Statistik
        $studentStats = [];
        $attendanceMatrix = []; // Array untuk lookup cepat: [student_id][session_id] = status

        // Pre-fill Matrix
        foreach ($teachingLogs as $session) {
            foreach ($session->records as $record) {
                $attendanceMatrix[$record->student_id][$session->id] = $record->status;
            }
        }
        
        foreach ($class->students as $student) {
            // Hitung statistik ringkas untuk kolom Total %
            // Kita hitung manual dari data matrix/teachingLogs yang ada agar sinkron
            $stats = ['present' => 0, 'total' => 0];

            foreach ($teachingLogs as $session) {
                $status = $attendanceMatrix[$student->id][$session->id] ?? null;
                if ($status) {
                    $stats['total']++;
                    if (in_array($status, ['present', 'late'])) {
                        $stats['present']++;
                    }
                }
            }

            $percentage = $stats['total'] > 0 
                ? round(($stats['present'] / $stats['total']) * 100) 
                : 0;

            $studentStats[] = (object) [
                'id' => $student->id, // PENTING: ID diperlukan untuk kunci Matrix
                'name' => $student->name,
                'student_number' => $student->student_number,
                'percentage' => $percentage
            ];
        }

        // Sortir berdasarkan persentase kehadiran terendah
        $studentStats = collect($studentStats)->sortBy('percentage')->values();

        return view('admin.classes.detail-class', compact(
            'class', 
            'availableStudents', 
            'teachingLogs', 
            'lastSession',
            'studentStats',
            'attendanceMatrix' // Kirim matrix ke view
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
}