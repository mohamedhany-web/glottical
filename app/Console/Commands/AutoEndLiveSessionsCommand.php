<?php

namespace App\Console\Commands;

use App\Models\LiveSession;
use App\Models\LiveSetting;
use App\Models\SessionAttendance;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AutoEndLiveSessionsCommand extends Command
{
    protected $signature = 'live:auto-end-sessions';

    protected $description = 'Automatically end live sessions that exceeded max duration or were left idle without the instructor';

    public function handle(): int
    {
        $maxMinutes = max(15, (int) LiveSetting::get('auto_end_minutes', 90));
        $idleMinutes = max(5, (int) LiveSetting::get('idle_end_minutes', 20));

        $ended = 0;

        // 1) تجاوز أقصى مدة للبث
        $overdue = LiveSession::where('status', 'live')
            ->whereNotNull('started_at')
            ->where('started_at', '<=', now()->subMinutes($maxMinutes))
            ->get();

        foreach ($overdue as $session) {
            $this->endSession($session, "after {$maxMinutes} minutes max duration");
            $ended++;
        }

        // 2) المدرب غادر (أو لم يعد نشطاً) والجلسة ما زالت live
        $idleCutoff = now()->subMinutes($idleMinutes);
        $candidates = LiveSession::where('status', 'live')
            ->whereNotNull('started_at')
            ->where('started_at', '<=', $idleCutoff)
            ->get();

        foreach ($candidates as $session) {
            if (! $this->instructorIsAbsent($session, $idleCutoff)) {
                continue;
            }

            $this->endSession($session, "after instructor idle {$idleMinutes} minutes");
            $ended++;
        }

        if ($ended === 0) {
            $this->info('No sessions to auto-end.');

            return self::SUCCESS;
        }

        $this->info("Auto-ended {$ended} session(s).");

        return self::SUCCESS;
    }

    private function instructorIsAbsent(LiveSession $session, $idleCutoff): bool
    {
        $instructorAttendance = SessionAttendance::query()
            ->where('session_id', $session->id)
            ->where('user_id', $session->instructor_id)
            ->where('role_in_session', 'instructor')
            ->latest('id')
            ->first();

        // لا يوجد سجل حضور للمدرب رغم مرور فترة الخمول بعد البدء
        if (! $instructorAttendance) {
            return true;
        }

        // المدرب ما زال في الغرفة
        if ($instructorAttendance->left_at === null) {
            return false;
        }

        // غادر منذ أكثر من idleCutoff
        return $instructorAttendance->left_at->lte($idleCutoff);
    }

    private function endSession(LiveSession $session, string $reason): void
    {
        SessionAttendance::where('session_id', $session->id)
            ->whereNull('left_at')
            ->each(function ($attendance) {
                $attendance->markLeft();
            });

        $session->end();

        Log::info("Auto-ended live session #{$session->id} \"{$session->title}\" ({$reason}).");
        $this->info("Ended: {$session->title} (ID: {$session->id}) — {$reason}");
    }
}
