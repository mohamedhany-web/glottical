<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\OneToOneSession;
use App\Models\PrivateLessonThread;
use App\Models\StudentReception;
use App\Services\PrivateCoursesCoreService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * محاضرات خاصة + رسائل مع المعلم + استقبال — منفصلة عن المجموعات والمدرسة.
 */
class PrivateLecturesController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        $sessions = OneToOneSession::query()
            ->where('student_id', $user->id)
            ->where(function ($q) {
                $q->where('is_private_lecture', true)
                    ->orWhereNull('is_private_lecture')
                    ->orWhere('system_channel', 'private_courses');
            })
            ->with(['course:id,title', 'instructor:id,name', 'classroomMeeting'])
            ->orderByRaw("CASE WHEN status = 'scheduled' THEN 0 WHEN status = 'pending_schedule' THEN 1 ELSE 2 END")
            ->orderBy('scheduled_at')
            ->paginate(20);

        $threads = PrivateLessonThread::query()
            ->where('student_id', $user->id)
            ->with(['instructor:id,name'])
            ->orderByDesc('last_message_at')
            ->limit(10)
            ->get();

        $reception = StudentReception::query()->where('student_id', $user->id)->first();

        return view('student.private-lectures.index', compact('sessions', 'threads', 'reception'));
    }

    public function messagesIndex(): View
    {
        $threads = PrivateLessonThread::query()
            ->where('student_id', Auth::id())
            ->with(['instructor:id,name'])
            ->orderByDesc('last_message_at')
            ->paginate(20);

        return view('student.private-lectures.messages-index', compact('threads'));
    }

    public function messages(PrivateLessonThread $thread): View
    {
        $user = Auth::user();
        abort_unless((int) $thread->student_id === (int) $user->id, 403);

        $thread->load(['instructor:id,name', 'messages.sender:id,name']);

        return view('student.private-lectures.messages', compact('thread'));
    }

    public function sendMessage(Request $request, PrivateLessonThread $thread)
    {
        $user = Auth::user();
        abort_unless((int) $thread->student_id === (int) $user->id, 403);

        $data = $request->validate([
            'body' => 'required|string|max:5000',
        ]);

        PrivateCoursesCoreService::postMessage($thread, $user, $data['body']);

        return back()->with('success', 'تم إرسال الرسالة.');
    }
}
