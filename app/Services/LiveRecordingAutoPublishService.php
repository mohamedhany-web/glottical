<?php

namespace App\Services;

use App\Models\LiveRecording;
use App\Models\LiveSession;

/**
 * ينشر تسجيلات الجلسات تلقائياً للطلاب المسجّلين — بدون خطوة يدوية من المدرب أو الإدارة.
 */
class LiveRecordingAutoPublishService
{
    public static function publishForSession(LiveRecording $recording, LiveSession $session): LiveRecording
    {
        if ($recording->status !== 'ready') {
            $recording->status = 'ready';
        }

        $recording->is_published = true;

        if (! filled(trim((string) $recording->title))) {
            $recording->title = 'تسجيل — '.$session->title;
        }

        $recording->save();

        return $recording;
    }
}
