<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PrivateLessonThread;
use App\Models\StudentReception;
use App\Services\PrivateCoursesCoreService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PrivateCoursesOpsController extends Controller
{
    public function threads(): View
    {
        $threads = PrivateLessonThread::query()
            ->where('admin_visible', true)
            ->with(['student:id,name', 'instructor:id,name'])
            ->orderByDesc('last_message_at')
            ->paginate(30);

        return view('admin.private-courses.threads', compact('threads'));
    }

    public function showThread(PrivateLessonThread $thread): View
    {
        $thread->load(['student:id,name', 'instructor:id,name', 'messages' => function ($q) {
            $q->orderBy('created_at')->with('sender:id,name');
        }]);

        return view('admin.private-courses.thread-show', compact('thread'));
    }

    public function reply(Request $request, PrivateLessonThread $thread)
    {
        $data = $request->validate([
            'body' => 'required|string|max:5000',
            'is_internal_note' => 'nullable|boolean',
        ]);

        PrivateCoursesCoreService::postMessage(
            $thread,
            Auth::user(),
            $data['body'],
            (bool) ($data['is_internal_note'] ?? false)
        );

        return back()->with('success', 'تم إرسال الرد.');
    }

    public function receptions(): View
    {
        $receptions = StudentReception::query()
            ->with(['student:id,name', 'instructor:id,name', 'handledBy:id,name'])
            ->orderByRaw("CASE WHEN status = 'pending' THEN 0 WHEN status = 'welcomed' THEN 1 ELSE 2 END")
            ->orderByDesc('created_at')
            ->paginate(30);

        return view('admin.private-courses.receptions', compact('receptions'));
    }

    public function updateReception(Request $request, StudentReception $reception)
    {
        $data = $request->validate([
            'status' => 'required|in:pending,welcomed,completed',
            'notes' => 'nullable|string|max:2000',
        ]);

        $reception->status = $data['status'];
        $reception->notes = $data['notes'] ?? $reception->notes;
        $reception->handled_by = Auth::id();
        if ($data['status'] === StudentReception::STATUS_WELCOMED && ! $reception->welcomed_at) {
            $reception->welcomed_at = now();
        }
        if ($data['status'] === StudentReception::STATUS_COMPLETED) {
            $reception->completed_at = now();
        }
        $reception->save();

        return back()->with('success', 'تم تحديث حالة الاستقبال.');
    }
}
