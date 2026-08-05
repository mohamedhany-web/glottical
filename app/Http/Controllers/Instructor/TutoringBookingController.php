<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\TutoringGroup;
use App\Models\TutoringGroupBooking;
use App\Models\TutoringGroupCohort;
use App\Services\TutoringGroupOrchestrationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TutoringBookingController extends Controller
{
    public function index(Request $request): View
    {
        $bookings = TutoringGroupBooking::query()
            ->where('instructor_id', $request->user()->id)
            ->with(['tutoringGroup:id,title,type,academic_year_id', 'tutoringGroup.schoolYear:id,name', 'user:id,name', 'classroomMeeting:id,code', 'cohort:id,title'])
            ->orderByDesc('starts_at')
            ->paginate(20);

        $stats = [
            'upcoming' => TutoringGroupBooking::query()
                ->where('instructor_id', $request->user()->id)
                ->where('status', TutoringGroupBooking::STATUS_CONFIRMED)
                ->where('starts_at', '>=', now())
                ->count(),
            'pending' => TutoringGroupBooking::query()
                ->where('instructor_id', $request->user()->id)
                ->where('status', TutoringGroupBooking::STATUS_PENDING)
                ->count(),
        ];

        return view('instructor.tutoring-bookings.index', compact('bookings', 'stats'));
    }

    public function show(Request $request, TutoringGroupBooking $booking): View
    {
        abort_unless((int) $booking->instructor_id === (int) $request->user()->id, 403);
        $booking->load(['tutoringGroup.schoolYear', 'tutoringGroup.schoolSubject', 'user', 'classroomMeeting', 'cohort', 'package']);

        return view('instructor.tutoring-bookings.show', compact('booking'));
    }

    public function complete(Request $request, TutoringGroupBooking $booking): RedirectResponse
    {
        abort_unless((int) $booking->instructor_id === (int) $request->user()->id, 403);

        if ($booking->starts_at && $booking->starts_at->isFuture()) {
            return back()->with('error', 'لا يمكن إكمال الحصة قبل موعد بدايتها.');
        }

        try {
            TutoringGroupOrchestrationService::completeBooking($booking);
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'تم إكمال الحصة وخصم وحدة واحدة من رصيد الطالب وإغلاق غرفة Live.');
    }
}
