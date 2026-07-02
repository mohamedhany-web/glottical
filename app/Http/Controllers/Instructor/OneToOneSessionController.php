<?php

namespace App\Http\Controllers\Instructor;

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
        $instructorId = $request->user()->id;

        $sessions = OneToOneSession::query()
            ->where('instructor_id', $instructorId)
            ->with(['course', 'student', 'classroomMeeting', 'enrollment'])
            ->orderByRaw("CASE status WHEN 'pending_schedule' THEN 0 WHEN 'scheduled' THEN 1 ELSE 2 END")
            ->orderBy('scheduled_at')
            ->orderBy('session_number')
            ->paginate(25);

        $students = OneToOneSession::query()
            ->where('instructor_id', $instructorId)
            ->with(['student', 'course'])
            ->get()
            ->groupBy('student_id')
            ->map(function ($group) {
                $first = $group->first();

                return [
                    'student' => $first->student,
                    'course' => $first->course,
                    'pending' => $group->where('status', OneToOneSession::STATUS_PENDING)->count(),
                    'scheduled' => $group->where('status', OneToOneSession::STATUS_SCHEDULED)->count(),
                ];
            })
            ->values();

        return view('instructor.one-to-one-sessions.index', compact('sessions', 'students'));
    }

    public function show(OneToOneSession $oneToOneSession): View
    {
        abort_unless($oneToOneSession->instructor_id === auth()->id(), 403);

        $oneToOneSession->load(['course', 'student', 'classroomMeeting', 'enrollment']);

        return view('instructor.one-to-one-sessions.show', ['session' => $oneToOneSession]);
    }

    public function schedule(Request $request, OneToOneSession $oneToOneSession): RedirectResponse
    {
        abort_unless($oneToOneSession->instructor_id === auth()->id(), 403);

        $data = $request->validate([
            'scheduled_at' => ['required', 'date', 'after:now'],
            'duration_minutes' => ['nullable', 'integer', 'min:30', 'max:180'],
        ]);

        try {
            OneToOneSessionService::scheduleSession(
                $oneToOneSession,
                Carbon::parse($data['scheduled_at']),
                (int) ($data['duration_minutes'] ?? 60),
                $request->user(),
                requireAvailability: true
            );
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['scheduled_at' => $e->getMessage()])->withInput();
        }

        return back()->with('success', 'تم جدولة الحصة وإشعار الطالب.');
    }

    public function complete(OneToOneSession $oneToOneSession): RedirectResponse
    {
        abort_unless($oneToOneSession->instructor_id === auth()->id(), 403);

        OneToOneSessionService::markCompleted($oneToOneSession);

        return back()->with('success', 'تم تسجيل الحصة كمكتملة.');
    }
}
