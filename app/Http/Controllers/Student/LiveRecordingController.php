<?php

namespace App\Http\Controllers\Student;

use App\Helpers\VideoHelper;
use App\Http\Controllers\Controller;
use App\Models\LiveRecording;

class LiveRecordingController extends Controller
{
    /**
     * قائمة التسجيلات المنشورة للجلسات التي يمكن للطالب الوصول إليها.
     */
    public function index(Request $request)
    {
        return redirect()->route('student.library.videos');
    }

    /**
     * مشاهدة تسجيل داخل المنصة (مشغّل مضمّن).
     */
    public function show(LiveRecording $liveRecording)
    {
        $liveRecording->load(['session.course', 'session.instructor', 'folder']);
        $session = $liveRecording->session;

        if (! $session || ! $session->canUserJoin(auth()->user())) {
            abort(403, 'ليس لديك صلاحية مشاهدة هذا التسجيل');
        }
        if ($liveRecording->status !== 'ready' || ! $liveRecording->is_published) {
            abort(404);
        }

        $url = $liveRecording->getUrl();
        if (! $url) {
            abort(404, 'رابط التسجيل غير متوفر حالياً');
        }

        $embedUrl = VideoHelper::getEmbedUrl($url);
        $directUrl = $embedUrl ? null : (VideoHelper::getDirectVideoUrl($url) ?: $url);
        $source = VideoHelper::getVideoSource($url);
        $thumbnail = VideoHelper::getThumbnail($url);

        return view('student.live-recordings.show', [
            'liveRecording' => $liveRecording,
            'url' => $url,
            'embedUrl' => $embedUrl,
            'directUrl' => $directUrl,
            'source' => $source,
            'thumbnail' => $thumbnail,
        ]);
    }
}
