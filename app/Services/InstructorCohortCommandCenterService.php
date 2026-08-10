<?php

namespace App\Services;

use App\Models\TutoringClassAttendance;
use App\Models\TutoringClassSession;
use App\Models\TutoringCohortEnrollment;
use App\Models\TutoringGroupCohort;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class InstructorCohortCommandCenterService
{
    /**
     * @return array{
     *     students_count: int,
     *     active_today: int,
     *     average_progress: int,
     *     sessions_total: int,
     *     sessions_completed: int,
     *     sessions_upcoming: int,
     *     today_sessions: Collection,
     *     upcoming_sessions: Collection,
     *     roster: Collection,
     *     at_risk: Collection
     * }
     */
    public function build(TutoringGroupCohort $cohort): array
    {
        $cohort->loadMissing([
            'tutoringGroup:id,title,instructor_id,academic_year_id,academic_subject_id',
            'tutoringGroup.schoolYear:id,name,level_number',
            'tutoringGroup.schoolSubject:id,name',
            'tutoringGroup.instructor:id,name',
        ]);

        $enrollments = collect();
        if (Schema::hasTable('tutoring_cohort_enrollments')) {
            $enrollments = TutoringCohortEnrollment::query()
                ->with(['user:id,name,email,phone,last_login_at'])
                ->where('tutoring_group_cohort_id', $cohort->id)
                ->where('status', TutoringCohortEnrollment::STATUS_ACTIVE)
                ->orderBy('enrolled_at')
                ->get();
        }

        $sessions = collect();
        if (Schema::hasTable('tutoring_class_sessions')) {
            $sessions = TutoringClassSession::query()
                ->with(['classroomMeeting:id,code'])
                ->where('tutoring_group_cohort_id', $cohort->id)
                ->where('status', '!=', TutoringClassSession::STATUS_CANCELLED)
                ->orderBy('session_number')
                ->orderBy('starts_at')
                ->get();
        }

        $sessionIds = $sessions->pluck('id');
        $attendances = collect();
        if ($sessionIds->isNotEmpty() && Schema::hasTable('tutoring_class_attendances')) {
            $attendances = TutoringClassAttendance::query()
                ->whereIn('tutoring_class_session_id', $sessionIds)
                ->get();
        }

        $attendanceByUser = $attendances->groupBy('user_id');
        $completedSessions = $sessions->where('status', TutoringClassSession::STATUS_COMPLETED);
        $completedCount = $completedSessions->count();
        $totalPlanned = max(1, (int) ($cohort->sessions_count ?: $sessions->count() ?: 1));

        $roster = $enrollments->map(function (TutoringCohortEnrollment $enrollment) use ($attendanceByUser, $completedCount, $totalPlanned) {
            $user = $enrollment->user;
            $userAtt = $attendanceByUser->get($user?->id, collect());
            $presentCount = $userAtt->filter(fn ($a) => in_array($a->status, [
                TutoringClassAttendance::STATUS_PRESENT,
                TutoringClassAttendance::STATUS_LATE,
                TutoringClassAttendance::STATUS_EXCUSED,
            ], true))->count();

            $lastJoin = $userAtt->max('joined_at');
            $lastJoinAt = $lastJoin ? Carbon::parse($lastJoin) : null;
            $daysSilent = $lastJoinAt ? $lastJoinAt->diffInDays(now()) : (
                $enrollment->enrolled_at ? $enrollment->enrolled_at->diffInDays(now()) : 99
            );

            $attendanceBase = max(1, $completedCount);
            $progress = $completedCount > 0
                ? (int) round(($presentCount / $attendanceBase) * 100)
                : (int) round(($presentCount / $totalPlanned) * 100);

            return (object) [
                'user_id' => $user?->id,
                'name' => $user?->name ?: 'طالب',
                'email' => $user?->email,
                'phone' => $user?->phone,
                'enrolled_at' => $enrollment->enrolled_at,
                'present_count' => $presentCount,
                'completed_sessions' => $completedCount,
                'progress_percent' => min(100, $progress),
                'last_activity_at' => $lastJoinAt,
                'days_silent' => (int) $daysSilent,
                'is_at_risk' => $daysSilent >= 5 || ($completedCount >= 2 && $presentCount === 0),
            ];
        })->values();

        $atRisk = $roster->filter->is_at_risk->sortByDesc('days_silent')->values();

        $todaySessions = $sessions->filter(fn (TutoringClassSession $s) => $s->starts_at?->isToday())->values();
        $upcomingSessions = $sessions
            ->filter(fn (TutoringClassSession $s) => $s->starts_at && $s->starts_at->gte(now()->subHour())
                && $s->status !== TutoringClassSession::STATUS_COMPLETED)
            ->take(6)
            ->values();

        $activeTodayUserIds = $attendances
            ->filter(fn ($a) => $a->joined_at && $a->joined_at->isToday())
            ->pluck('user_id')
            ->unique()
            ->count();

        $avgProgress = $roster->isEmpty()
            ? 0
            : (int) round($roster->avg('progress_percent'));

        return [
            'students_count' => $roster->count(),
            'active_today' => $activeTodayUserIds,
            'average_progress' => $avgProgress,
            'sessions_total' => $sessions->count(),
            'sessions_completed' => $completedCount,
            'sessions_upcoming' => $upcomingSessions->count(),
            'today_sessions' => $todaySessions,
            'upcoming_sessions' => $upcomingSessions,
            'roster' => $roster,
            'at_risk' => $atRisk,
            'sessions' => $sessions,
        ];
    }

    /**
     * Overview cards for instructor cohorts index.
     *
     * @param  Collection<int, TutoringGroupCohort>  $cohorts
     */
    public function summarizeMany(Collection $cohorts, User $instructor): array
    {
        $ids = $cohorts->pluck('id');
        $students = 0;
        $atRiskApprox = 0;

        if ($ids->isNotEmpty() && Schema::hasTable('tutoring_cohort_enrollments')) {
            $students = TutoringCohortEnrollment::query()
                ->whereIn('tutoring_group_cohort_id', $ids)
                ->where('status', TutoringCohortEnrollment::STATUS_ACTIVE)
                ->count();
        }

        $liveToday = 0;
        if ($ids->isNotEmpty() && Schema::hasTable('tutoring_class_sessions')) {
            $liveToday = TutoringClassSession::query()
                ->whereIn('tutoring_group_cohort_id', $ids)
                ->where('status', '!=', TutoringClassSession::STATUS_CANCELLED)
                ->whereDate('starts_at', now()->toDateString())
                ->count();
        }

        return [
            'cohorts_count' => $cohorts->count(),
            'students_count' => $students,
            'sessions_today' => $liveToday,
            'instructor_name' => $instructor->name,
        ];
    }
}
