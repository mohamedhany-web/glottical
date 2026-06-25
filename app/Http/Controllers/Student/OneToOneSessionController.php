<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\OneToOneSession;
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

        return view('student.one-to-one-sessions.show', ['session' => $oneToOneSession]);
    }
}
