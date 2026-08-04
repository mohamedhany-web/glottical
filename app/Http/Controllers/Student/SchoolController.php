<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\FreeTrialBooking;
use App\Models\SchoolYear;
use App\Models\TutoringGroupBooking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class SchoolController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $bookings = TutoringGroupBooking::query()
            ->where('user_id', $user->id)
            ->whereIn('status', [
                TutoringGroupBooking::STATUS_CONFIRMED,
                TutoringGroupBooking::STATUS_PENDING,
                TutoringGroupBooking::STATUS_COMPLETED,
            ])
            ->with([
                'tutoringGroup:id,title,slug,type,school_year_id,school_subject_id',
                'tutoringGroup.schoolYear:id,name,slug,level_number,tagline',
                'tutoringGroup.schoolSubject:id,name',
                'instructor:id,name',
                'classroomMeeting:id,code,scheduled_for',
                'cohort:id,title',
            ])
            ->orderByDesc('starts_at')
            ->get();

        $schoolBookings = $bookings->filter(fn ($b) => $b->tutoringGroup && $b->tutoringGroup->school_year_id);

        $years = $schoolBookings
            ->map(fn ($b) => $b->tutoringGroup?->schoolYear)
            ->filter()
            ->unique('id')
            ->values();

        $upcoming = $schoolBookings
            ->filter(fn ($b) => $b->status === TutoringGroupBooking::STATUS_CONFIRMED && $b->starts_at && $b->starts_at->gte(now()))
            ->sortBy('starts_at')
            ->values();

        $completedCount = $schoolBookings
            ->filter(fn ($b) => $b->status === TutoringGroupBooking::STATUS_COMPLETED)
            ->count();

        $placement = null;
        if (Schema::hasTable('free_trial_bookings')) {
            $placement = FreeTrialBooking::query()
                ->where(function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                    if ($user->email) {
                        $q->orWhere('email', $user->email);
                    }
                })
                ->with('recommendedSchoolYear:id,name,slug,level_number')
                ->orderByDesc('starts_at')
                ->first();
        }

        $recommendedYear = $placement?->recommendedSchoolYear;
        if (! $recommendedYear && $years->isNotEmpty()) {
            $recommendedYear = $years->first();
        }

        $allYears = Schema::hasTable('school_years')
            ? SchoolYear::query()->active()->ordered()->get()
            : collect();

        return view('student.school.index', [
            'years' => $years,
            'upcoming' => $upcoming,
            'completedCount' => $completedCount,
            'placement' => $placement,
            'recommendedYear' => $recommendedYear,
            'allYears' => $allYears,
            'schoolBookings' => $schoolBookings->take(20),
        ]);
    }
}
