<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassroomMeeting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ClassroomRecordingController extends Controller
{
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
     * حذف ملف/ملفات التسجيل من R2 ومسح حقول التسجيل من الاجتماع.
     */
    public function destroy(ClassroomMeeting $meeting)
    {
        if (! $meeting->hasRecordingMediaOnR2()
            && empty($meeting->recording_path)
            && empty($meeting->recording_audio_path)) {
            return back()->with('error', 'لا يوجد تسجيل لحذفه لهذا الاجتماع.');
        }

        $paths = array_values(array_filter([
            $meeting->recording_path,
            $meeting->recording_audio_path,
        ]));

        if ($meeting->recording_disk === 'live_recordings_r2' && $paths !== []) {
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
        }

        $meeting->update([
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
        ]);

        return back()->with('success', 'تم حذف تسجيل الاجتماع من التخزين ولوحة التحكم.');
    }
}
