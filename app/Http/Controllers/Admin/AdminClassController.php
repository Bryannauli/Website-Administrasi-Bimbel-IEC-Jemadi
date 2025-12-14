<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassModel; 
use App\Models\Schedule; 
use App\Models\User; 
use App\Models\Student;
use App\Models\ClassSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator; // <--- Import Validator

class AdminClassController extends Controller
{
    /**
     * Menampilkan daftar kelas
     */
    public function index(Request $request)
    {
        $query = ClassModel::with(['formTeacher', 'localTeacher', 'schedules']);

        // 1. Filter Search Text
        if ($request->has('search') && $request->search != '') {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('classroom', 'LIKE', "%{$searchTerm}%");
            });
        }
        
        // 2. Filters Utama
        if ($request->filled('academic_year')) {
            $query->where('academic_year', $request->academic_year);
        }
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        // [BARU] Filter Specific Class Name
        if ($request->filled('class_name')) {
            $query->where('name', $request->class_name);
        }
        
        // 3. Filter Status
        if (!$request->has('status')) {
            $query->where('is_active', true);
        } elseif ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }
        
        // 4. Sorting
        $sort = $request->query('sort', 'newest'); 
        switch ($sort) {
            case 'oldest': $query->orderBy('created_at', 'asc'); break;
            case 'name_asc': $query->orderBy('name', 'asc'); break;
            case 'name_desc': $query->orderBy('name', 'desc'); break;
            case 'newest': default: $query->orderBy('created_at', 'desc'); break;
        }

        // --- DATA PENDUKUNG DROPDOWN (DINAMIS) ---
        $categories = ['pre_level', 'level', 'step', 'private'];
        $years = ClassModel::select('academic_year')->distinct()->pluck('academic_year')->sortDesc();

        // [LOGIKA BARU] Ambil List Nama Kelas untuk Dropdown
        // Query ini dipengaruhi oleh filter Category & Year yang sedang aktif
        $classNameQuery = ClassModel::select('name')->distinct()->orderBy('name', 'asc');

        if ($request->filled('category')) {
            $classNameQuery->where('category', $request->category);
        }
        if ($request->filled('academic_year')) {
            $classNameQuery->where('academic_year', $request->academic_year);
        }
        
        // Ambil hasil pluck
        $classNames = $classNameQuery->pluck('name'); 

        // Execute Pagination
        $classes = $query->paginate(10);
        $classes->appends($request->all());

        $teachers = User::where('is_teacher', true)->orderBy('name', 'asc')->get();

        // Jangan lupa kirim $classNames ke view
        return view('admin.classes.class', compact('classes', 'teachers', 'years', 'categories', 'classNames'));
    }

    /**
     * Menyimpan Data Kelas Baru
     */
    public function store(Request $request)
    {
        // Validasi untuk Create
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
            'teacher_types' => 'nullable|array',
        ]);

        try {
            // Siapkan JSON Schedule
            $scheduleData = [];
            $teacherTypes = $request->input('teacher_types', []);

            foreach ($request->days as $day) {
                $scheduleData[] = [
                    'day' => $day,
                    'type' => $teacherTypes[$day] ?? 'form'
                ];
            }
            
            $jsonSchedule = json_encode($scheduleData); 

            // Panggil Stored Procedure
            DB::statement('SET @newClassId = 0');
            DB::statement('CALL p_CreateClass(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, @newClassId)', [
                $request->category,
                $request->name,
                $request->classroom,
                $request->start_month,
                $request->end_month,
                $request->academic_year,
                $request->form_teacher_id,
                $request->local_teacher_id,
                $request->start_time,
                $request->end_time,
                $jsonSchedule 
            ]);

            $result = DB::select('SELECT @newClassId AS id');
            $newClassId = $result[0]->id; 

            return redirect()->route('admin.classes.detailclass', $newClassId)
                                ->with('success', 'Class created successfully!');

        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Failed to create class: ' . $e->getMessage());
        }
    }

    /**
     * Update Data Kelas (REFACTORED: Menggunakan Validator Manual)
     */
    public function update(Request $request, $id)
    {
        $class = ClassModel::findOrFail($id);

        // 1. Definisikan Validator
        $validator = Validator::make($request->all(), [
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

        // 2. Cek apakah Validasi Gagal?
        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                // Kirim input lama + ID agar modal edit tahu siapa yang diedit
                ->withInput($request->all() + ['id' => $id]) 
                // Flag untuk membuka modal edit di frontend
                ->with('edit_failed', true);
        }

        try {
            // 3. Update Data Utama
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
        
            // 4. Sync Jadwal Hari
            $class->schedules()->delete(); 
        
            if ($request->has('days')) {
                $teacherTypes = $request->input('teacher_types', []);

                foreach ($request->days as $day) {
                    $type = $teacherTypes[$day] ?? 'form';
                    
                    Schedule::create([
                        'class_id' => $class->id,
                        'day_of_week' => $day,
                        'teacher_type' => $type, 
                    ]);
                }
            }
        
            return redirect()->route('admin.classes.index')->with('success', 'Class updated successfully!');

        } catch (\Throwable $e) {
            return back()
                ->withInput($request->all() + ['id' => $id])
                ->with('error', 'Update failed: ' . $e->getMessage())
                ->with('edit_failed', true);
        }
    }

    public function detailClass(Request $request, $id)
    {
        $class = ClassModel::with(['schedules', 'formTeacher', 'localTeacher', 'students', 'assessmentSessions'])->findOrFail($id);
        $class->students_count = $class->students->count();

        $query = \App\Models\Student::where('is_active', true)->whereNull('class_id')->orderBy('name', 'asc');
        if ($request->filled('search_student')) {
            $search = $request->search_student;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")->orWhere('student_number', 'LIKE', "%{$search}%");
            });
        }
        $availableStudents = $query->get();

        $teachingLogs = DB::table('v_class_activity_logs')->where('class_id', $id)->orderBy('date', 'asc')->get();

        $lastSession = ClassSession::where('class_id', $id)->with('teacher')->orderBy('date', 'desc')->orderBy('created_at', 'desc')->first();
            
        $studentStats = DB::select('CALL p_get_class_attendance_stats(?)', [$id]);
        
        $rawLogs = ClassSession::where('class_id', $id)->with(['records:id,class_session_id,student_id,status'])->get();
        $attendanceMatrix = []; 
        foreach ($rawLogs as $session) {
            foreach ($session->records as $record) {
                $attendanceMatrix[$record->student_id][$session->id] = $record->status;
            }
        }

        // Data pendukung form edit di detail class
        $categories = ['pre_level', 'level', 'step', 'private'];
        $years = ClassModel::select('academic_year')->distinct()->pluck('academic_year')->sortDesc();
        $teachers = User::where('is_teacher', true)->orderBy('name', 'asc')->get();

        return view('admin.classes.detail-class', compact(
            'class', 'availableStudents', 'teachingLogs', 'lastSession',
            'studentStats', 'attendanceMatrix', 'categories', 'years', 'teachers'
        ));
    }

    public function toggleStatus($id)
    {
        $class = ClassModel::findOrFail($id);
        $class->update(['is_active' => !$class->is_active]);
        $statusMessage = $class->is_active ? 'activated' : 'deactivated';
        return back()->with('success', "Class successfully {$statusMessage}!");
    }

    public function delete($id)
    {
        try {
            $class = ClassModel::findOrFail($id);
            $class->delete(); 
            return redirect()->route('admin.classes.index')->with('success', 'Class moved to trash successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete class: ' . $e->getMessage());
        }
    }

    public function assignStudent(Request $request, $classId)
    {
        $request->validate(['student_ids' => 'required|array', 'student_ids.*' => 'exists:students,id']);
        Student::whereIn('id', $request->student_ids)->update(['class_id' => $classId]);
        $count = count($request->student_ids);
        return back()->with('success', "Successfully enrolled {$count} students to this class.");
    }

    public function unassignStudent($studentId)
    {
        Student::findOrFail($studentId)->update(['class_id' => null]);
        return back()->with('success', 'Student removed from class.');
    }

    public function assignTeacher(Request $request, $id)
    {
        $request->validate(['teacher_id' => 'required|exists:users,id', 'type' => 'required|in:form,local']);
        $class = ClassModel::findOrFail($id);
        $column = ($request->type === 'form') ? 'form_teacher_id' : 'local_teacher_id';
        $class->update([$column => $request->teacher_id]);
        return back()->with('success', 'Teacher assigned successfully!');
    }

    public function unassignTeacher($classId, $type)
    {
        $class = ClassModel::findOrFail($classId);
        if ($type === 'form') $class->update(['form_teacher_id' => null]);
        elseif ($type === 'local') $class->update(['local_teacher_id' => null]);
        return back()->with('success', ucfirst($type) . ' Teacher has been unassigned.');
    }

    public function dailyRecap(Request $request)
    {
        // 1. Ambil Tanggal Filter (Default: Hari Ini)
        $date = $request->input('date', \Carbon\Carbon::today()->format('Y-m-d'));
        
        // 2. Tentukan Hari (Monday, Tuesday, dst)
        $dayOfWeek = \Carbon\Carbon::parse($date)->format('l');

        // 3. Query Jadwal (Schedules) join ke Classes
        // Lalu Left Join ke Session untuk cek apakah sudah ada sesi hari ini
        $records = DB::table('schedules as sch')
            ->join('classes as c', 'sch.class_id', '=', 'c.id')
            ->leftJoin('class_sessions as s', function($join) use ($date) {
                $join->on('c.id', '=', 's.class_id')
                     ->where('s.date', '=', $date);
            })
            ->leftJoin('users as t', 's.teacher_id', '=', 't.id') // Ambil guru dari sesi (jika ada)
            ->where('sch.day_of_week', $dayOfWeek)
            ->where('c.is_active', 1)   // Hanya tampilkan kelas aktif
            ->whereNull('c.deleted_at')
            ->select(
                'c.id as class_id',
                'c.name as class_name',
                'c.start_time',
                'c.end_time',
                's.id as session_id',        // NULL jika belum ada sesi
                't.name as teacher_name',    // NULL jika belum ada sesi/guru
                'sch.teacher_type'           // form/local (info tambahan jika perlu)
            )
            ->orderBy('c.start_time', 'asc')
            ->paginate(20)->withQueryString();

        // Statistik dihapus sesuai request

        return view('admin.classes.daily-recap', compact('records', 'date'));
    }
}