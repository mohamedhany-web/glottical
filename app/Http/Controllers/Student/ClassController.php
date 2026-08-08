<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\TutoringClassSession;
use App\Models\TutoringCohortEnrollment;
use App\Models\TutoringGroupCohort;
use App\Services\TutoringClassService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use InvalidArgumentException;

class ClassController extends Controller
{
    public function index(Request $request): View
    {
        $enrollments = TutoringCohortEnrollment::query()
            ->with([
                'cohort.tutoringGroup.instructor',
                'cohort.classSessions' => fn ($q) => $q
                    ->where('status', '!=', TutoringClassSession::STATUS_CANCELLED)
                    ->orderBy('starts_at')
                    ->limit(5),
            ])
            ->where('user_id', $request->user()->id)
            ->where('status', TutoringCohortEnrollment::STATUS_ACTIVE)
            ->latest('enrolled_at')
            ->get();

        $upcoming = TutoringClassSession::query()
            ->with(['cohort.tutoringGroup', 'classroomMeeting'])
            ->whereIn('tutoring_group_cohort_id', $enrollments->pluck('tutoring_group_cohort_id'))
            ->where('status', '!=', TutoringClassSession::STATUS_CANCELLED)
            ->where('starts_at', '>=', now()->subHour())
            ->orderBy('starts_at')
            ->limit(10)
            ->get();

        return view('student.classes.index', [
            'enrollments' => $enrollments,
            'upcoming' => $upcoming,
        ]);
    }

    public function show(Request $request, TutoringGroupCohort $cohort): View
    {
        abort_unless(TutoringClassService::userCanAccessCohort($request->user(), $cohort), 403);

        $cohort->load([
            'tutoringGroup.instructor',
            'classSessions' => fn ($q) => $q->with('classroomMeeting')->orderBy('session_number'),
            'activeEnrollments.user',
        ]);

        $enrollment = TutoringCohortEnrollment::query()
            ->where('tutoring_group_cohort_id', $cohort->id)
            ->where('user_id', $request->user()->id)
            ->where('status', TutoringCohortEnrollment::STATUS_ACTIVE)
            ->first();

        return view('student.classes.show', [
            'cohort' => $cohort,
            'enrollment' => $enrollment,
        ]);
    }

    public function joinSession(Request $request, TutoringClassSession $session): RedirectResponse
    {
        $session->loadMissing(['cohort.tutoringGroup', 'classroomMeeting']);
        abort_unless($session->cohort, 404);
        abort_unless(TutoringClassService::userCanAccessCohort($request->user(), $session->cohort), 403);

        if ($session->status === TutoringClassSession::STATUS_CANCELLED) {
            return back()->with('error', 'هذه الحصة ملغاة.');
        }

        if (! $session->isJoinable() && ! $request->user()->isAdmin()) {
            return back()->with('error', 'لم يفتح باب الدخول بعد. يُسمح قبل الموعد بـ 30 دقيقة.');
        }

        $meeting = TutoringClassService::ensureSessionMeeting($session);
        TutoringClassService::markAttendanceOnJoin($session, $request->user());

        if ($session->status === TutoringClassSession::STATUS_SCHEDULED) {
            $session->update(['status' => TutoringClassSession::STATUS_LIVE]);
        }

        return redirect()->away(url('classroom/join/'.$meeting->code));
    }

    /**
     * Manual join for free/open cohorts (admin may still require payment via checkout).
     */
    public function enroll(Request $request, TutoringGroupCohort $cohort): RedirectResponse
    {
        if (! $cohort->isEnrollmentOpen()) {
            return back()->with('error', 'الانضمام مغلق لهذه الدفعة.');
        }

        $group = $cohort->tutoringGroup;
        if ($group && (float) ($group->price ?? 0) > 0) {
            return redirect()
                ->route('public.groups.checkout', ['slug' => $group->slug, 'cohort' => $cohort->id])
                ->with('info', 'أكمل الاشتراك للانضمام للفصل.');
        }

        try {
            TutoringClassService::enrollStudent($cohort, $request->user(), countSeat: true);
        } catch (InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()
            ->route('student.classes.show', $cohort)
            ->with('success', 'تم انضمامك للفصل.');
    }
}
