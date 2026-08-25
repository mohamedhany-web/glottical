<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassroomMeeting;
use App\Services\LiveMeetingProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ClassroomRecordingController extends Controller
{
    private const CLASSROOM_MAX_DURATION_MINUTES = 180;

    private const CLASSROOM_DEFAULT_DURATION_MINUTES = 60;

    public function index(Request $request)
    {
        $status = (string) $request->get('status', 'all');
        if (! in_array($status, ['all', 'live', 'scheduled', 'ended'], true)) {
            $status = 'all';
        }

        $hasRecording = (string) $request->get('has_recording', 'all');
        if (! in_array($hasRecording, ['all', 'yes', 'no'], true)) {
            $hasRecording = 'all';
        }

        $search = trim((string) $request->get('search', ''));

        $query = ClassroomMeeting::query()->with(['user', 'consultationRequest'])->latest();

        if ($status === 'live') {
            $query->whereNotNull('started_at')->whereNull('ended_at');
        } elseif ($status === 'scheduled') {
            $query->whereNull('started_at');
        } elseif ($status === 'ended') {
            $query->whereNotNull('ended_at');
        }

        if ($hasRecording === 'yes') {
            $query->where('recording_disk', 'live_recordings_r2')
                ->where(function ($q) {
                    $q->whereNotNull('recording_path')->orWhereNotNull('recording_audio_path');
                });
        } elseif ($hasRecording === 'no') {
            $query->where(function ($q) {
                $q->whereNull('recording_disk')
                    ->orWhere('recording_disk', '!=', 'live_recordings_r2')
                    ->orWhere(function ($inner) {
                        $inner->whereNull('recording_path')->whereNull('recording_audio_path');
                    });
            });
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%'.$search.'%')
                    ->orWhere('code', 'like', '%'.$search.'%')
                    ->orWhere('room_name', 'like', '%'.$search.'%')
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', '%'.$search.'%')
                            ->orWhere('email', 'like', '%'.$search.'%');
                    });
            });
        }

        $meetings = $query->paginate(20)->withQueryString();

        return view('admin.classroom-recordings.index', compact('meetings', 'status', 'hasRecording', 'search'));
    }

    /**
     * دخول صامت لاجتماع Classroom مباشر — مخفي عن المعلم والطالب (LiveKit hidden).
     */
    public function observe(ClassroomMeeting $meeting)
    {
        if (! $meeting->isLive()) {
            return redirect()
                ->route('admin.classroom-recordings.index', ['status' => 'live'])
                ->with('error', 'الاجتماع غير مباشر حالياً.');
        }

        $maxDurationMinutes = self::CLASSROOM_MAX_DURATION_MINUTES;
        $effectiveDurationMinutes = (int) ($meeting->planned_duration_minutes ?: self::CLASSROOM_DEFAULT_DURATION_MINUTES);
        if ($effectiveDurationMinutes > $maxDurationMinutes) {
            $effectiveDurationMinutes = $maxDurationMinutes;
        }

        if ($meeting->started_at && $meeting->started_at->copy()->addMinutes($effectiveDurationMinutes)->isPast()) {
            if (! $meeting->ended_at) {
                $meeting->update(['ended_at' => now()]);
            }

            return redirect()
                ->route('admin.classroom-recordings.index')
                ->with('error', 'انتهت مدة الاجتماع.');
        }

        $admin = auth()->user();
        $meetingPayload = app(LiveMeetingProvider::class)->classroomPayload(
            $meeting->liveRoomName(),
            $admin,
            false,
            [
                'canPublish' => false,
                'canSubscribe' => true,
                'canPublishData' => false,
                'hidden' => true,
                'roomAdmin' => false,
            ]
        );

        $meetingEndsAt = $meeting->started_at
            ? $meeting->started_at->copy()->addMinutes($effectiveDurationMinutes)
            : null;
        $useInstructorRoutes = false;
        $user = $admin;
        $academicObserverMode = true;
        $academicObserverExitUrl = route('admin.classroom-recordings.index', ['status' => 'live']);
        $subscriptionFeatureMenuItems = [];
        $subscriptionPackageLabel = null;
        $canManageMeeting = false;

        return view('student.classroom.room', array_merge(
            compact(
                'meeting',
                'user',
                'maxDurationMinutes',
                'effectiveDurationMinutes',
                'meetingEndsAt',
                'useInstructorRoutes',
                'academicObserverMode',
                'academicObserverExitUrl',
                'subscriptionFeatureMenuItems',
                'subscriptionPackageLabel',
                'canManageMeeting'
            ),
            $meetingPayload
        ));
    }

    /**
     * حذف ملف/ملفات التسجيل من R2 ومسح حقول التسجيل من الاجتماع.
     */
    public function destroy(int $meeting)
    {
        $meeting = ClassroomMeeting::query()->findOrFail($meeting);

        $paths = array_values(array_filter([
            $meeting->recording_path,
            $meeting->recording_audio_path,
        ]));

        if ($paths === [] && empty($meeting->recording_disk)) {
            return back()->with('error', 'لا يوجد تسجيل لحذفه لهذا الاجتماع.');
        }

        if ($paths !== []) {
            try {
                $disk = Storage::disk('live_recordings_r2');
                foreach ($paths as $path) {
                    try {
                        if ($disk->exists($path)) {
                            $disk->delete($path);
                        }
                    } catch (\Throwable $e) {
                        Log::warning('Failed deleting classroom recording object from R2', [
                            'meeting_id' => $meeting->id,
                            'path' => $path,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('R2 disk unavailable while deleting classroom recording', [
                    'meeting_id' => $meeting->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $meeting->forceFill([
            'recording_disk' => null,
            'recording_path' => null,
            'recording_mime_type' => null,
            'recording_size' => null,
            'recording_audio_path' => null,
            'recording_audio_mime_type' => null,
            'recording_audio_size' => null,
            'recording_duration_seconds' => null,
            'recording_audio_duration_seconds' => null,
            'recording_uploaded_at' => null,
        ])->save();

        return back()->with('success', 'تم حذف تسجيل الاجتماع من التخزين ولوحة التحكم.');
    }
}
