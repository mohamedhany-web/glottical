<?php

namespace App\Console\Commands;

use App\Services\CourseSubscriptionService;
use Illuminate\Console\Command;

class ExpireCourseSubscriptionsCommand extends Command
{
    protected $signature = 'courses:expire-subscriptions';

    protected $description = 'إنهاء اشتراكات الكورسات الشهرية المنتهية';

    public function handle(): int
    {
        $count = CourseSubscriptionService::expireDueEnrollments();
        $this->info("تم إنهاء {$count} اشتراك كورس منتهٍ.");

        return self::SUCCESS;
    }
}
