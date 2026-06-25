<?php

namespace App\Console\Commands;

use App\Services\CourseAutoRenewalService;
use Illuminate\Console\Command;

class ProcessCourseAutoRenewalsCommand extends Command
{
    protected $signature = 'courses:process-auto-renewals';

    protected $description = 'إنشاء طلبات تجديد وإرسال تذكيرات للاشتراكات الشهرية';

    public function handle(): int
    {
        $renewals = CourseAutoRenewalService::processDueRenewals();
        $reminders = CourseAutoRenewalService::sendExpiryReminders();

        $this->info("Renewal orders created: {$renewals}");
        $this->info("Expiry reminders sent: {$reminders}");

        return self::SUCCESS;
    }
}
