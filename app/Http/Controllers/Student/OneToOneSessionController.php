<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\OneToOneSession;
use App\Services\OneToOneAvailabilityService;
use App\Services\OneToOneSessionService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OneToOneSessionController extends Controller
{
    public function index(Request $request): View
    {
        $sessions = OneToOneSession::query()
            ->where('student_id', $request->user()->id)
            ->with(['course', 'instructor', 'classroomMeeting'])
            ->orderByRaw("CASE status WHEN 'scheduled' THEN 0 WHEN 'pending_schedule' THEN 1 ELSE 2 END")
            ->orderBy('scheduled_at')
            ->orderBy('session_number')
            ->paginate(20);

        return view('student.one-to-one-sessions.index', compact('sessions'));
    }

    public function show(OneToOneSession $oneToOneSession): View
    {
        abort_unless($oneToOneSession->student_id === auth()->id(), 403);

        $oneToOneSession->load(['course', 'instructor', 'classroomMeeting', 'enrollment']);

        $availableSlots = collect();
        if ($oneToOneSession->status === OneToOneSession::STATUS_PENDING) {
            $availableSlots = OneToOneAvailabilityService::availableSlots(
                (int) $oneToOneSession->instructor_id,
                now(),
                now()->addWeeks(4),
                (int) ($oneToOneSession->duration_minutes ?? 60)
            );
        }

        return view('student.one-to-one-sessions.show', [
            'session' => $oneToOneSession,
            'availableSlots' => $availableSlots,
        ]);
    }

    public function book(Request $request, OneToOneSession $oneToOneSession): RedirectResponse
    {
        abort_unless($oneToOneSession->student_id === auth()->id(), 403);
        abort_unless($oneToOneSession->status === OneToOneSession::STATUS_PENDING, 422);

        $data = $request->validate([
            'scheduled_at' => ['required', 'date', 'after:now'],
        ]);

        try {
            OneToOneSessionService::scheduleSession(
                $oneToOneSession,
                Carbon::parse($data['scheduled_at']),
                (int) ($oneToOneSession->duration_minutes ?? 60),
                $request->user(),
                requireAvailability: true
            );
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['scheduled_at' => $e->getMessage()]);
        }

        return redirect()
            ->route('student.one-to-one-sessions.show', $oneToOneSession)
            ->with('success', __('student.one_to_one_booking_success'));
    }
}
