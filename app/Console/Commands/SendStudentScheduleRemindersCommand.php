<?php

namespace App\Console\Commands;

use App\Services\StudentScheduleService;
use Illuminate\Console\Command;

class SendStudentScheduleRemindersCommand extends Command
{
    protected $signature = 'student:send-schedule-reminders {--minutes= : Minutes before appointment}';

    protected $description = 'Send in-app reminders before private/group class appointments';

    public function handle(): int
    {
        $minutes = (int) ($this->option('minutes') ?: config('student_ui.reminder_minutes', 30));
        $sent = StudentScheduleService::sendUpcomingReminders(max(1, $minutes));
        $this->info("Sent {$sent} reminder(s) (~{$minutes} min window).");

        return self::SUCCESS;
    }
}
