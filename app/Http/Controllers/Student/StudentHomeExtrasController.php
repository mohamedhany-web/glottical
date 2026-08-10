<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Lecture;
use App\Models\LectureMaterial;
use App\Models\LiveRecording;
use App\Models\LiveSession;
use App\Models\OneToOneSession;
use App\Models\TutoringClassSession;
use App\Models\TutoringCohortEnrollment;
use App\Services\StudentScheduleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class StudentHomeExtrasController extends Controller
{
    public function join(Request $request, string $type, int $id): RedirectResponse
    {
        $url = StudentScheduleService::resolveJoinUrl($request->user(), $type, $id);

        if (! $url) {
            return back()->with('error', 'لا يمكن دخول هذه الحصة الآن. تأكد من الاشتراك أو أن الغرفة جاهزة.');
        }

        return redirect()->away($url);
    }

    public function materials(Request $request): View
    {
        $user = $request->user();
        $q = trim((string) $request->query('q', ''));
        $materials = collect();

        if (Schema::hasTable('lecture_materials') && Schema::hasTable('student_course_enrollments')) {
            $courseIds = DB::table('student_course_enrollments')
                ->where('user_id', $user->id)
                ->where('status', 'active')
                ->pluck('advanced_course_id');

            if ($courseIds->isNotEmpty() && Schema::hasTable('lectures')) {
                $lectureIds = Lecture::query()
                    ->whereIn('course_id', $courseIds)
                    ->pluck('id');

                $materials = LectureMaterial::query()
                    ->with('lecture:id,title,course_id')
                    ->whereIn('lecture_id', $lectureIds)
                    ->where(function ($query) {
                        $query->where('is_visible_to_student', true)
                            ->orWhereNull('is_visible_to_student');
                    })
                    ->when($q !== '', function ($query) use ($q) {
                        $query->where(function ($inner) use ($q) {
                            $inner->where('title', 'like', '%'.$q.'%')
                                ->orWhere('file_name', 'like', '%'.$q.'%')
                                ->orWhereHas('lecture', fn ($lq) => $lq->where('title', 'like', '%'.$q.'%'));
                        });
                    })
                    ->orderBy('sort_order')
                    ->latest('id')
                    ->limit(100)
                    ->get();
            }
        }

        return view('student.library.materials', [
            'materials' => $materials,
            'searchQuery' => $q,
        ]);
    }

    public function videos(Request $request): View|RedirectResponse
    {
        $q = trim((string) $request->query('q', ''));

        if (! Schema::hasTable('live_recordings') || ! Schema::hasTable('live_sessions')) {
            return view('student.library.videos', [
                'videos' => collect(),
                'searchQuery' => $q,
            ]);
        }

        $user = $request->user();
        $enrolledCourseIds = collect();
        if (Schema::hasTable('student_course_enrollments')) {
            $enrolledCourseIds = DB::table('student_course_enrollments')
                ->where('user_id', $user->id)
                ->when(Schema::hasColumn('student_course_enrollments', 'status'), fn ($query) => $query->where('status', 'active'))
                ->pluck('advanced_course_id');
        }

        $sessionIds = LiveSession::query()
            ->where('status', 'ended')
            ->where(function ($query) use ($enrolledCourseIds) {
                $query->whereIn('course_id', $enrolledCourseIds)
                    ->orWhere('require_enrollment', false)
                    ->orWhereNull('course_id');
            })
            ->pluck('id');

        $videos = LiveRecording::query()
            ->with(['session.course', 'session.instructor'])
            ->whereIn('session_id', $sessionIds)
            ->where('status', 'ready')
            ->where('is_published', true)
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($inner) use ($q) {
                    $inner->where('title', 'like', '%'.$q.'%')
                        ->orWhereHas('session', function ($sq) use ($q) {
                            $sq->where('title', 'like', '%'.$q.'%')
                                ->orWhereHas('course', fn ($cq) => $cq->where('title', 'like', '%'.$q.'%'))
                                ->orWhereHas('instructor', fn ($iq) => $iq->where('name', 'like', '%'.$q.'%'));
                        });
                });
            })
            ->latest('id')
            ->paginate(24)
            ->withQueryString();

        return view('student.library.videos', [
            'videos' => $videos,
            'searchQuery' => $q,
        ]);
    }

    public function lectures(Request $request): View
    {
        $user = $request->user();
        $q = trim((string) $request->query('q', ''));
        $filter = (string) $request->query('filter', 'all');
        if (! in_array($filter, ['all', 'private', 'classes'], true)) {
            $filter = 'all';
        }

        $private = collect();
        $classes = collect();

        if (Schema::hasTable('one_to_one_sessions')) {
            $private = OneToOneSession::query()
                ->with(['course:id,title', 'instructor:id,name,profile_image', 'classroomMeeting'])
                ->where('student_id', $user->id)
                ->whereIn('status', [OneToOneSession::STATUS_SCHEDULED, OneToOneSession::STATUS_COMPLETED])
                ->when($q !== '', function ($query) use ($q) {
                    $query->where(function ($inner) use ($q) {
                        $inner->whereHas('course', fn ($cq) => $cq->where('title', 'like', '%'.$q.'%'))
                            ->orWhereHas('instructor', fn ($iq) => $iq->where('name', 'like', '%'.$q.'%'));
                    });
                })
                ->orderByDesc('scheduled_at')
                ->limit(40)
                ->get();
        }

        if (Schema::hasTable('tutoring_class_sessions') && Schema::hasTable('tutoring_cohort_enrollments')) {
            $cohortIds = TutoringCohortEnrollment::query()
                ->where('user_id', $user->id)
                ->where('status', TutoringCohortEnrollment::STATUS_ACTIVE)
                ->pluck('tutoring_group_cohort_id');

            $classes = TutoringClassSession::query()
                ->with(['cohort:id,title', 'tutoringGroup:id,title', 'classroomMeeting'])
                ->whereIn('tutoring_group_cohort_id', $cohortIds)
                ->where('status', '!=', TutoringClassSession::STATUS_CANCELLED)
                ->when($q !== '', function ($query) use ($q) {
                    $query->where(function ($inner) use ($q) {
                        $inner->where('title', 'like', '%'.$q.'%')
                            ->orWhereHas('cohort', fn ($cq) => $cq->where('title', 'like', '%'.$q.'%'))
                            ->orWhereHas('tutoringGroup', fn ($gq) => $gq->where('title', 'like', '%'.$q.'%'));
                    });
                })
                ->orderByDesc('starts_at')
                ->limit(40)
                ->get();
        }

        $nextJoinable = null;
        foreach ($classes as $session) {
            if (method_exists($session, 'isJoinable') && $session->isJoinable()) {
                $nextJoinable = (object) [
                    'kind' => 'class',
                    'title' => $session->displayTitle(),
                    'meta' => $session->cohort?->title,
                    'at' => $session->starts_at,
                    'join_url' => route('student.schedule.join', ['type' => 'class', 'id' => $session->id]),
                ];
                break;
            }
        }
        if (! $nextJoinable) {
            foreach ($private as $session) {
                $canJoin = $session->status === OneToOneSession::STATUS_SCHEDULED
                    && $session->scheduled_at
                    && $session->scheduled_at->lte(now()->addMinutes(30))
                    && $session->scheduled_at->gte(now()->subMinutes(50));
                if ($canJoin) {
                    $nextJoinable = (object) [
                        'kind' => 'private',
                        'title' => $session->course?->title ?: (__('student_timeline.private_lesson')),
                        'meta' => $session->instructor?->name,
                        'at' => $session->scheduled_at,
                        'join_url' => route('student.schedule.join', ['type' => 'private', 'id' => $session->id]),
                    ];
                    break;
                }
            }
        }

        return view('student.library.lectures', [
            'private' => $private,
            'classes' => $classes,
            'searchQuery' => $q,
            'filter' => $filter,
            'nextJoinable' => $nextJoinable,
        ]);
    }
}
