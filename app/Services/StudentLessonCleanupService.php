<?php

namespace App\Services;

use App\Models\ClassroomMeeting;
use App\Models\ClassroomMeetingParticipant;
use App\Models\ClassroomMeetingReport;
use App\Models\LiveSession;
use App\Models\OneToOneSession;
use App\Models\SessionAttendance;
use App\Models\TutoringGroupBooking;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class StudentLessonCleanupService
{
    /**
     * Hard-delete a 1:1 session (and linked classroom meeting).
     * Open placements free reserved credit automatically once the row is gone.
     */
    public static function purgeOneToOne(OneToOneSession $session, bool $entireSeries = false): int
    {
        return (int) DB::transaction(function () use ($session, $entireSeries) {
            $query = OneToOneSession::query();

            if ($entireSeries && filled($session->series_id)) {
                $query->where('series_id', $session->series_id);
            } else {
                $query->whereKey($session->id);
            }

            $rows = $query->with('classroomMeeting')->get();
            if ($rows->isEmpty()) {
                return 0;
            }

            foreach ($rows as $row) {
                $meeting = $row->classroomMeeting;
                if ($row->classroom_meeting_id) {
                    $row->forceFill(['classroom_meeting_id' => null])->save();
                }
                $row->delete();
                if ($meeting) {
                    self::purgeClassroomMeeting($meeting);
                }
            }

            return $rows->count();
        });
    }

    public static function purgeClassroomMeeting(ClassroomMeeting $meeting): void
    {
        $meeting = ClassroomMeeting::query()->find($meeting->id);
        if (! $meeting) {
            return;
        }

        OneToOneSession::query()
            ->where('classroom_meeting_id', $meeting->id)
            ->update(['classroom_meeting_id' => null]);

        if (Schema::hasTable('classroom_meeting_participants')) {
            ClassroomMeetingParticipant::query()
                ->where('classroom_meeting_id', $meeting->id)
                ->delete();
        }
        if (Schema::hasTable('classroom_meeting_reports')) {
            ClassroomMeetingReport::query()
                ->where('classroom_meeting_id', $meeting->id)
                ->delete();
        }

        $meeting->delete();
    }

    public static function purgeTutoringBooking(TutoringGroupBooking $booking): void
    {
        DB::transaction(function () use ($booking) {
            $booking = TutoringGroupBooking::query()->lockForUpdate()->findOrFail($booking->id);

            if (in_array($booking->status, [
                TutoringGroupBooking::STATUS_PENDING,
                TutoringGroupBooking::STATUS_CONFIRMED,
            ], true)) {
                try {
                    TutoringGroupOrchestrationService::cancelBooking($booking, 'حذف نهائي من صفحة تنظيف الحصص');
                } catch (\InvalidArgumentException) {
                    // continue
                }
            }

            $meetingId = $booking->classroom_meeting_id ?? null;
            if (Schema::hasColumn($booking->getTable(), 'classroom_meeting_id')) {
                $booking->update(['classroom_meeting_id' => null]);
            }

            $booking->delete();

            if ($meetingId) {
                $meeting = ClassroomMeeting::query()->find($meetingId);
                if ($meeting) {
                    self::purgeClassroomMeeting($meeting);
                }
            }
        });
    }

    public static function purgeLiveSession(LiveSession $session): void
    {
        $session = LiveSession::query()->findOrFail($session->id);

        if ($session->isLive()) {
            $session->end();
        }

        if (Schema::hasTable('session_attendance')) {
            SessionAttendance::query()->where('session_id', $session->id)->delete();
        }

        if (Schema::hasTable('live_recordings')) {
            DB::table('live_recordings')->where('session_id', $session->id)->delete();
        }

        $session->delete();
    }

    public static function looksExperimental(?string $text): bool
    {
        $text = mb_strtolower(trim((string) $text));
        if ($text === '') {
            return false;
        }

        foreach (['demo:', 'تجريب', 'اختبار', 'test', 'تسكين يدوي', 'بث إداري', 'experimental'] as $needle) {
            if (str_contains($text, mb_strtolower($needle))) {
                return true;
            }
        }

        return false;
    }
}
