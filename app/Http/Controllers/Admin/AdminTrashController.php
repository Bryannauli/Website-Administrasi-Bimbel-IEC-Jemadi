<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator; // Wajib import untuk paginasi manual collection
use App\Models\User;
use App\Models\Student;
use App\Models\ClassSession;
use App\Models\ClassModel; // Pastikan nama model sesuai (ClassModel.php)
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminTrashController extends Controller
{
    /**
     * Menampilkan semua data yang di-soft delete (Unified Trash)
     */
    public function index(Request $request)
    {
        // 1. Ambil Data Teacher yang dihapus
        $teachers = User::onlyTrashed()
            ->where('is_teacher', true)
            ->select('id', 'name', 'deleted_at')
            ->get()
            ->map(function ($item) {
                $item->type = 'teacher'; // Inject label type
                return $item;
            });

        // 2. Ambil Data Student yang dihapus
        $students = Student::onlyTrashed()
            ->select('id', 'name', 'deleted_at')
            ->get()
            ->map(function ($item) {
                $item->type = 'student';
                return $item;
            });

        // 3. Ambil Data Class yang dihapus
        $classes = ClassModel::onlyTrashed()
            ->select('id', 'name', 'deleted_at')
            ->get()
            ->map(function ($item) {
                $item->type = 'class';
                return $item;
            });

        // 4. Gabungkan (Merge) dan Sortir berdasarkan waktu dihapus (Terbaru di atas)
        $mergedTrash = $teachers
            ->merge($students)
            ->merge($classes)
            ->sortByDesc('deleted_at')
            ->values(); // Reset keys agar urutan array bersih

        // 5. Konfigurasi Paginasi Manual
        $page = $request->get('page', 1); // Halaman saat ini
        $perPage = 10; // Jumlah item per halaman
        
        // Slice collection sesuai halaman
        $currentPageItems = $mergedTrash->slice(($page - 1) * $perPage, $perPage)->all();

        // Buat Object Paginator
        $logs = new LengthAwarePaginator(
            $currentPageItems,
            $mergedTrash->count(),
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        $totalCount = $mergedTrash->count();

        return view('admin.trash.trash', compact('logs', 'totalCount'));
    }

    /**
     * Helper untuk mendapatkan Model Class berdasarkan string type
     */
    private function getModelClass($type)
    {
        return match($type) {
            'teacher' => User::class,
            'student' => Student::class,
            'class'   => ClassModel::class,
            default   => null,
        };
    }

    /**
     * Restore Data
     */
    public function restore($type, $id)
    {
        $modelClass = $this->getModelClass($type);

        if (!$modelClass) {
            return back()->with('error', 'Invalid item type.');
        }

        // Cari data di tong sampah
        $item = $modelClass::onlyTrashed()->find($id);

        if ($item) {
            $item->restore(); // Kembalikan data
            return redirect()->route('admin.trash.trash')
                ->with('success', ucfirst($type) . " '{$item->name}' has been restored successfully.");
        }

        return back()->with('error', 'Data not found in trash.');
    }

    /**
     * Force Delete (Hapus Permanen)
     */
    public function forceDelete($type, $id)
    {
        $modelClass = $this->getModelClass($type);

        if (!$modelClass) {
            return back()->with('error', 'Invalid item type.');
        }

        $item = $modelClass::onlyTrashed()->find($id);

        if ($item) {
            $name = $item->name;
            $item->forceDelete(); // Hapus selamanya dari database

            return redirect()->route('admin.trash.trash')
                ->with('success', ucfirst($type) . " '{$name}' has been permanently deleted.");
        }

        return back()->with('error', 'Data not found in trash.');
    }

    /**
     * Menampilkan Detail Siswa yang ada di Tong Sampah
     */
    public function detailTrashedStudent($id)
    {
        // 1. Ambil data siswa (Termasuk yang soft deleted)
        // Kita gunakan withTrashed() pada relasi classModel jaga-jaga kelasnya juga sudah dihapus
        $student = Student::onlyTrashed()
            ->with(['classModel' => function($q) {
                $q->withTrashed(); 
            }])
            ->findOrFail($id);

        // 2. Data pendukung untuk View (Sama seperti detail biasa)
        // Kita gunakan withTrashed() untuk list kelas jaga-jaga ingin melihat info kelas lama
        $classes = ClassModel::withTrashed()->orderBy('category')->orderBy('name')->get();
        $categories = $classes->pluck('category')->unique();

        // 3. Ambil History Absensi (Menggunakan Query Builder ke VIEW)
        // View DB tidak peduli soft delete, jadi data tetap akan muncul
        $attendance = DB::table('v_student_attendance')
            ->where('student_id', $id)
            ->orderBy('session_date', 'ASC')
            ->get();

        // 4. Ambil Statistik (Menggunakan Stored Procedure)
        // SP juga raw SQL, jadi aman
        $rawSummary = DB::select("CALL p_get_attendance_summary(?)", [$id]);
        
        // Handle jika result kosong
        if (empty($rawSummary)) {
            $summary = ['present' => 0, 'absent' => 0, 'late' => 0, 'permission' => 0, 'sick' => 0];
            $totalDays = 0;
            $presentPercent = 0;
        } else {
            $summaryData = $rawSummary[0];
            $summary = [
                'present'      => $summaryData->present,
                'absent'       => $summaryData->absent,
                'late'         => $summaryData->late,
                'permission'   => $summaryData->permission,
                'sick'         => $summaryData->sick,
            ];
            $totalDays = $summaryData->total_days;
            $presentPercent = round($summaryData->present_percent);
        }

        // 5. Return View yang SAMA, tapi kirim flag 'isTrashed'
        return view('admin.student.detail-student', compact(
            'student', 'attendance', 'summary', 'presentPercent', 
            'totalDays', 'classes', 'categories'
        ))->with('isTrashed', true);
    }

    /**
     * Menampilkan Detail Teacher yang ada di Tong Sampah
     */
    public function detailTrashedTeacher(Request $request, $id)
    {
        // 1. Ambil Data Guru (Soft Deleted)
        // Gunakan withTrashed() pada relasi kelas untuk jaga-jaga jika kelasnya juga sudah dihapus
        $teacher = User::onlyTrashed()
            ->with([
                'formClasses' => fn($q) => $q->withTrashed(), 
                'localClasses' => fn($q) => $q->withTrashed()
            ])
            ->where('is_teacher', 1)
            ->findOrFail($id);

        // 2. Filter Date (Sama seperti detail normal)
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate   = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        // 3. Ambil History Mengajar (View SQL tidak peduli deleted_at, jadi aman)
        $query = DB::table('v_teacher_teaching_history')
            ->where('teacher_id', $id)
            ->whereBetween('date', [$startDate, $endDate]);

        // History Data
        $history = $query->clone()
            ->orderBy('date', 'asc')
            ->orderBy('start_time', 'asc')
            ->get();

        // Statistik
        $summary = [
            'total_sessions' => $query->clone()->count(),
            'unique_classes' => $query->clone()->distinct('class_id')->count('class_id'),
        ];

        // 4. Return View dengan Flag isTrashed
        // Kita gunakan View yang SAMA dengan AdminTeacherController
        return view('admin.teacher.detail-teacher', compact(
            'teacher', 'history', 'summary', 'startDate', 'endDate'
        ))->with('isTrashed', true);
    }

    /**
     * Menampilkan Detail Class yang ada di Tong Sampah
     */
    public function detailTrashedClass(Request $request, $id)
    {
        // 1. Ambil Data Class (Soft Deleted)
        $class = ClassModel::onlyTrashed()
            ->with([
                'schedules',
                // Gunakan withTrashed utk relasi guru, jaga-jaga gurunya juga dihapus
                'formTeacher' => fn($q) => $q->withTrashed(),
                'localTeacher' => fn($q) => $q->withTrashed(),
                // Student juga withTrashed + Sort
                'students' => fn($q) => $q->withTrashed()->orderBy('student_number', 'asc'),
                'assessmentSessions'
            ])
            ->findOrFail($id);
            
        // Hitung manual karena property count kadang tidak update di soft delete
        $class->students_count = $class->students->count();

        // 2. Available Students (KOSONGKAN)
        // Kita tidak bisa menambah siswa ke kelas sampah
        $availableStudents = collect([]); 

        // 3. Teaching Logs (History)
        // View SQL aman, karena berbasis class_sessions
        $teachingLogs = DB::table('v_class_activity_logs')
            ->where('class_id', $id)
            ->orderBy('date', 'asc')
            ->get();

        // 4. Last Session
        $lastSession = ClassSession::where('class_id', $id)
            ->with(['teacher' => fn($q) => $q->withTrashed()]) // Teacher mungkin trashed
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->first();

        // 5. Student Stats (Stored Procedure)
        // SP menggunakan RAW SQL, jadi dia akan mengambil semua student (termasuk yg deleted)
        // selama Class ID-nya cocok.
        $studentStats = DB::select('CALL p_get_class_attendance_stats(?)', [$id]);

        // 6. Attendance Matrix (Logika Manual)
        $rawLogs = ClassSession::where('class_id', $id)
            ->with(['records:id,class_session_id,student_id,status'])
            ->get();
            
        $attendanceMatrix = []; 
        foreach ($rawLogs as $session) {
            foreach ($session->records as $record) {
                $attendanceMatrix[$record->student_id][$session->id] = $record->status;
            }
        }

        // 7. Data Pendukung Form Edit (Hanya visual, karena edit dimatikan)
        $categories = ['pre_level', 'level', 'step', 'private'];
        $years = ClassModel::withTrashed()->select('academic_year')->distinct()->pluck('academic_year')->sortDesc();
        $teachers = User::withTrashed()->where('is_teacher', true)->orderBy('name', 'asc')->get();

        // 8. Return View
        return view('admin.classes.detail-class', compact(
            'class', 'availableStudents', 'teachingLogs', 'lastSession',
            'studentStats', 'attendanceMatrix', 'categories', 'years', 'teachers'
        ))->with('isTrashed', true);
    }
}