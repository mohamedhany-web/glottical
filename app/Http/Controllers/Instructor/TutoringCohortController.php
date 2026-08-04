<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\TutoringGroup;
use App\Models\TutoringGroupCohort;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TutoringCohortController extends Controller
{
    public function index(Request $request): View
    {
        $groupIds = TutoringGroup::query()
            ->where('instructor_id', $request->user()->id)
            ->collective()
            ->pluck('id');

        $cohorts = TutoringGroupCohort::query()
            ->whereIn('tutoring_group_id', $groupIds)
            ->with(['tutoringGroup:id,title,school_year_id', 'tutoringGroup.schoolYear:id,name,level_number'])
            ->withCount(['bookings as confirmed_bookings' => fn ($q) => $q->whereIn('status', ['pending', 'confirmed'])])
            ->orderByDesc('starts_at')
            ->paginate(20);

        return view('instructor.tutoring-cohorts.index', compact('cohorts'));
    }

    public function show(Request $request, TutoringGroupCohort $cohort): View
    {
        $cohort->load([
            'tutoringGroup.schoolYear:id,name,level_number',
            'tutoringGroup.schoolSubject:id,name',
            'bookings' => fn ($q) => $q->with('user:id,name,email,phone')->orderByDesc('starts_at'),
        ]);
        abort_unless((int) $cohort->tutoringGroup?->instructor_id === (int) $request->user()->id, 403);

        return view('instructor.tutoring-cohorts.show', compact('cohort'));
    }
}
