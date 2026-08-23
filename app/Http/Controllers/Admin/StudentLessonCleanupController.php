<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassroomMeeting;
use App\Models\LiveSession;
use App\Models\OneToOneSession;
use App\Models\TutoringGroupBooking;
use App\Models\User;
use App\Services\StudentLessonCleanupService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentLessonCleanupController extends Controller
{
    public function index(Request $request): View
    {
        $type = (string) $request->get('type', 'one_to_one');
        $studentId = (int) $request->get('student_id', 0);
        $q = trim((string) $request->get('q', ''));
        $experimentalOnly = $request->boolean('experimental');

        $students = User::query()
            ->where('role', 'student')
            ->orderBy('name')
            ->limit(500)
            ->get(['id', 'name', 'email']);

        $rows = collect();
        $paginator = null;

        if ($type === 'one_to_one') {
            $query = OneToOneSession::query()
                ->with(['student:id,name,email', 'instructor:id,name', 'classroomMeeting'])
                ->orderByDesc('id');

            if ($studentId > 0) {
                $query->where('student_id', $studentId);
            }
            if ($q !== '') {
                $query->where(function ($inner) use ($q) {
                    $inner->where('notes', 'like', '%'.$q.'%')
                        ->orWhere('id', is_numeric($q) ? (int) $q : 0)
                        ->orWhereHas('student', fn ($s) => $s->where('name', 'like', '%'.$q.'%')->orWhere('email', 'like', '%'.$q.'%'));
                });
            }
            if ($experimentalOnly) {
                $query->where(function ($inner) {
                    $inner->where('notes', 'like', '%demo:%')
                        ->orWhere('notes', 'like', '%تجريب%')
                        ->orWhere('notes', 'like', '%اختبار%')
                        ->orWhere('notes', 'like', '%test%')
                        ->orWhere('notes', 'like', '%تسكين يدوي%');
                });
            }

            $paginator = $query->paginate(40)->withQueryString();
            $rows = $paginator;
        } elseif ($type === 'group') {
            $query = TutoringGroupBooking::query()
                ->with(['user:id,name,email', 'instructor:id,name', 'tutoringGroup:id,name'])
                ->orderByDesc('id');

            if ($studentId > 0) {
                $query->where('user_id', $studentId);
            }
            if ($q !== '') {
                $query->where(function ($inner) use ($q) {
                    $inner->where('admin_notes', 'like', '%'.$q.'%')
                        ->orWhere('student_notes', 'like', '%'.$q.'%')
                        ->orWhere('id', is_numeric($q) ? (int) $q : 0)
                        ->orWhereHas('user', fn ($s) => $s->where('name', 'like', '%'.$q.'%')->orWhere('email', 'like', '%'.$q.'%'));
                });
            }
            if ($experimentalOnly) {
                $query->where(function ($inner) {
                    $inner->where('admin_notes', 'like', '%demo:%')
                        ->orWhere('admin_notes', 'like', '%تجريب%')
                        ->orWhere('admin_notes', 'like', '%اختبار%')
                        ->orWhere('admin_notes', 'like', '%test%')
                        ->orWhere('student_notes', 'like', '%تجريب%')
                        ->orWhere('student_notes', 'like', '%test%');
                });
            }

            $paginator = $query->paginate(40)->withQueryString();
            $rows = $paginator;
        } elseif ($type === 'meetings') {
            $query = ClassroomMeeting::query()
                ->with(['user:id,name', 'oneToOneSession.student:id,name'])
                ->orderByDesc('id');

            if ($studentId > 0) {
                $query->where(function ($inner) use ($studentId) {
                    $inner->where('user_id', $studentId)
                        ->orWhereHas('oneToOneSession', fn ($s) => $s->where('student_id', $studentId));
                });
            }
            if ($q !== '') {
                $query->where(function ($inner) use ($q) {
                    $inner->where('title', 'like', '%'.$q.'%')
                        ->orWhere('code', 'like', '%'.$q.'%')
                        ->orWhere('id', is_numeric($q) ? (int) $q : 0);
                });
            }
            if ($experimentalOnly) {
                $query->where(function ($inner) {
                    $inner->where('title', 'like', '%تجريب%')
                        ->orWhere('title', 'like', '%test%')
                        ->orWhere('title', 'like', '%اختبار%')
                        ->orWhere('title', 'like', '%demo%');
                });
            }

            $paginator = $query->paginate(40)->withQueryString();
            $rows = $paginator;
        } else { // live
            $query = LiveSession::query()
                ->with(['instructor:id,name', 'course:id,title'])
                ->orderByDesc('id');

            if ($q !== '') {
                $query->where(function ($inner) use ($q) {
                    $inner->where('title', 'like', '%'.$q.'%')
                        ->orWhere('description', 'like', '%'.$q.'%')
                        ->orWhere('id', is_numeric($q) ? (int) $q : 0);
                });
            }
            if ($experimentalOnly) {
                $query->where(function ($inner) {
                    $inner->where('title', 'like', 'بث إداري%')
                        ->orWhere('description', 'like', '%أنشأتها الإدارة%')
                        ->orWhere('title', 'like', '%تجريب%')
                        ->orWhere('title', 'like', '%test%')
                        ->orWhere('settings', 'like', '%"admin_only":true%');
                });
            }

            $paginator = $query->paginate(40)->withQueryString();
            $rows = $paginator;
        }

        $stats = [
            'one_to_one' => OneToOneSession::query()->count(),
            'group' => TutoringGroupBooking::query()->count(),
            'meetings' => ClassroomMeeting::query()->count(),
            'live' => LiveSession::query()->count(),
            'live_admin' => LiveSession::query()
                ->where(function ($inner) {
                    $inner->where('title', 'like', 'بث إداري%')
                        ->orWhere('description', 'like', '%أنشأتها الإدارة%')
                        ->orWhere('settings', 'like', '%"admin_only":true%');
                })
                ->count(),
        ];

        return view('admin.student-lesson-cleanup.index', compact(
            'type',
            'studentId',
            'q',
            'experimentalOnly',
            'students',
            'rows',
            'paginator',
            'stats',
        ));
    }

    public function destroyOneToOne(Request $request, int $oneToOneSession): RedirectResponse
    {
        $session = OneToOneSession::query()->findOrFail($oneToOneSession);

        try {
            $count = StudentLessonCleanupService::purgeOneToOne(
                $session,
                $request->boolean('series')
            );
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }

        if ($count < 1) {
            return back()->with('error', 'لم يتم العثور على الحصة للحذف.');
        }

        return back()->with('success', $count > 1
            ? 'تم حذف '.$count.' حصص 1:1 نهائياً.'
            : 'تم حذف الحصة 1:1 نهائياً.');
    }

    public function destroyGroup(int $tutoringGroupBooking): RedirectResponse
    {
        $booking = TutoringGroupBooking::query()->findOrFail($tutoringGroupBooking);

        try {
            StudentLessonCleanupService::purgeTutoringBooking($booking);
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'تم حذف حجز المجموعة نهائياً.');
    }

    public function destroyMeeting(int $classroomMeeting): RedirectResponse
    {
        $meeting = ClassroomMeeting::query()->findOrFail($classroomMeeting);

        try {
            StudentLessonCleanupService::purgeClassroomMeeting($meeting);
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'تم حذف غرفة Classroom نهائياً.');
    }

    public function destroyLive(int $liveSession): RedirectResponse
    {
        $session = LiveSession::query()->findOrFail($liveSession);

        try {
            StudentLessonCleanupService::purgeLiveSession($session);
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'تم حذف جلسة البث نهائياً.');
    }

    public function bulkDestroy(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'type' => ['required', 'in:one_to_one,group,meetings,live'],
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer'],
        ]);

        $deleted = 0;
        $errors = 0;

        foreach ($data['ids'] as $id) {
            try {
                if ($data['type'] === 'one_to_one') {
                    $row = OneToOneSession::query()->find($id);
                    if ($row) {
                        $deleted += StudentLessonCleanupService::purgeOneToOne($row);
                    }
                } elseif ($data['type'] === 'group') {
                    $row = TutoringGroupBooking::query()->find($id);
                    if ($row) {
                        StudentLessonCleanupService::purgeTutoringBooking($row);
                        $deleted++;
                    }
                } elseif ($data['type'] === 'meetings') {
                    $row = ClassroomMeeting::query()->find($id);
                    if ($row) {
                        StudentLessonCleanupService::purgeClassroomMeeting($row);
                        $deleted++;
                    }
                } else {
                    $row = LiveSession::query()->find($id);
                    if ($row) {
                        StudentLessonCleanupService::purgeLiveSession($row);
                        $deleted++;
                    }
                }
            } catch (\Throwable) {
                $errors++;
            }
        }

        $message = 'تم حذف '.$deleted.' عنصر'.($errors > 0 ? ' — فشل '.$errors : '').'.';

        return back()->with($deleted > 0 ? 'success' : 'error', $message);
    }
}
