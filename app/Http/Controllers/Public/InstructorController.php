<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\AdvancedCourse;
use App\Models\User;
use App\Models\InstructorProfile;
use App\Models\ConsultationSetting;

class InstructorController extends Controller
{
    public function index()
    {
        $profiles = InstructorProfile::query()
            ->approved()
            ->whereHas('user', function ($q) {
                $q->whereIn('role', ['instructor', 'teacher'])
                    ->where('is_active', true);
            })
            ->with(['user:id,name,role,is_active'])
            ->orderByDesc('reviewed_at')
            ->orderByDesc('id')
            ->get();

        $courseCounts = AdvancedCourse::query()
            ->where('is_active', true)
            ->whereIn('instructor_id', $profiles->pluck('user_id')->filter()->unique()->values())
            ->selectRaw('instructor_id, COUNT(*) as aggregate')
            ->groupBy('instructor_id')
            ->pluck('aggregate', 'instructor_id');

        $profiles->each(function (InstructorProfile $profile) use ($courseCounts) {
            $profile->setAttribute('courses_count', (int) ($courseCounts[$profile->user_id] ?? 0));
        });

        $consultationSetting = ConsultationSetting::current();

        $instructorIds = $profiles->pluck('user_id')->filter()->unique()->values();
        $featuredCourses = $instructorIds->isEmpty()
            ? collect()
            : AdvancedCourse::query()
                ->where('is_active', true)
                ->whereIn('instructor_id', $instructorIds)
                ->with(['instructor:id,name', 'courseCategory:id,name'])
                ->withCount('lessons')
                ->orderByDesc('is_featured')
                ->orderByDesc('created_at')
                ->limit(8)
                ->get();

        return view('instructors.index', compact('profiles', 'consultationSetting', 'featuredCourses'));
    }

    public function show(User $instructor)
    {
        if (!$instructor->isInstructor()) {
            abort(404);
        }
        $profile = InstructorProfile::where('user_id', $instructor->id)->approved()->with('user')->firstOrFail();
        $courses = AdvancedCourse::where('instructor_id', $instructor->id)
            ->where('is_active', true)
            ->withCount('lessons')
            ->orderByDesc('is_featured')
            ->orderByDesc('created_at')
            ->get();

        $groupCourses = $courses->filter(fn ($c) => ! $c->isOneToOne())->values();
        $oneToOneCourses = $courses->filter(fn ($c) => $c->isOneToOne())->values();

        $consultationSetting = ConsultationSetting::current();

        return view('instructors.show', compact(
            'profile',
            'courses',
            'groupCourses',
            'oneToOneCourses',
            'consultationSetting'
        ));
    }
}
