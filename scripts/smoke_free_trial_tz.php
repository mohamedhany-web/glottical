<?php

/**
 * Live smoke for free-trial timezone slots (run: php scripts/smoke_free_trial_tz.php)
 */
require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\FreeTrialBooking;
use App\Services\FreeTrialBookingService;
use App\Support\AppTimezone;
use Carbon\Carbon;

config(['app.timezone' => 'UTC', 'platform.academy_timezone' => 'Africa/Cairo']);
Carbon::setTestNow(Carbon::parse('2026-03-10 12:00:00', 'UTC'));

$errors = [];

// 1) Quality bands
foreach ([
    [9, 'good'], [19, 'good'], [6, 'caution'], [21, 'caution'], [2, 'poor'], [23, 'poor'],
] as [$h, $expect]) {
    $got = AppTimezone::slotQualityForHour($h);
    if ($got !== $expect) {
        $errors[] = "quality hour {$h}: expected {$expect}, got {$got}";
    }
}

// 2) US states
if (AppTimezone::timezoneForUsState('نيويورك') !== 'America/New_York') {
    $errors[] = 'NY state map failed';
}
if (AppTimezone::timezoneForUsState('كاليفورنيا') !== 'America/Los_Angeles') {
    $errors[] = 'CA state map failed';
}

// 3) Slots for NY viewer — academy wall clock must convert
$slots = FreeTrialBookingService::availableSlots(
    Carbon::parse('2026-03-10 12:00:00', 'UTC'),
    Carbon::parse('2026-03-17 23:59:59', 'UTC'),
    'America/New_York'
);

echo 'slots_count='.$slots->count().PHP_EOL;

if ($slots->isEmpty()) {
    $errors[] = 'No free-trial slots generated (check free_trial_weekly_availability)';
} else {
    $s = $slots->first();
    $utc = $s['starts_at']->copy()->utc();
    $ny = $utc->copy()->timezone('America/New_York')->format('H:i');
    $cairo = $utc->copy()->timezone('Africa/Cairo')->format('H:i');

    echo "first_slot viewer={$s['time']} academy={$s['time_academy']} quality={$s['quality']}".PHP_EOL;
    echo "verify ny={$ny} cairo={$cairo} utc={$utc->format('Y-m-d H:i')}".PHP_EOL;

    if ($s['time'] !== $ny) {
        $errors[] = "viewer time {$s['time']} != NY {$ny}";
    }
    if ($s['time_academy'] !== $cairo) {
        $errors[] = "academy time {$s['time_academy']} != Cairo {$cairo}";
    }
    if (! in_array($s['quality'], ['good', 'caution', 'poor'], true)) {
        $errors[] = 'invalid quality '.$s['quality'];
    }

    // Academy times in seed are 10:00–21:00 Cairo — academy label must be in that range typically
    $cairoHour = (int) $utc->copy()->timezone('Africa/Cairo')->format('G');
    if ($cairoHour < 10 || $cairoHour >= 21) {
        // still valid if window includes evening; just note
        echo "note: cairo hour={$cairoHour}".PHP_EOL;
    }

    // 4) Book + conflict
    $iso = $utc->toIso8601String();
    try {
        $booking = FreeTrialBookingService::book([
            'name' => 'Smoke Parent',
            'email' => 'smoke-tz-'.uniqid().'@example.com',
            'phone' => '512345678',
            'country_code' => '+966',
            'goal' => 'trial',
            'starts_at' => $iso,
            'timezone' => 'America/New_York',
            'us_state' => 'نيويورك',
        ]);
        echo 'booked_id='.$booking->id.' tz='.$booking->timezone.' state='.$booking->us_state.PHP_EOL;

        if ($booking->timezone !== 'America/New_York') {
            $errors[] = 'booking timezone not saved';
        }

        $again = FreeTrialBookingService::availableSlots(
            $utc->copy()->subHour(),
            $utc->copy()->addHour(),
            'America/New_York'
        );
        $stillThere = $again->contains(fn ($row) => $row['starts_at']->equalTo($utc));
        if ($stillThere) {
            $errors[] = 'booked slot still listed as available';
        }

        // cleanup
        $booking->delete();
    } catch (Throwable $e) {
        $errors[] = 'book failed: '.$e->getMessage();
    }
}

// 5) HTTP slots endpoint
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::create('/free-trial/slots?days=7&timezone=America/Chicago', 'GET');
$response = $kernel->handle($request);
$body = json_decode($response->getContent(), true);
echo 'http_slots_status='.$response->getStatusCode().' total='.($body['total'] ?? 'n/a').PHP_EOL;
if ($response->getStatusCode() !== 200) {
    $errors[] = 'HTTP slots failed: '.$response->getStatusCode();
} elseif (($body['viewer_timezone'] ?? '') !== 'America/Chicago') {
    $errors[] = 'HTTP viewer_timezone mismatch';
} elseif (! empty($body['slots_by_date'])) {
    $firstDate = array_key_first($body['slots_by_date']);
    $slot0 = $body['slots_by_date'][$firstDate][0] ?? null;
    if ($slot0 && empty($slot0['quality'])) {
        $errors[] = 'HTTP slot missing quality';
    }
    if ($slot0 && empty($slot0['time_academy'])) {
        $errors[] = 'HTTP slot missing time_academy';
    }
}
$kernel->terminate($request, $response);

Carbon::setTestNow();

if ($errors) {
    echo "FAIL\n";
    foreach ($errors as $e) {
        echo " - {$e}\n";
    }
    exit(1);
}

echo "OK all smoke checks passed\n";
exit(0);
