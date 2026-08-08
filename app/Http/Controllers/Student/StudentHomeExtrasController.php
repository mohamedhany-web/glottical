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
                    ->where(function ($q) {
                        $q->where('is_visible_to_student', true)
                            ->orWhereNull('is_visible_to_student');
                    })
                    ->orderBy('sort_order')
                    ->latest('id')
                    ->limit(100)
                    ->get();
            }
        }

        return view('student.library.materials', compact('materials'));
    }

    public function videos(Request $request): View|RedirectResponse
    {
        if (! Schema::hasTable('live_recordings') || ! Schema::hasTable('live_sessions')) {
            return view('student.library.videos', ['videos' => collect()]);
        }

        $user = $request->user();
        $enrolledCourseIds = collect();
        if (Schema::hasTable('student_course_enrollments')) {
            $enrolledCourseIds = DB::table('student_course_enrollments')
                ->where('user_id', $user->id)
                ->when(Schema::hasColumn('student_course_enrollments', 'status'), fn ($q) => $q->where('status', 'active'))
                ->pluck('advanced_course_id');
        }

        $sessionIds = LiveSession::query()
            ->where('status', 'ended')
            ->where(function ($q) use ($enrolledCourseIds) {
                $q->whereIn('course_id', $enrolledCourseIds)
                    ->orWhere('require_enrollment', false)
                    ->orWhereNull('course_id');
            })
            ->pluck('id');

        $videos = LiveRecording::query()
            ->with(['session.course', 'session.instructor'])
            ->whereIn('session_id', $sessionIds)
            ->where('status', 'ready')
            ->where('is_published', true)
            ->latest('id')
            ->paginate(24);

        return view('student.library.videos', compact('videos'));
    }

    public function lectures(Request $request): View
    {
        $user = $request->user();
        $private = collect();
        $classes = collect();

        if (Schema::hasTable('one_to_one_sessions')) {
            $private = OneToOneSession::query()
                ->with(['course:id,title', 'instructor:id,name', 'classroomMeeting'])
                ->where('student_id', $user->id)
                ->whereIn('status', [OneToOneSession::STATUS_SCHEDULED, OneToOneSession::STATUS_COMPLETED])
                ->orderByDesc('scheduled_at')
                ->limit(30)
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
                ->orderByDesc('starts_at')
                ->limit(30)
                ->get();
        }

        return view('student.library.lectures', compact('private', 'classes'));
    }
}
