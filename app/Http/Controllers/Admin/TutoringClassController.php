<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TutoringClassSession;
use App\Models\TutoringCohortEnrollment;
use App\Models\TutoringGroup;
use App\Models\TutoringGroupCohort;
use App\Models\User;
use App\Services\TutoringClassService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use InvalidArgumentException;

class TutoringClassController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = $request->user();
            if (! $user || (! $user->isAdmin() && ! $user->hasPermission('manage.tutoring-groups'))) {
                abort(403);
            }

            return $next($request);
        });
    }

    public function show(TutoringGroup $tutoringGroup, TutoringGroupCohort $cohort): View
    {
        abort_unless($tutoringGroup->isCollective() && (int) $cohort->tutoring_group_id === (int) $tutoringGroup->id, 404);

        $cohort->load([
            'tutoringGroup.instructor',
            'enrollments' => fn ($q) => $q->with('user')->latest('enrolled_at'),
            'classSessions' => fn ($q) => $q->with('classroomMeeting')->orderBy('session_number'),
        ]);

        $nextSession = $cohort->classSessions
            ->where('status', '!=', TutoringClassSession::STATUS_CANCELLED)
            ->filter(fn ($s) => $s->starts_at && $s->starts_at->gte(now()->subHour()))
            ->sortBy('starts_at')
            ->first();

        return view('admin.tutoring-groups.classes.show', [
            'group' => $tutoringGroup,
            'cohort' => $cohort,
            'nextSession' => $nextSession,
            'type' => $tutoringGroup->type,
        ]);
    }

    public function generateSchedule(Request $request, TutoringGroup $tutoringGroup, TutoringGroupCohort $cohort): RedirectResponse
    {
        abort_unless((int) $cohort->tutoring_group_id === (int) $tutoringGroup->id, 404);

        try {
            $created = TutoringClassService::generateSchedule(
                $cohort,
                $request->boolean('replace_future')
            );
            $rooms = TutoringClassService::ensureAllMeetings($cohort->fresh());
        } catch (InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()
            ->route('admin.tutoring-groups.classes.show', [$tutoringGroup, $cohort])
            ->with('success', 'تم توليد '.count($created).' حصة'.($rooms ? ' وتهيئة '.$rooms.' غرفة Live' : '').'.');
    }

    public function ensureRooms(TutoringGroup $tutoringGroup, TutoringGroupCohort $cohort): RedirectResponse
    {
        abort_unless((int) $cohort->tutoring_group_id === (int) $tutoringGroup->id, 404);
        $n = TutoringClassService::ensureAllMeetings($cohort);

        return back()->with('success', $n > 0 ? "تم إنشاء {$n} غرفة مشتركة." : 'كل الحصص لديها غرف مسبقاً.');
    }

    public function storeEnrollment(Request $request, TutoringGroup $tutoringGroup, TutoringGroupCohort $cohort): RedirectResponse
    {
        abort_unless((int) $cohort->tutoring_group_id === (int) $tutoringGroup->id, 404);

        $data = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $user = User::query()->findOrFail($data['user_id']);

        try {
            TutoringClassService::enrollStudent(
                $cohort,
                $user,
                notes: $data['notes'] ?? null,
                countSeat: true,
            );
        } catch (InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'تم إضافة الطالب للفصل.');
    }

    public function cancelEnrollment(
        TutoringGroup $tutoringGroup,
        TutoringGroupCohort $cohort,
        TutoringCohortEnrollment $enrollment
    ): RedirectResponse {
        abort_unless(
            (int) $cohort->tutoring_group_id === (int) $tutoringGroup->id
            && (int) $enrollment->tutoring_group_cohort_id === (int) $cohort->id,
            404
        );

        TutoringClassService::cancelEnrollment($enrollment);

        return back()->with('success', 'تم إلغاء تسجيل الطالب.');
    }

    public function updateSession(
        Request $request,
        TutoringGroup $tutoringGroup,
        TutoringGroupCohort $cohort,
        TutoringClassSession $session
    ): RedirectResponse {
        abort_unless(
            (int) $cohort->tutoring_group_id === (int) $tutoringGroup->id
            && (int) $session->tutoring_group_cohort_id === (int) $cohort->id,
            404
        );

        $data = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['nullable', 'date', 'after:starts_at'],
            'status' => ['required', 'in:scheduled,live,completed,cancelled'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $session->update($data);

        if ($session->classroom_meeting_id && isset($data['starts_at'])) {
            $session->classroomMeeting?->update([
                'scheduled_for' => $session->starts_at,
                'title' => ($tutoringGroup->title ?? 'فصل').' — '.$session->displayTitle(),
            ]);
        }

        return back()->with('success', 'تم تحديث الحصة.');
    }

    public function ensureSessionRoom(
        TutoringGroup $tutoringGroup,
        TutoringGroupCohort $cohort,
        TutoringClassSession $session
    ): RedirectResponse {
        abort_unless(
            (int) $cohort->tutoring_group_id === (int) $tutoringGroup->id
            && (int) $session->tutoring_group_cohort_id === (int) $cohort->id,
            404
        );

        $meeting = TutoringClassService::ensureSessionMeeting($session);

        return back()->with('success', 'غرفة Live جاهزة: '.$meeting->code);
    }

    public function cancelSession(
        TutoringGroup $tutoringGroup,
        TutoringGroupCohort $cohort,
        TutoringClassSession $session
    ): RedirectResponse {
        abort_unless(
            (int) $cohort->tutoring_group_id === (int) $tutoringGroup->id
            && (int) $session->tutoring_group_cohort_id === (int) $cohort->id,
            404
        );

        TutoringClassService::cancelSession($session);

        return back()->with('success', 'تم إلغاء الحصة.');
    }

    public function completeSession(
        TutoringGroup $tutoringGroup,
        TutoringGroupCohort $cohort,
        TutoringClassSession $session
    ): RedirectResponse {
        abort_unless(
            (int) $cohort->tutoring_group_id === (int) $tutoringGroup->id
            && (int) $session->tutoring_group_cohort_id === (int) $cohort->id,
            404
        );

        TutoringClassService::completeSession($session);

        return back()->with('success', 'تم تعليم الحصة كمكتملة.');
    }

    public function storeSession(Request $request, TutoringGroup $tutoringGroup, TutoringGroupCohort $cohort): RedirectResponse
    {
        abort_unless((int) $cohort->tutoring_group_id === (int) $tutoringGroup->id, 404);

        $data = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['nullable', 'date', 'after:starts_at'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $next = (int) TutoringClassSession::query()
            ->where('tutoring_group_cohort_id', $cohort->id)
            ->max('session_number') + 1;

        $starts = \Carbon\Carbon::parse($data['starts_at']);
        $ends = ! empty($data['ends_at'])
            ? \Carbon\Carbon::parse($data['ends_at'])
            : $starts->copy()->addMinutes((int) ($cohort->session_duration_minutes ?: $tutoringGroup->duration_minutes ?: 60));

        $session = TutoringClassSession::create([
            'tutoring_group_cohort_id' => $cohort->id,
            'tutoring_group_id' => $tutoringGroup->id,
            'session_number' => max(1, $next),
            'title' => $data['title'] ?: ('الحصة '.$next),
            'starts_at' => $starts,
            'ends_at' => $ends,
            'status' => TutoringClassSession::STATUS_SCHEDULED,
            'notes' => $data['notes'] ?? null,
        ]);

        TutoringClassService::ensureSessionMeeting($session);

        return back()->with('success', 'تمت إضافة حصة جديدة.');
    }
}
