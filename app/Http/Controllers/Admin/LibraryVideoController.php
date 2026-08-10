<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdvancedCourse;
use App\Models\Lecture;
use App\Models\LiveRecording;
use App\Models\LiveSession;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LibraryVideoController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = $request->user();
            if (! $user || (! $user->isAdmin() && ! $user->hasPermission('manage.live-sessions') && ! $user->hasPermission('manage.lectures') && ! $user->hasPermission('manage.courses'))) {
                abort(403);
            }

            return $next($request);
        });
    }

    public function index(Request $request): View
    {
        $tab = in_array($request->get('tab'), ['live', 'lectures', 'all'], true)
            ? $request->get('tab')
            : 'all';

        $liveQuery = LiveRecording::query()
            ->with(['session:id,title,course_id,instructor_id', 'session.course:id,title', 'session.instructor:id,name'])
            ->orderByDesc('id');

        if ($request->filled('search')) {
            $s = trim((string) $request->search);
            $liveQuery->where(function ($q) use ($s) {
                $q->where('title', 'like', "%{$s}%")
                    ->orWhereHas('session', fn ($sq) => $sq->where('title', 'like', "%{$s}%"));
            });
        }
        if ($request->filled('status')) {
            $liveQuery->where('status', $request->status);
        }
        if ($request->filled('published')) {
            $liveQuery->where('is_published', $request->published === '1');
        }
        if ($request->filled('session_id')) {
            $liveQuery->where('session_id', (int) $request->session_id);
        }

        $liveRecordings = ($tab === 'lectures')
            ? LiveRecording::query()->whereRaw('1=0')->paginate(1)
            : $liveQuery->paginate(20, ['*'], 'live_page')->withQueryString();

        $lectureQuery = Lecture::query()
            ->with(['course:id,title', 'instructor:id,name'])
            ->where(function ($q) {
                $q->where(function ($qq) {
                    $qq->whereNotNull('recording_url')->where('recording_url', '!=', '');
                })->orWhere(function ($qq) {
                    $qq->whereNotNull('recording_file_path')->where('recording_file_path', '!=', '');
                });
            })
            ->orderByDesc('id');

        if ($request->filled('search')) {
            $s = trim((string) $request->search);
            $lectureQuery->where(function ($q) use ($s) {
                $q->where('title', 'like', "%{$s}%")
                    ->orWhereHas('course', fn ($cq) => $cq->where('title', 'like', "%{$s}%"));
            });
        }
        if ($request->filled('course_id')) {
            $lectureQuery->where('course_id', (int) $request->course_id);
        }

        $lectureVideos = ($tab === 'live')
            ? Lecture::query()->whereRaw('1=0')->paginate(1)
            : $lectureQuery->paginate(20, ['*'], 'lecture_page')->withQueryString();

        $stats = [
            'live_total' => LiveRecording::query()->count(),
            'live_ready' => LiveRecording::query()->where('status', 'ready')->count(),
            'live_published' => LiveRecording::query()->where('is_published', true)->count(),
            'lecture_videos' => Lecture::query()
                ->where(function ($q) {
                    $q->whereNotNull('recording_url')->where('recording_url', '!=', '')
                        ->orWhereNotNull('recording_file_path')->where('recording_file_path', '!=', '');
                })
                ->count(),
        ];

        return view('admin.libraries.videos.index', [
            'tab' => $tab,
            'liveRecordings' => $liveRecordings,
            'lectureVideos' => $lectureVideos,
            'stats' => $stats,
            'sessions' => LiveSession::query()->orderByDesc('scheduled_at')->limit(200)->get(['id', 'title']),
            'courses' => AdvancedCourse::query()->orderBy('title')->get(['id', 'title']),
        ]);
    }

    public function create(): View
    {
        return view('admin.libraries.videos.form', [
            'mode' => 'create',
            'recording' => new LiveRecording([
                'status' => 'ready',
                'is_published' => true,
                'storage_disk' => 'public',
                'duration_seconds' => 0,
            ]),
            'sessions' => LiveSession::query()
                ->with('course:id,title')
                ->orderByDesc('scheduled_at')
                ->limit(300)
                ->get(['id', 'title', 'course_id', 'scheduled_at']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'session_id' => ['required', 'exists:live_sessions,id'],
            'title' => ['nullable', 'string', 'max:255'],
            'external_url' => ['nullable', 'url', 'max:2000'],
            'file_path' => ['nullable', 'string', 'max:1000'],
            'storage_disk' => ['nullable', 'in:public,r2,local'],
            'duration_seconds' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', 'in:ready,processing,failed'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        if (empty($data['external_url']) && empty($data['file_path'])) {
            return back()->withInput()->with('error', 'أدخل رابط خارجي أو مسار ملف للتسجيل.');
        }

        $session = LiveSession::findOrFail((int) $data['session_id']);

        $rec = LiveRecording::create([
            'session_id' => (int) $data['session_id'],
            'title' => $data['title'] ?: ('تسجيل '.$session->title),
            'external_url' => $data['external_url'] ?? null,
            'file_path' => $data['file_path'] ?? null,
            'storage_disk' => $data['storage_disk'] ?? 'public',
            'duration_seconds' => (int) ($data['duration_seconds'] ?? 0),
            'status' => $data['status'],
            'is_published' => $request->boolean('is_published'),
        ]);

        return redirect()
            ->route('admin.libraries.videos.index', ['tab' => 'live'])
            ->with('success', 'تم إضافة فيديو المكتبة #'.$rec->id);
    }

    public function edit(LiveRecording $liveRecording): View
    {
        return view('admin.libraries.videos.form', [
            'mode' => 'edit',
            'recording' => $liveRecording,
            'sessions' => LiveSession::query()
                ->with('course:id,title')
                ->orderByDesc('scheduled_at')
                ->limit(300)
                ->get(['id', 'title', 'course_id', 'scheduled_at']),
        ]);
    }

    public function update(Request $request, LiveRecording $liveRecording): RedirectResponse
    {
        $data = $request->validate([
            'session_id' => ['required', 'exists:live_sessions,id'],
            'title' => ['nullable', 'string', 'max:255'],
            'external_url' => ['nullable', 'url', 'max:2000'],
            'file_path' => ['nullable', 'string', 'max:1000'],
            'storage_disk' => ['nullable', 'in:public,r2,local'],
            'duration_seconds' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', 'in:ready,processing,failed'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        if (empty($data['external_url']) && empty($data['file_path'])) {
            return back()->withInput()->with('error', 'أدخل رابط خارجي أو مسار ملف للتسجيل.');
        }

        $session = LiveSession::findOrFail((int) $data['session_id']);

        $liveRecording->update([
            'session_id' => (int) $data['session_id'],
            'title' => $data['title'] ?: ('تسجيل '.$session->title),
            'external_url' => $data['external_url'] ?? null,
            'file_path' => $data['file_path'] ?? null,
            'storage_disk' => $data['storage_disk'] ?? $liveRecording->storage_disk,
            'duration_seconds' => (int) ($data['duration_seconds'] ?? 0),
            'status' => $data['status'],
            'is_published' => $request->boolean('is_published'),
        ]);

        return redirect()
            ->route('admin.libraries.videos.index', ['tab' => 'live'])
            ->with('success', 'تم تحديث التسجيل.');
    }

    public function togglePublish(LiveRecording $liveRecording): RedirectResponse
    {
        $liveRecording->update(['is_published' => ! $liveRecording->is_published]);

        return back()->with('success', $liveRecording->is_published ? 'تم نشر الفيديو للطلاب.' : 'تم إلغاء نشر الفيديو.');
    }

    public function destroy(LiveRecording $liveRecording): RedirectResponse
    {
        $liveRecording->delete();

        return back()->with('success', 'تم حذف التسجيل من المكتبة.');
    }

    public function updateLectureVideo(Request $request, Lecture $lecture): RedirectResponse
    {
        $data = $request->validate([
            'recording_url' => ['nullable', 'url', 'max:2000'],
            'video_platform' => ['nullable', 'string', 'max:50'],
        ]);

        $lecture->update([
            'recording_url' => $data['recording_url'] ?? null,
            'video_platform' => $data['video_platform'] ?? $lecture->video_platform,
        ]);

        return back()->with('success', 'تم تحديث فيديو المحاضرة.');
    }
}
