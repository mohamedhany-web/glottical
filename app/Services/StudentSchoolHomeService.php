<?php

namespace App\Services;

use App\Models\AcademicYear;
use App\Models\FreeTrialBooking;
use App\Models\StudentServiceEntitlement;
use App\Models\TutoringClassAttendance;
use App\Models\TutoringClassSession;
use App\Models\TutoringCohortEnrollment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

class StudentSchoolHomeService
{
    /**
     * @return array{
     *     greeting: string,
     *     primaryClass: ?object,
     *     classes: Collection,
     *     todayMission: ?object,
     *     upcoming: Collection,
     *     progress: array{percent: int, attended: int, completed_sessions: int, total_sessions: int, label: string},
     *     credits: array{total_left: int, entitlements: Collection},
     *     weekDays: Collection,
     *     todayItems: Collection,
     *     nextAppointment: ?object,
     *     recommendedYear: ?AcademicYear,
     *     placement: ?FreeTrialBooking,
     *     hasSchoolLife: bool
     * }
     */
    public function build(User $user, array $filters = []): array
    {
        $isRtl = app()->getLocale() === 'ar';
        $view = in_array($filters['view'] ?? '', ['week', 'day'], true) ? $filters['view'] : 'week';
        $sort = in_array($filters['sort'] ?? '', ['classes', 'progress', 'name'], true) ? $filters['sort'] : 'classes';
        $search = trim((string) ($filters['q'] ?? ''));
        $tz = config('app.timezone', 'Africa/Cairo');
        $weekAnchor = ! empty($filters['week'])
            ? Carbon::parse((string) $filters['week'], $tz)->startOfDay()
            : now($tz)->startOfDay();
        $weekStart = $weekAnchor->copy()->startOfWeek(Carbon::SATURDAY);

        $hour = (int) now()->format('G');
        $greeting = $hour < 12
            ? ($isRtl ? 'صباح الخير' : 'Good morning')
            : ($hour < 17
                ? ($isRtl ? 'مساء الخير' : 'Good afternoon')
                : ($isRtl ? 'مساء الخير' : 'Good evening'));

        $enrollments = collect();
        if (Schema::hasTable('tutoring_cohort_enrollments')) {
            $enrollments = TutoringCohortEnrollment::query()
                ->with([
                    'cohort.tutoringGroup.instructor:id,name',
                    'cohort.tutoringGroup.schoolYear:id,name,slug,level_number,tagline',
                    'cohort.tutoringGroup.schoolSubject:id,name',
                ])
                ->where('user_id', $user->id)
                ->where('status', TutoringCohortEnrollment::STATUS_ACTIVE)
                ->latest('enrolled_at')
                ->get();
        }

        $cohortIds = $enrollments->pluck('tutoring_group_cohort_id')->filter()->values();

        $sessionsByCohort = collect();
        $upcomingSessions = collect();
        if ($cohortIds->isNotEmpty() && Schema::hasTable('tutoring_class_sessions')) {
            $allSessions = TutoringClassSession::query()
                ->with(['cohort:id,title', 'tutoringGroup:id,title', 'classroomMeeting:id,code'])
                ->whereIn('tutoring_group_cohort_id', $cohortIds)
                ->where('status', '!=', TutoringClassSession::STATUS_CANCELLED)
                ->orderBy('starts_at')
                ->get();

            $sessionsByCohort = $allSessions->groupBy('tutoring_group_cohort_id');
            $upcomingSessions = $allSessions
                ->filter(fn (TutoringClassSession $s) => $s->starts_at && $s->starts_at->gte(now()->subHour()))
                ->values();
        }

        $attendanceBySession = collect();
        if ($cohortIds->isNotEmpty() && Schema::hasTable('tutoring_class_attendances')) {
            $sessionIds = $sessionsByCohort->flatten()->pluck('id');
            $attendanceBySession = TutoringClassAttendance::query()
                ->where('user_id', $user->id)
                ->whereIn('tutoring_class_session_id', $sessionIds)
                ->get()
                ->keyBy('tutoring_class_session_id');
        }

        $classes = $enrollments->map(function (TutoringCohortEnrollment $enrollment) use ($sessionsByCohort, $attendanceBySession, $isRtl) {
            $cohort = $enrollment->cohort;
            if (! $cohort) {
                return null;
            }

            $sessions = $sessionsByCohort->get($cohort->id, collect());
            $completed = $sessions->where('status', TutoringClassSession::STATUS_COMPLETED);
            $attended = $completed->filter(function (TutoringClassSession $session) use ($attendanceBySession) {
                $row = $attendanceBySession->get($session->id);

                return $row && in_array($row->status, [
                    TutoringClassAttendance::STATUS_PRESENT,
                    TutoringClassAttendance::STATUS_LATE,
                    TutoringClassAttendance::STATUS_EXCUSED,
                ], true);
            })->count();

            $total = max(1, (int) ($cohort->sessions_count ?: $sessions->count() ?: 1));
            $done = $completed->count();
            $percent = (int) round(($done / $total) * 100);

            $next = $sessions
                ->filter(fn (TutoringClassSession $s) => $s->starts_at && $s->starts_at->gte(now()->subMinutes(30))
                    && $s->status !== TutoringClassSession::STATUS_COMPLETED)
                ->sortBy('starts_at')
                ->first();

            $group = $cohort->tutoringGroup;
            $year = $group?->schoolYear;
            $subject = $group?->schoolSubject;

            return (object) [
                'enrollment_id' => $enrollment->id,
                'cohort_id' => $cohort->id,
                'title' => $cohort->title ?: ($group?->title ?: ($isRtl ? 'فصل' : 'Class')),
                'group_title' => $group?->title,
                'year_name' => $year?->name,
                'subject_name' => $subject?->name,
                'instructor_name' => $group?->instructor?->name,
                'schedule' => $cohort->scheduleSummary(),
                'progress_percent' => min(100, $percent),
                'attended' => $attended,
                'completed_sessions' => $done,
                'total_sessions' => $total,
                'next_session' => $next,
                'url' => Route::has('student.classes.show')
                    ? route('student.classes.show', $cohort)
                    : '#',
            ];
        })->filter()->values();

        if ($search !== '') {
            $needle = mb_strtolower($search);
            $classes = $classes->filter(function ($class) use ($needle) {
                return str_contains(mb_strtolower((string) $class->title), $needle)
                    || str_contains(mb_strtolower((string) ($class->subject_name ?? '')), $needle)
                    || str_contains(mb_strtolower((string) ($class->group_title ?? '')), $needle);
            })->values();
        }

        $classes = match ($sort) {
            'progress' => $classes->sortByDesc('progress_percent')->values(),
            'name' => $classes->sortBy(fn ($class) => mb_strtolower((string) ($class->subject_name ?: $class->title)))->values(),
            default => $classes,
        };

        $primaryClass = $classes->first();

        $todayMission = null;
        $nextSession = $upcomingSessions->first();
        if ($nextSession instanceof TutoringClassSession) {
            $mins = max(1, (int) ($nextSession->starts_at && $nextSession->ends_at
                ? $nextSession->starts_at->diffInMinutes($nextSession->ends_at)
                : 60));
            $joinable = $nextSession->isJoinable();
            $todayMission = (object) [
                'session_id' => $nextSession->id,
                'title' => $nextSession->displayTitle(),
                'subtitle' => $nextSession->cohort?->title
                    ?: ($nextSession->tutoringGroup?->title ?: ($isRtl ? 'حصة جماعية' : 'Class session')),
                'starts_at' => $nextSession->starts_at,
                'duration_minutes' => $mins,
                'is_today' => $nextSession->starts_at?->isToday() ?? false,
                'is_joinable' => $joinable,
                'status' => $nextSession->status,
                'join_url' => Route::has('student.schedule.join')
                    ? route('student.schedule.join', ['type' => 'class', 'id' => $nextSession->id])
                    : ($nextSession->joinUrl() ?: '#'),
                'class_url' => $nextSession->cohort && Route::has('student.classes.show')
                    ? route('student.classes.show', $nextSession->cohort)
                    : null,
            ];
        }

        $attendedTotal = $classes->sum('attended');
        $completedTotal = $classes->sum('completed_sessions');
        $sessionsTotal = max(1, (int) $classes->sum('total_sessions'));
        $progressPercent = $classes->isEmpty()
            ? 0
            : (int) round($classes->avg('progress_percent'));

        $entitlements = collect();
        $creditsLeft = 0;
        if (Schema::hasTable('student_service_entitlements')) {
            $entitlements = StudentServiceEntitlement::query()
                ->with(['servicePackage:id,name', 'academicYear:id,name'])
                ->where('user_id', $user->id)
                ->where('status', StudentServiceEntitlement::STATUS_ACTIVE)
                ->orderByDesc('id')
                ->limit(6)
                ->get();
            $creditsLeft = (int) $entitlements->sum(fn (StudentServiceEntitlement $e) => $e->unitsLeft());
        }

        $weekDays = StudentScheduleService::weekDays($user, $weekStart);
        $focusDay = $weekDays->first(fn ($day) => $day->date->toDateString() === $weekAnchor->toDateString())
            ?? $weekDays->firstWhere('is_today')
            ?? $weekDays->first();
        $todayItems = $weekDays->firstWhere('is_today')?->items ?? collect();

        $scheduleRows = $view === 'day' && $focusDay
            ? collect($focusDay->items ?? [])->take(6)
            : $weekDays
                ->flatMap(function ($day) {
                    return collect($day->items ?? [])->map(function ($slot) use ($day) {
                        $slot->day_short = $day->short;

                        return $slot;
                    });
                })
                ->sortBy(fn ($slot) => $slot->starts_at?->timestamp ?? 0)
                ->values()
                ->take(6);

        $timelineQuery = function (array $overrides = []) use ($weekAnchor, $view, $sort, $search): string {
            $params = array_filter([
                'week' => $overrides['week'] ?? $weekAnchor->toDateString(),
                'view' => $overrides['view'] ?? $view,
                'sort' => $overrides['sort'] ?? $sort,
                'q' => array_key_exists('q', $overrides) ? $overrides['q'] : ($search !== '' ? $search : null),
                'lang' => request()->query('lang'),
            ], fn ($value) => $value !== null && $value !== '');

            if (($params['view'] ?? '') === 'week') {
                unset($params['view']);
            }
            if (($params['sort'] ?? '') === 'classes') {
                unset($params['sort']);
            }
            if (($params['week'] ?? '') === now(config('app.timezone', 'Africa/Cairo'))->toDateString()) {
                unset($params['week']);
            }

            $query = http_build_query($params);

            return request()->url().($query !== '' ? '?'.$query : '');
        };

        $nextDate = $view === 'day'
            ? $weekAnchor->copy()->addDay()->toDateString()
            : $weekAnchor->copy()->addWeek()->toDateString();
        $prevDate = $view === 'day'
            ? $weekAnchor->copy()->subDay()->toDateString()
            : $weekAnchor->copy()->subWeek()->toDateString();
        $nextAppointment = $weekDays->flatMap->items
            ->filter(fn ($a) => $a->starts_at && $a->starts_at->gte(now()->subMinutes(30)))
            ->sortBy('starts_at')
            ->first();

        $placement = null;
        if (Schema::hasTable('free_trial_bookings')) {
            $placement = FreeTrialBooking::query()
                ->where(function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                    if ($user->email) {
                        $q->orWhere('email', $user->email);
                    }
                })
                ->with('recommendedSchoolYear:id,name,slug,level_number,tagline')
                ->orderByDesc('starts_at')
                ->first();
        }

        $recommendedYear = $placement?->recommendedSchoolYear;
        if (! $recommendedYear && $primaryClass?->year_name) {
            $yearId = $enrollments->first()?->cohort?->tutoringGroup?->academic_year_id;
            if ($yearId && Schema::hasTable('academic_years')) {
                $recommendedYear = AcademicYear::query()->find($yearId);
            }
        }

        return [
            'greeting' => $greeting,
            'primaryClass' => $primaryClass,
            'classes' => $classes,
            'todayMission' => $todayMission,
            'upcoming' => $upcomingSessions->take(5),
            'progress' => [
                'percent' => min(100, $progressPercent),
                'attended' => (int) $attendedTotal,
                'completed_sessions' => (int) $completedTotal,
                'total_sessions' => (int) $sessionsTotal,
                'label' => $isRtl
                    ? ($progressPercent.'% من مسار فصلك')
                    : ($progressPercent.'% of your class path'),
            ],
            'credits' => [
                'total_left' => $creditsLeft,
                'entitlements' => $entitlements,
            ],
            'weekDays' => $weekDays,
            'todayItems' => $todayItems,
            'scheduleRows' => $scheduleRows,
            'weekAnchor' => $weekAnchor,
            'weekStart' => $weekStart,
            'viewMode' => $view,
            'sortMode' => $sort,
            'searchQuery' => $search,
            'focusDay' => $focusDay,
            'timelinePrevUrl' => $timelineQuery(['week' => $prevDate]),
            'timelineNextUrl' => $timelineQuery(['week' => $nextDate]),
            'timelineSortUrl' => $timelineQuery(['sort' => $sort === 'classes' ? 'progress' : 'classes']),
            'timelineViewUrl' => $timelineQuery(['view' => $view === 'week' ? 'day' : 'week']),
            'timelineTodayUrl' => $timelineQuery(['week' => now($tz)->toDateString(), 'view' => $view === 'day' ? 'day' : null]),
            'timelineMonthPrevUrl' => $timelineQuery([
                'week' => $weekAnchor->copy()->subMonthNoOverflow()->startOfMonth()->toDateString(),
            ]),
            'timelineMonthNextUrl' => $timelineQuery([
                'week' => $weekAnchor->copy()->addMonthNoOverflow()->startOfMonth()->toDateString(),
            ]),
            'nextAppointment' => $nextAppointment,
            'recommendedYear' => $recommendedYear,
            'placement' => $placement,
            'hasSchoolLife' => $classes->isNotEmpty() || $creditsLeft > 0,
            'game' => StudentSchoolGameService::profileSnapshot($user),
        ];
    }
}
