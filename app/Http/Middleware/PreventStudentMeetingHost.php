<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * يمنع الطالب من إنشاء/إدارة اجتماعات Classroom أو البث — الانضمام فقط من الجدول/الحصص.
 */
class PreventStudentMeetingHost
{
    /** @var list<string> */
    private const BLOCKED_ROUTE_NAMES = [
        'student.classroom.index',
        'student.classroom.create',
        'student.classroom.store',
        'student.classroom.whiteboard',
        'student.classroom.show',
        'student.classroom.edit',
        'student.classroom.update',
        'student.classroom.destroy',
        'student.classroom.start',
        'student.classroom.start-meeting',
        'student.classroom.recording.upload-tab',
        'student.classroom.participant-whiteboard',
        'student.classroom.guest-join',
        'student.classroom.end',
        'student.classroom.recording.upload',
        'student.classroom.recording.presign',
        'student.classroom.recording.complete',
        'student.classroom.recording-audio.presign',
        'student.classroom.recording-audio.upload',
        'student.classroom.recording-audio.complete',
        'student.classroom.ai-report',
        'student.classroom.curriculum.catalog',
        'student.classroom.curriculum.present',
        'student.classroom.curriculum.slide.update',
        'student.classroom.curriculum.stop',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        if (! $user || ! $user->isStudent()) {
            return $next($request);
        }

        $routeName = $request->route()?->getName();
        if ($routeName && in_array($routeName, self::BLOCKED_ROUTE_NAMES, true)) {
            return redirect()
                ->route('dashboard')
                ->with('error', 'لا يمكن للطلاب إنشاء أو إدارة اجتماعات مباشرة. انضم من جدولك أو من حصصك/فصولك.');
        }

        return $next($request);
    }
}
