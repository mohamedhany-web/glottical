<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Course;
use App\Models\User;
use App\Models\Subject;
use App\Models\Classroom;
use App\Models\Order;
use App\Models\AdvancedCourse;
use App\Models\ContactMessage;
use App\Models\Assignment;
use App\Models\Exam;
use App\Models\Certificate;
use App\Models\LectureVideoQuestionAnswer;

class DashboardController extends Controller
{

    public function index()
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect('/login')->with('error', 'يجب تسجيل الدخول أولاً');
        }
        
        // التحقق من أن المستخدم نشط
        if (!$user->is_active) {
            Auth::logout();
            return redirect('/login')->with('error', 'حسابك غير نشط. يرجى التواصل مع الإدارة.');
        }
        
        // التحقق من كون المستخدم موظف
        if ($user->isEmployee()) {
            // الموظف ذو دور RBAC مخصص → لوحة تحكم الأدمن بصلاحيات محدودة
            if ($user->roles()->exists()) {
                return redirect()->route('admin.dashboard');
            }
            // الموظف العادي → لوحة الموظفين
            return redirect()->route('employee.dashboard');
        }
        
        // التحقق من وجود دور للمستخدم
        if (!$user->role) {
            Auth::logout();
            return redirect('/login')->with('error', 'دور المستخدم غير محدد. يرجى التواصل مع الإدارة.');
        }
        
        // دعم الأدوار القديمة والجديدة للتوافق
        $role = strtolower(trim($user->role));
        
        switch ($role) {
            case 'super_admin':
            case 'admin': // للتوافق مع الأدوار القديمة
                // توجيه المديرين إلى لوحة التحكم الأساسية
                return redirect()->route('admin.dashboard');
            case 'instructor':
            case 'teacher': // للتوافق مع الأدوار القديمة
                return $this->instructorDashboard();
            case 'student':
                return $this->studentDashboard();
            default:
                // إذا كان الدور غير معروف، نعيد إلى الصفحة الرئيسية مع رسالة خطأ
                Auth::logout();
                return redirect('/login')->with('error', 'دور المستخدم غير صالح: ' . $role . '. يرجى التواصل مع الإدارة.');
        }
    }


    private function instructorDashboard()
    {
        $user = Auth::user();
        
        try {
            // معرفات الكورسات التي يدرّسها المدرب: مباشرة (instructor_id) + المعينة له في المسارات (assigned_courses)
            $directCourseIds = \App\Models\AdvancedCourse::where('instructor_id', $user->id)->pluck('id');
            $assignedFromPaths = $user->teachingLearningPaths()->get()->flatMap(function ($ay) {
                $ids = json_decode($ay->pivot->assigned_courses ?? '[]', true);
                return is_array($ids) ? $ids : [];
            });
            $teachingCourseIds = $directCourseIds->merge($assignedFromPaths)->unique()->filter()->values();

            // عدد الكورسات التي يدرّسها
            $myCoursesCount = $teachingCourseIds->count();

            // الكورسات (آخر 5 للعرض)
            $my_courses = $myCoursesCount > 0
                ? \App\Models\AdvancedCourse::whereIn('id', $teachingCourseIds)
                    ->with(['academicSubject', 'academicYear'])
                    ->withCount(['enrollments as active_students_count' => function ($q) {
                        $q->where('status', 'active');
                    }])
                    ->latest()
                    ->take(5)
                    ->get()
                : collect();

            // إحصائيات حقيقية (مبنية على كورسات التدريس فقط)
            $stats = [
                'my_courses' => $myCoursesCount,
                'total_students' => $teachingCourseIds->isEmpty()
                    ? 0
                    : \App\Models\StudentCourseEnrollment::whereIn('advanced_course_id', $teachingCourseIds)
                        ->where('status', 'active')
                        ->distinct('user_id')
                        ->count('user_id'),
                'my_classrooms' => Classroom::where('teacher_id', $user->id)->count(),
                'total_lectures' => $teachingCourseIds->isEmpty()
                    ? 0
                    : \App\Models\Lecture::whereIn('course_id', $teachingCourseIds)->count(),
                'upcoming_lectures' => $teachingCourseIds->isEmpty()
                    ? 0
                    : \App\Models\Lecture::whereIn('course_id', $teachingCourseIds)
                        ->where('status', 'scheduled')
                        ->where('scheduled_at', '>=', now())
                        ->count(),
                'total_assignments' => \App\Models\Assignment::where('teacher_id', $user->id)->count(),
                'pending_submissions' => \App\Models\AssignmentSubmission::whereHas('assignment', function ($q) use ($user) {
                    $q->where('teacher_id', $user->id);
                })->whereNull('graded_at')->count(),
                'total_exams' => \App\Models\Exam::where('created_by', $user->id)->count(),
            ];

            // المحاضرات القادمة (للكورسات التي يدرّسها فقط)
            $upcoming_lectures = $teachingCourseIds->isEmpty()
                ? collect()
                : \App\Models\Lecture::whereIn('course_id', $teachingCourseIds)
                ->where('status', 'scheduled')
                ->where('scheduled_at', '>=', now())
                ->with(['course', 'lesson'])
                ->orderBy('scheduled_at', 'asc')
                ->take(5)
                ->get();

            // الواجبات المعلقة (تسليمات تحتاج تقييم)
            $pending_assignments = \App\Models\AssignmentSubmission::whereHas('assignment', function($q) use ($user) {
                    $q->where('teacher_id', $user->id);
                })
                ->whereNull('graded_at')
                ->with(['assignment', 'student'])
                ->latest()
                ->take(5)
                ->get();

            $my_classrooms = Classroom::where('teacher_id', $user->id)
                ->with('students')
                ->latest()
                ->take(5)
                ->get();

            $upcomingTutoringBooking = null;
            $upcomingTutoringCount = 0;
            if (\Illuminate\Support\Facades\Schema::hasTable('tutoring_group_bookings')) {
                $upcomingTutoringBooking = \App\Models\TutoringGroupBooking::query()
                    ->where('instructor_id', $user->id)
                    ->where('status', \App\Models\TutoringGroupBooking::STATUS_CONFIRMED)
                    ->where('starts_at', '>=', now())
                    ->with(['tutoringGroup', 'classroomMeeting', 'user'])
                    ->orderBy('starts_at')
                    ->first();
                $upcomingTutoringCount = \App\Models\TutoringGroupBooking::query()
                    ->where('instructor_id', $user->id)
                    ->where('status', \App\Models\TutoringGroupBooking::STATUS_CONFIRMED)
                    ->where('starts_at', '>=', now())
                    ->count();
            }
            $stats['upcoming_tutoring'] = $upcomingTutoringCount;

            return view('dashboard.instructor', compact(
                'stats',
                'my_courses',
                'my_classrooms',
                'upcoming_lectures',
                'pending_assignments',
                'upcomingTutoringBooking'
            ));
        } catch (\Exception $e) {
            // في حالة وجود خطأ، نعيد لوحة تحكم بسيطة
            \Log::error('Instructor Dashboard Error: ' . $e->getMessage());
            $stats = [
                'my_courses' => 0,
                'total_students' => 0,
                'my_classrooms' => 0,
                'total_lectures' => 0,
                'upcoming_lectures' => 0,
                'total_assignments' => 0,
                'pending_submissions' => 0,
                'total_exams' => 0,
                'upcoming_tutoring' => 0,
            ];
            $my_courses = collect();
            $my_classrooms = collect();
            $upcoming_lectures = collect();
            $pending_assignments = collect();
            $upcomingTutoringBooking = null;

            return view('dashboard.instructor', compact(
                'stats',
                'my_courses',
                'my_classrooms',
                'upcoming_lectures',
                'pending_assignments',
                'upcomingTutoringBooking'
            ));
        }
    }

    private function studentDashboard()
    {
        $user = Auth::user();

        // School Home هي تجربة الطالب الأساسية (فصول / جدول / حضور / رصيد)
        $payload = app(\App\Services\StudentSchoolHomeService::class)->build($user, [
            'week' => request()->query('week'),
            'view' => request()->query('view'),
            'sort' => request()->query('sort'),
            'q' => request()->query('q'),
        ]);

        return view('student.school.home', $payload);
    }

    private function calculateOverallProgress($user)
    {
        $enrollments = $user->courseEnrollments()
            ->whereIn('status', ['active', 'completed'])
            ->get();
        if ($enrollments->isEmpty()) return 0;
        
        $totalProgress = $enrollments->reduce(function ($carry, $enrollment) {
            return $carry + (float) ($enrollment->progress ?? 0);
        }, 0);

        return round($totalProgress / $enrollments->count(), 1);
    }

}
