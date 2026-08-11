<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\PrivateLessonMessage;
use App\Models\PrivateLessonThread;
use App\Services\PrivateCoursesCoreService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PrivateMessagesController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));

        $threads = PrivateLessonThread::query()
            ->where('instructor_id', Auth::id())
            ->with([
                'student:id,name,profile_image',
                'messages' => fn ($query) => $query
                    ->where('is_internal_note', false)
                    ->orderByDesc('id')
                    ->limit(1),
            ])
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($inner) use ($q) {
                    $inner->where('subject', 'like', '%'.$q.'%')
                        ->orWhereHas('student', fn ($sq) => $sq->where('name', 'like', '%'.$q.'%'));
                });
            })
            ->orderByDesc('last_message_at')
            ->paginate(20)
            ->withQueryString();

        return view('instructor.private-messages.index', [
            'threads' => $threads,
            'searchQuery' => $q,
        ]);
    }

    public function show(PrivateLessonThread $thread): View
    {
        $user = Auth::user();
        abort_unless((int) $thread->instructor_id === (int) $user->id, 403);

        $thread->load(['student:id,name,profile_image', 'messages.sender:id,name,profile_image']);

        // تعليم رسائل الطالب كمقروءة عند فتح المحادثة
        PrivateLessonMessage::query()
            ->where('private_lesson_thread_id', $thread->id)
            ->where('sender_id', '!=', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $otherThreads = PrivateLessonThread::query()
            ->where('instructor_id', $user->id)
            ->where('id', '!=', $thread->id)
            ->with(['student:id,name'])
            ->orderByDesc('last_message_at')
            ->limit(8)
            ->get();

        return view('instructor.private-messages.show', [
            'thread' => $thread,
            'otherThreads' => $otherThreads,
        ]);
    }

    public function send(Request $request, PrivateLessonThread $thread): RedirectResponse
    {
        $user = Auth::user();
        abort_unless((int) $thread->instructor_id === (int) $user->id, 403);

        $data = $request->validate([
            'body' => 'required|string|max:5000',
        ]);

        PrivateCoursesCoreService::postMessage($thread, $user, $data['body']);

        return back()->with('success', app()->getLocale() === 'ar' ? 'تم إرسال الرسالة.' : 'Message sent.');
    }
}
