<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\InstructorProfile;
use App\Models\ConsultationSetting;
use App\Services\InstructorMarketingRankingService;

class InstructorController extends Controller
{
    public function index()
    {
        $profiles = InstructorMarketingRankingService::rankApprovedProfiles();
        $consultationSetting = ConsultationSetting::current();

        $instructorIds = $profiles->pluck('user_id')->filter()->unique()->values();
        $featuredCourses = $instructorIds->isEmpty()
            ? collect()
            : \App\Models\AdvancedCourse::query()
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
        $courses = \App\Models\AdvancedCourse::where('instructor_id', $instructor->id)
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
