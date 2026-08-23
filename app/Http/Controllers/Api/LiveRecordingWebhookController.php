<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LiveRecording;
use App\Models\LiveSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * ويب هوك لتسجيل تسجيلات الجلسات بعد رفع Jibri للملف إلى R2.
 * يُستدعى من السكربت على سيرفر Jibri بعد: aws s3 cp ... s3://bucket/key
 */
class LiveRecordingWebhookController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $token = config('services.live_recordings_webhook.token');
        if (empty($token) || $request->header('X-Webhook-Token') !== $token) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'session_id'        => 'required|exists:live_sessions,id',
            'file_path'         => 'required|string|max:500',
            'title'             => 'nullable|string|max:255',
            'duration_seconds'  => 'nullable|integer|min:0',
            'file_size'         => 'nullable|integer|min:0',
        ]);

        $session = LiveSession::find($validated['session_id']);
        $title = $validated['title'] ?? ('تسجيل ' . $session->title);

        $rec = LiveRecording::create([
            'session_id'        => $validated['session_id'],
            'title'             => $title,
            'file_path'         => $validated['file_path'],
            'storage_disk'      => 'r2',
            'file_size'         => $validated['file_size'] ?? 0,
            'duration_seconds'  => $validated['duration_seconds'] ?? 0,
            'status'            => 'ready',
            'is_published'      => false,
        ]);

        \App\Services\LiveRecordingAutoPublishService::publishForSession($rec, $session);

        return response()->json([
            'success' => true,
            'recording_id' => $rec->id,
            'message' => 'تم حفظ التسجيل ونشره للطلاب.',
        ], 201);
    }
}
