<?php

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\OneToOneSessionService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

$from = Carbon::parse('2026-08-12 10:00:00');
$dates = OneToOneSessionService::expandWeeklyPattern([
    ['day_of_week' => 1, 'time' => '18:00'],
    ['day_of_week' => 3, 'time' => '18:00'],
], 4, $from);

$checks = [
    'pattern_count' => count($dates),
    'first' => $dates[0]->toDateTimeString(),
    'last' => $dates[count($dates) - 1]->toDateTimeString(),
    'series_col' => Schema::hasColumn('one_to_one_sessions', 'series_id') ? 'yes' : 'no',
    'routes_ok' => Route::has('admin.placement.store') && Route::has('student.one-to-one-sessions.book-instructor') ? 'yes' : 'no',
    'placement_view' => view()->exists('admin.placement.create') ? 'yes' : 'no',
    'instructor_view' => view()->exists('instructors.show') ? 'yes' : 'no',
];

foreach ($checks as $k => $v) {
    echo $k.'='.$v.PHP_EOL;
}

echo 'OK'.PHP_EOL;
