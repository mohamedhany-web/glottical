<?php

namespace App\Http\Controllers\Instructor;

use App\Helpers\VideoHelper;
use App\Http\Controllers\Controller;
use App\Models\Lecture;
use App\Models\LiveRecording;
use App\Models\LiveSession;
use App\Services\LectureMaterialStorage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

/**
 * صفحة «تسجيل المحاضرات» — ربط رابط أو رفع ملف، تشغيل داخل المنصة.
 */
class LectureRecordingController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = $request->user();
            if (! $user || (! $user->isInstructor() && ! $user->isTeacher())) {
                abort(403);
            }

            return $next($request);
        });
    }

    public function index(Request $request): View
    {
        $user = $request->user();

        $base = Lecture::query()->where('instructor_id', $user->id);

        $stats = [
            'total' => (clone $base)->count(),
            'recorded' => (clone $base)->where(function ($q) {
                $q->whereNotNull('recording_url')->where('recording_url', '!=', '')
                    ->orWhereNotNull('recording_file_path');
            })->count(),
        ];
        $stats['missing'] = max(0, $stats['total'] - $stats['recorded']);

        $lectures = (clone $base)
            ->with(['course:id,title'])
            ->orderByDesc('scheduled_at')
            ->orderByDesc('id')
            ->paginate(20, ['*'], 'lectures_page');

        $liveRecordings = collect();
        if (Schema::hasTable('live_recordings') && Schema::hasTable('live_sessions')) {
            $sessionIds = LiveSession::query()
                ->where('instructor_id', $user->id)
                ->pluck('id');
            if ($sessionIds->isNotEmpty()) {
                $liveRecordings = LiveRecording::query()
                    ->whereIn('session_id', $sessionIds)
                    ->orderByDesc('id')
                    ->limit(30)
                    ->get();
            }
        }

        $stats['live'] = $liveRecordings->count();

        return view('instructor.lecture-recordings.index', compact('lectures', 'liveRecordings', 'stats'));
    }

    public function update(Request $request, Lecture $lecture): RedirectResponse
    {
        $user = $request->user();
        abort_unless((int) $lecture->instructor_id === (int) $user->id, 403);

        $data = $request->validate([
            'recording_url' => ['nullable', 'string', 'max:2000'],
            'recording_file' => ['nullable', 'file', 'mimetypes:video/mp4,video/webm,video/quicktime,application/octet-stream', 'max:512000'],
            'clear_file' => ['nullable', 'boolean'],
        ]);

        $url = trim((string) ($data['recording_url'] ?? ''));
        if ($url !== '') {
            if (! VideoHelper::isValidVideoUrl($url)) {
                return back()->withErrors(['recording_url' => 'الرابط غير مدعوم للتشغيل داخل المنصة (YouTube / Vimeo / Bunny / ملف مباشر).'])->withInput();
            }
            $lecture->recording_url = $url;
            $lecture->video_platform = VideoHelper::getVideoSource($url);
        }

        if ($request->boolean('clear_file') && $lecture->recording_file_path) {
            LectureMaterialStorage::delete($lecture->recording_file_path, $lecture->storage_disk ?? null);
            $lecture->recording_file_path = null;
        }

        if ($request->hasFile('recording_file')) {
            if ($lecture->recording_file_path) {
                LectureMaterialStorage::delete($lecture->recording_file_path);
            }
            $lecture->recording_file_path = LectureMaterialStorage::storeLectureRecording(
                $request->file('recording_file'),
                (int) $lecture->id
            );
        }

        $lecture->save();

        return back()->with('success', 'تم حفظ تسجيل المحاضرة.');
    }

    public function preview(Lecture $lecture): View
    {
        $user = request()->user();
        abort_unless((int) $lecture->instructor_id === (int) $user->id, 403);

        $embedUrl = $lecture->recording_url ? VideoHelper::getEmbedUrl($lecture->recording_url) : null;
        $directUrl = $lecture->recording_url ? VideoHelper::getDirectVideoUrl($lecture->recording_url) : null;
        $fileUrl = null;
        if ($lecture->recording_file_path) {
            try {
                $fileUrl = \Illuminate\Support\Facades\Storage::disk(LectureMaterialStorage::resolvedDisk())
                    ->temporaryUrl($lecture->recording_file_path, now()->addHour());
            } catch (\Throwable $e) {
                $fileUrl = \Illuminate\Support\Facades\Storage::disk('public')->url($lecture->recording_file_path);
            }
        }

        return view('instructor.lecture-recordings.preview', compact('lecture', 'embedUrl', 'directUrl', 'fileUrl'));
    }
}
