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
    public function index(Request $request): View
    {
        $user = Auth::user();
        $q = trim((string) $request->query('q', ''));

        $sessions = OneToOneSession::query()
            ->where('student_id', $user->id)
            ->where(function ($query) {
                $query->where('is_private_lecture', true)
                    ->orWhereNull('is_private_lecture')
                    ->orWhere('system_channel', 'private_courses');
            })
            ->with(['course:id,title', 'instructor:id,name,profile_image', 'classroomMeeting'])
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($inner) use ($q) {
                    $inner->whereHas('course', fn ($cq) => $cq->where('title', 'like', '%'.$q.'%'))
                        ->orWhereHas('instructor', fn ($iq) => $iq->where('name', 'like', '%'.$q.'%'));
                });
            })
            ->orderByRaw("CASE WHEN status = 'scheduled' THEN 0 WHEN status = 'pending_schedule' THEN 1 ELSE 2 END")
            ->orderBy('scheduled_at')
            ->paginate(20)
            ->withQueryString();

        $threads = PrivateLessonThread::query()
            ->where('student_id', $user->id)
            ->with(['instructor:id,name'])
            ->orderByDesc('last_message_at')
            ->limit(8)
            ->get();

        $reception = StudentReception::query()->where('student_id', $user->id)->first();

        $nextJoinable = OneToOneSession::query()
            ->where('student_id', $user->id)
            ->where(function ($query) {
                $query->where('is_private_lecture', true)
                    ->orWhereNull('is_private_lecture')
                    ->orWhere('system_channel', 'private_courses');
            })
            ->where('status', OneToOneSession::STATUS_SCHEDULED)
            ->whereNotNull('scheduled_at')
            ->where('scheduled_at', '<=', now()->addMinutes(30))
            ->where('scheduled_at', '>=', now()->subMinutes(50))
            ->with(['course:id,title', 'instructor:id,name', 'classroomMeeting'])
            ->orderBy('scheduled_at')
            ->first();

        $upcomingCount = OneToOneSession::query()
            ->where('student_id', $user->id)
            ->where(function ($query) {
                $query->where('is_private_lecture', true)
                    ->orWhereNull('is_private_lecture')
                    ->orWhere('system_channel', 'private_courses');
            })
            ->whereIn('status', [OneToOneSession::STATUS_SCHEDULED, OneToOneSession::STATUS_PENDING])
            ->count();

        return view('student.private-lectures.index', [
            'sessions' => $sessions,
            'threads' => $threads,
            'reception' => $reception,
            'nextJoinable' => $nextJoinable,
            'upcomingCount' => $upcomingCount,
            'searchQuery' => $q,
        ]);
    }

    public function messagesIndex(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));

        $threads = PrivateLessonThread::query()
            ->where('student_id', Auth::id())
            ->with([
                'instructor:id,name,profile_image',
                'messages' => fn ($query) => $query
                    ->where('is_internal_note', false)
                    ->orderByDesc('id')
                    ->limit(1),
            ])
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($inner) use ($q) {
                    $inner->where('subject', 'like', '%'.$q.'%')
                        ->orWhereHas('instructor', fn ($iq) => $iq->where('name', 'like', '%'.$q.'%'));
                });
            })
            ->orderByDesc('last_message_at')
            ->paginate(20)
            ->withQueryString();

        return view('student.private-lectures.messages-index', [
            'threads' => $threads,
            'searchQuery' => $q,
        ]);
    }

    public function messages(PrivateLessonThread $thread): View
    {
        $user = Auth::user();
        abort_unless((int) $thread->student_id === (int) $user->id, 403);

        $thread->load(['instructor:id,name,profile_image', 'messages.sender:id,name,profile_image']);

        $otherThreads = PrivateLessonThread::query()
            ->where('student_id', $user->id)
            ->where('id', '!=', $thread->id)
            ->with(['instructor:id,name'])
            ->orderByDesc('last_message_at')
            ->limit(8)
            ->get();

        return view('student.private-lectures.messages', [
            'thread' => $thread,
            'otherThreads' => $otherThreads,
        ]);
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
