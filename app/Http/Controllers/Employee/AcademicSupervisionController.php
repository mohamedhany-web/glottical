<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\ClassroomMeeting;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AcademicSupervisionController extends Controller
{
    private const CLASSROOM_MAX_PARTICIPANTS = 50;

    private const CLASSROOM_MAX_DURATION_MINUTES = 180;

    private const CLASSROOM_DEFAULT_DURATION_MINUTES = 60;
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $supervisor = Auth::user();
        $this->ensureAcademicSupervisor($supervisor);

        $students = $supervisor
            ->supervisedStudentsAsAcademic()
            ->where('users.role', 'student')
            ->orderBy('users.name')
            ->get();

        $studentIds = $students->pluck('id');

        $liveMeetings = ClassroomMeeting::query()
            ->whereIn('user_id', $studentIds)
            ->whereNotNull('started_at')
            ->whereNull('ended_at')
            ->whereNull('consultation_request_id')
            ->with(['user:id,name,email'])
            ->withCount('participants')
            ->get()
            ->keyBy('user_id');

        return view('employee.academic-supervision.index', compact('students', 'liveMeetings'));
    }

    public function show(User $student)
    {
        $supervisor = Auth::user();
        $this->ensureAcademicSupervisor($supervisor);
        $this->ensureSupervises($supervisor, $student);

        $student->loadCount('courseEnrollments');
        $enrollments = $student->courseEnrollments()
            ->with(['course:id,title'])
            ->orderByDesc('enrolled_at')
            ->limit(30)
            ->get();

        $subscription = null;
        $limits = [
            'classroom_meetings_per_month' => 9999,
            'classroom_max_participants' => self::CLASSROOM_MAX_PARTICIPANTS,
            'classroom_default_duration_minutes' => self::CLASSROOM_DEFAULT_DURATION_MINUTES,
            'classroom_max_duration_minutes' => self::CLASSROOM_MAX_DURATION_MINUTES,
        ];
        $usedMeetingsThisMonth = ClassroomMeeting::query()
            ->where('user_id', $student->id)
            ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->count();
        $hasClassroom = true;

        $meetings = ClassroomMeeting::query()
            ->where('user_id', $student->id)
            ->whereNull('consultation_request_id')
            ->withCount('participants')
            ->orderByDesc('created_at')
            ->limit(25)
            ->get();

        $liveMeeting = $meetings->first(fn ($m) => $m->isLive());

        return view('employee.academic-supervision.show', compact(
            'student',
            'enrollments',
            'subscription',
            'limits',
            'usedMeetingsThisMonth',
            'hasClassroom',
            'meetings',
            'liveMeeting'
        ));
    }

    public function observerRoom(ClassroomMeeting $meeting)
    {
        $supervisor = Auth::user();
        $this->ensureAcademicSupervisor($supervisor);

        if ($meeting->consultation_request_id) {
            abort(403, 'غرف الاستشارة غير متاحة للدخول من مسار الإشراف الأكاديمي.');
        }

        $student = $meeting->user;
        if (! $student || $student->role !== 'student') {
            abort(404);
        }

        $this->ensureSupervises($supervisor, $student);

        if (! $meeting->isLive()) {
            return redirect()
                ->route('employee.academic-supervision.show', $student)
                ->with('error', 'الاجتماع غير نشط حالياً.');
        }

        if ($meeting->ended_at) {
            return redirect()
                ->route('employee.academic-supervision.show', $student)
                ->with('error', 'انتهى هذا الاجتماع.');
        }

        $maxDurationMinutes = self::CLASSROOM_MAX_DURATION_MINUTES;
        $effectiveDurationMinutes = (int) ($meeting->planned_duration_minutes ?: self::CLASSROOM_DEFAULT_DURATION_MINUTES);
        if ($effectiveDurationMinutes > $maxDurationMinutes) {
            $effectiveDurationMinutes = $maxDurationMinutes;
        }

        if ($meeting->started_at && $meeting->started_at->copy()->addMinutes($effectiveDurationMinutes)->isPast()) {
            if (! $meeting->ended_at) {
                $meeting->update(['ended_at' => now()]);
            }

            return redirect()
                ->route('employee.academic-supervision.show', $student)
                ->with('error', 'انتهت مدة الاجتماع.');
        }

        $meetingPayload = app(\App\Services\LiveMeetingProvider::class)->classroomPayload(
            $meeting->liveRoomName(),
            $supervisor,
            false,
            [
                'canPublish' => false,
                'canSubscribe' => true,
                'canPublishData' => false,
                'hidden' => true,
                'roomAdmin' => false,
            ]
        );
        $meetingEndsAt = $meeting->started_at ? $meeting->started_at->copy()->addMinutes($effectiveDurationMinutes) : null;
        $useInstructorRoutes = false;
        $user = $supervisor;
        $academicObserverMode = true;
        $academicObserverExitUrl = route('employee.academic-supervision.show', $student);
        $subscriptionFeatureMenuItems = [];
        $subscriptionPackageLabel = null;

        return view('student.classroom.room', array_merge(
            compact(
                'meeting',
                'user',
                'maxDurationMinutes',
                'effectiveDurationMinutes',
                'meetingEndsAt',
                'useInstructorRoutes',
                'academicObserverMode',
                'academicObserverExitUrl',
                'subscriptionFeatureMenuItems',
                'subscriptionPackageLabel'
            ),
            $meetingPayload
        ));
    }

    private function ensureAcademicSupervisor(User $user): void
    {
        if (! $user->is_employee || $user->employeeJob?->code !== 'academic_supervisor') {
            abort(403, 'هذا القسم متاح لمشرف أكاديمي فقط.');
        }
        if (! $user->employeeCan('academic_supervision_desk')) {
            abort(403);
        }
    }

    private function ensureSupervises(User $supervisor, User $student): void
    {
        if ($student->role !== 'student') {
            abort(404);
        }
        if (! $supervisor->supervisedStudentsAsAcademic()->whereKey($student->id)->exists()) {
            abort(403, 'هذا الطالب غير ضمن قائمة إشرافك.');
        }
    }
}
