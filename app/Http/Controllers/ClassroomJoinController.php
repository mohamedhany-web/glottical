<?php

namespace App\Http\Controllers;

use App\Models\ClassroomMeeting;
use App\Models\ClassroomMeetingParticipant;
use App\Models\LiveSetting;
use App\Services\SubscriptionLimitService;
use App\Support\ShareAnnotationSanitizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class ClassroomJoinController extends Controller
{
    /**
     * صفحة الدخول كضيف — لا تتطلب تسجيل دخول.
     * الرابط يُشارك من المعلم: /classroom/join/{code}
     */
    public function show(string $code)
    {
        $code = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $code));
        if (strlen($code) < 4) {
            abort(404, 'كود الغرفة غير صالح.');
        }

        $roomName = 'Glottical-'.$code;
        $meeting = ClassroomMeeting::where('code', $code)->first();
        $jitsiDomain = LiveSetting::getJitsiDomain();
        $joinUrl = url('classroom/join/'.$code);
        $maxParticipants = (int) ($meeting?->max_participants ?? 25);
        $meetingEnded = (bool) ($meeting && $meeting->ended_at);

        return view('classroom.join', compact('code', 'roomName', 'meeting', 'jitsiDomain', 'joinUrl', 'maxParticipants', 'meetingEnded'));
    }

    public function enter(Request $request, string $code)
    {
        $code = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $code));
        $meeting = ClassroomMeeting::where('code', $code)->firstOrFail();

        if ($meeting->ended_at) {
            return response()->json([
                'ok' => false,
                'message' => 'هذا الاجتماع تم إنهاؤه من المعلم.',
            ], 422);
        }

        $owner = $meeting->user;
        if ($owner && $meeting->started_at) {
            $limits = SubscriptionLimitService::limitsForUser($owner);
            $packageMax = (int) $limits['classroom_max_duration_minutes'];
            $effectiveDuration = (int) ($meeting->planned_duration_minutes ?: $packageMax);
            if ($effectiveDuration > $packageMax) {
                $effectiveDuration = $packageMax;
            }
            $expiresAt = $meeting->started_at->copy()->addMinutes($effectiveDuration);
            if ($expiresAt->isPast()) {
                $meeting->update(['ended_at' => now()]);

                return response()->json([
                    'ok' => false,
                    'message' => 'انتهت مدة هذا الاجتماع حسب قيود الباقة.',
                ], 422);
            }
        }

        $maxParticipants = (int) ($meeting->max_participants ?: 25);
        $activeParticipants = $this->activeParticipantsCount($meeting->id);
        if ($activeParticipants >= $maxParticipants) {
            return response()->json([
                'ok' => false,
                'message' => 'تم الوصول للحد الأقصى للطلاب في هذا الاجتماع.',
            ], 422);
        }

        $displayName = trim((string) $request->input('display_name', 'ضيف'));
        if ($displayName === '') {
            $displayName = 'ضيف';
        }
        $displayName = mb_substr($displayName, 0, 120);

        $token = Str::random(48);
        ClassroomMeetingParticipant::create([
            'classroom_meeting_id' => $meeting->id,
            'token' => $token,
            'display_name' => $displayName,
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 255),
            'joined_at' => now(),
            'last_seen_at' => now(),
        ]);

        $newCount = $this->activeParticipantsCount($meeting->id);
        if ($newCount > (int) ($meeting->participants_peak ?? 0)) {
            $meeting->update(['participants_peak' => $newCount]);
        }

        return response()->json([
            'ok' => true,
            'token' => $token,
            'active_participants' => $newCount,
            'max_participants' => $maxParticipants,
            'allow_participant_whiteboard' => $meeting->allowsParticipantWhiteboard(),
        ]);
    }

    public function heartbeat(Request $request, string $code)
    {
        $token = (string) $request->input('token');
        if ($token === '') {
            return response()->json(['ok' => false], 422);
        }

        $code = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $code));
        $meeting = ClassroomMeeting::where('code', $code)->firstOrFail();
        $participant = ClassroomMeetingParticipant::where('classroom_meeting_id', $meeting->id)
            ->where('token', $token)
            ->first();

        if (! $participant || $participant->left_at) {
            return response()->json(['ok' => false], 404);
        }

        $participant->update(['last_seen_at' => now()]);
        $meeting->refresh();

        return response()->json([
            'ok' => true,
            'active_participants' => $this->activeParticipantsCount($meeting->id),
            'max_participants' => (int) ($meeting->max_participants ?: 25),
            'allow_participant_whiteboard' => $meeting->allowsParticipantWhiteboard(),
        ]);
    }

    public function leave(Request $request, string $code)
    {
        $token = (string) $request->input('token');
        if ($token === '') {
            return response()->json(['ok' => false], 422);
        }

        $code = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $code));
        $meeting = ClassroomMeeting::where('code', $code)->firstOrFail();
        ClassroomMeetingParticipant::where('classroom_meeting_id', $meeting->id)
            ->where('token', $token)
            ->whereNull('left_at')
            ->update(['left_at' => now(), 'last_seen_at' => now()]);

        return response()->json(['ok' => true]);
    }

    public function pushShareAnnotation(Request $request, string $code)
    {
        $code = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $code));
        $meeting = ClassroomMeeting::where('code', $code)->firstOrFail();

        if (! $meeting->allowsParticipantWhiteboard() || ! $meeting->started_at || $meeting->ended_at) {
            return response()->json(['message' => 'غير مسموح'], 422);
        }

        $token = (string) $request->input('token');
        if ($token === '') {
            return response()->json(['message' => 'رمز غير صالح'], 422);
        }

        $participant = ClassroomMeetingParticipant::where('classroom_meeting_id', $meeting->id)
            ->where('token', $token)
            ->whereNull('left_at')
            ->first();

        if (! $participant) {
            return response()->json(['message' => 'غير مصرح'], 403);
        }

        $clean = ShareAnnotationSanitizer::polylines($request->input('polylines'));
        $key = 'mx_share_ann_classroom_'.$meeting->id;
        $all = Cache::get($key, []);
        $layerKey = 'g_'.substr(hash('sha256', $token), 0, 24);
        $all[$layerKey] = [
            'name' => $participant->display_name,
            'polylines' => $clean,
            'ts' => now()->timestamp,
        ];
        Cache::put($key, $all, now()->addHours(6));

        return response()->json(['ok' => true]);
    }

    private function activeParticipantsCount(int $meetingId): int
    {
        return ClassroomMeetingParticipant::query()
            ->where('classroom_meeting_id', $meetingId)
            ->whereNull('left_at')
            ->where('last_seen_at', '>=', now()->subMinutes(2))
            ->count();
    }
}
