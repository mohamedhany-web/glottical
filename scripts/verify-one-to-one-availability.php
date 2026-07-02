<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\OneToOneSession;
use App\Models\User;
use App\Services\OneToOneAvailabilityService;
use App\Services\OneToOneSessionService;
use Carbon\Carbon;

$instructor = User::query()->whereIn('role', ['instructor', 'teacher'])->first();
$student = User::query()->where('role', 'student')->first();

if (! $instructor || ! $student) {
    echo json_encode(['ok' => false, 'error' => 'missing_users'], JSON_PRETTY_PRINT).PHP_EOL;
    exit(1);
}

$targetDay = Carbon::now()->addDays(3)->isoWeekday();
OneToOneAvailabilityService::syncRules($instructor->id, [[
    'day_of_week' => $targetDay,
    'start_time' => '10:00',
    'end_time' => '14:00',
    'slot_duration_minutes' => 60,
]]);

$slots = OneToOneAvailabilityService::availableSlots($instructor->id, now(), now()->addWeeks(2));
$conflictTest = false;

$session = OneToOneSession::query()
    ->where('instructor_id', $instructor->id)
    ->where('status', OneToOneSession::STATUS_PENDING)
    ->first();

$booked = false;
if ($session && $slots->isNotEmpty()) {
    $slot = $slots->first();
    OneToOneSessionService::scheduleSession($session, $slot['starts_at'], 60, $student, true);
    $session->refresh();
    $booked = $session->status === OneToOneSession::STATUS_SCHEDULED;

    try {
        OneToOneSessionService::scheduleSession($session, $slot['starts_at'], 60, $student, true);
    } catch (\InvalidArgumentException $e) {
        $conflictTest = true;
    }
}

$result = [
    'ok' => $slots->isNotEmpty() && ($session ? $booked && $conflictTest : true),
    'slots' => $slots->count(),
    'had_pending_session' => (bool) $session,
    'booked' => $booked,
    'conflict_rejected' => $conflictTest,
];

echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE).PHP_EOL;
exit($result['ok'] ? 0 : 1);
