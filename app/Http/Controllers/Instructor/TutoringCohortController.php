<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\TutoringGroup;
use App\Models\TutoringGroupCohort;
use App\Services\InstructorCohortCommandCenterService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TutoringCohortController extends Controller
{
    public function index(Request $request, InstructorCohortCommandCenterService $commandCenter): View
    {
        $groupIds = TutoringGroup::query()
            ->where('instructor_id', $request->user()->id)
            ->collective()
            ->pluck('id');

        $cohorts = TutoringGroupCohort::query()
            ->whereIn('tutoring_group_id', $groupIds)
            ->with([
                'tutoringGroup:id,title,academic_year_id,academic_subject_id',
                'tutoringGroup.schoolYear:id,name,level_number',
                'tutoringGroup.schoolSubject:id,name',
            ])
            ->withCount([
                'activeEnrollments as students_count',
                'bookings as confirmed_bookings' => fn ($q) => $q->whereIn('status', ['pending', 'confirmed']),
            ])
            ->orderByDesc('starts_at')
            ->paginate(20);

        $overview = $commandCenter->summarizeMany($cohorts->getCollection(), $request->user());

        return view('instructor.tutoring-cohorts.index', compact('cohorts', 'overview'));
    }

    public function show(Request $request, TutoringGroupCohort $cohort, InstructorCohortCommandCenterService $commandCenter): View
    {
        $cohort->load([
            'tutoringGroup.schoolYear:id,name,level_number',
            'tutoringGroup.schoolSubject:id,name',
            'tutoringGroup.instructor:id,name',
        ]);
        abort_unless((int) $cohort->tutoringGroup?->instructor_id === (int) $request->user()->id, 403);

        $center = $commandCenter->build($cohort);
        $feedPosts = \App\Services\ClassFeedService::postsFor($cohort, $request->user(), 20);

        return view('instructor.tutoring-cohorts.show', [
            'cohort' => $cohort,
            'center' => $center,
            'feedPosts' => $feedPosts,
            'canModerateFeed' => true,
        ]);
    }
}
