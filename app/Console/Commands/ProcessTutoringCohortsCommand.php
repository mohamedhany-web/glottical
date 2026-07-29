<?php

namespace App\Console\Commands;

use App\Models\Notification;
use App\Models\TutoringGroupBooking;
use App\Services\TutoringCohortService;
use Illuminate\Console\Command;

class ProcessTutoringCohortsCommand extends Command
{
    protected $signature = 'tutoring:process-cohorts';

    protected $description = 'Postpone under-enrolled cohorts, close enrollment windows, send tutoring reminders';

    public function handle(): int
    {
        $result = TutoringCohortService::processAll();
        $this->info('Postponed: '.$result['postponed'].' · Closed: '.$result['closed']);

        $reminded = 0;
        TutoringGroupBooking::query()
            ->where('status', TutoringGroupBooking::STATUS_CONFIRMED)
            ->whereBetween('starts_at', [now()->addMinutes(50), now()->addMinutes(70)])
            ->whereNotNull('user_id')
            ->with(['tutoringGroup:id,title', 'classroomMeeting:id,code'])
            ->chunkById(50, function ($bookings) use (&$reminded) {
                foreach ($bookings as $booking) {
                    $join = $booking->joinUrl() ?: '';
                    Notification::create([
                        'user_id' => $booking->user_id,
                        'sender_id' => null,
                        'title' => 'تذكير: حصتك خلال ساعة',
                        'message' => ($booking->tutoringGroup?->title ?? 'مجموعة').' — '.$booking->starts_at?->format('H:i').($join ? ' · '.$join : ''),
                        'type' => 'reminder',
                        'priority' => 'high',
                        'audience' => 'student',
                        'action_url' => $join ?: route('student.tutoring-bookings.show', $booking),
                        'action_text' => 'دخول الحصة',
                    ]);
                    if ($booking->instructor_id) {
                        Notification::create([
                            'user_id' => $booking->instructor_id,
                            'sender_id' => null,
                            'title' => 'تذكير حصة مجموعة',
                            'message' => ($booking->tutoringGroup?->title ?? '').' — '.$booking->starts_at?->format('H:i'),
                            'type' => 'reminder',
                            'priority' => 'normal',
                            'audience' => 'instructor',
                        ]);
                    }
                    $reminded++;
                }
            });

        $this->info('Reminders sent: '.$reminded);

        return self::SUCCESS;
    }
}
