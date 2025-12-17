<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Student;
use App\Models\ClassModel; // Pastikan sesuai nama model Anda
use App\Models\ClassSession;
use Carbon\Carbon;

class AdminTrashController extends Controller
{
    /**
     * Menampilkan semua data yang di-soft delete (Unified Trash)
     * SEKARANG MENGGUNAKAN DATABASE VIEW: v_unified_trash
     */
    public function index(Request $request)
    {
        // 1 baris magis menggantikan puluhan baris logika merge manual!
        $logs = DB::table('v_unified_trash')
            ->orderBy('deleted_at', 'desc')
            ->paginate(10); 

        $totalCount = $logs->total();

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
            return redirect()->route('admin.trash.index')
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

            return redirect()->route('admin.trash.index')
                ->with('success', ucfirst($type) . " '{$name}' has been permanently deleted.");
        }

        return back()->with('error', 'Data not found in trash.');
    }

    /**
     * Menampilkan Detail Siswa yang ada di Tong Sampah
     */
    public function detailTrashedStudent($id)
    {
        $student = Student::onlyTrashed()
            ->with(['classModel' => function($q) {
                $q->withTrashed(); 
            }])
            ->findOrFail($id);

        $classes = ClassModel::withTrashed()->orderBy('category')->orderBy('name')->get();
        $categories = $classes->pluck('category')->unique();

        $attendance = DB::table('v_student_attendance')
            ->where('student_id', $id)
            ->orderBy('session_date', 'ASC')
            ->get();

        $rawSummary = DB::select("CALL p_get_attendance_summary(?)", [$id]);
        
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
        $teacher = User::onlyTrashed()
            ->with([
                'formClasses' => fn($q) => $q->withTrashed(), 
                'localClasses' => fn($q) => $q->withTrashed()
            ])
            ->where('is_teacher', 1)
            ->findOrFail($id);

        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate   = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $query = DB::table('v_teacher_teaching_history')
            ->where('teacher_id', $id)
            ->whereBetween('date', [$startDate, $endDate]);

        $history = $query->clone()
            ->orderBy('date', 'asc')
            ->orderBy('start_time', 'asc')
            ->get();

        $summary = [
            'total_sessions' => $query->clone()->count(),
            'unique_classes' => $query->clone()->distinct('class_id')->count('class_id'),
        ];

        return view('admin.teacher.detail-teacher', compact(
            'teacher', 'history', 'summary', 'startDate', 'endDate'
        ))->with('isTrashed', true);
    }

    /**
     * Menampilkan Detail Class yang ada di Tong Sampah
     */
    public function detailTrashedClass(Request $request, $id)
    {
        $class = ClassModel::onlyTrashed()
            ->with([
                'schedules',
                'formTeacher' => fn($q) => $q->withTrashed(),
                'localTeacher' => fn($q) => $q->withTrashed(),
                'students' => fn($q) => $q->withTrashed()->orderBy('student_number', 'asc'),
                'assessmentSessions'
            ])
            ->findOrFail($id);
            
        $class->students_count = $class->students->count();

        $availableStudents = collect([]); 

        $teachingLogs = DB::table('v_class_activity_logs')
            ->where('class_id', $id)
            ->orderBy('date', 'asc')
            ->get();

        $lastSession = ClassSession::where('class_id', $id)
            ->with(['teacher' => fn($q) => $q->withTrashed()]) 
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->first();

        $studentStats = DB::select('CALL p_get_class_attendance_stats(?)', [$id]);

        $rawLogs = ClassSession::where('class_id', $id)
            ->with(['records:id,class_session_id,student_id,status'])
            ->get();
            
        $attendanceMatrix = []; 
        foreach ($rawLogs as $session) {
            foreach ($session->records as $record) {
                $attendanceMatrix[$record->student_id][$session->id] = $record->status;
            }
        }

        $categories = ['pre_level', 'level', 'step', 'private'];
        $years = ClassModel::withTrashed()->select('academic_year')->distinct()->pluck('academic_year')->sortDesc();
        $teachers = User::withTrashed()->where('is_teacher', true)->orderBy('name', 'asc')->get();

        return view('admin.classes.detail-class', compact(
            'class', 'availableStudents', 'teachingLogs', 'lastSession',
            'studentStats', 'attendanceMatrix', 'categories', 'years', 'teachers'
        ))->with('isTrashed', true);
    }
}