<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\AdvancedCourse;
use App\Models\OneToOneSession;
use App\Models\StudentCourseEnrollment;
use App\Models\User;
use App\Services\CourseSubscriptionService;
use App\Services\OneToOneAvailabilityService;
use App\Services\OneToOneSessionService;
use Carbon\Carbon;

$instructor = User::query()->whereIn('role', ['instructor', 'teacher'])->first();
$student = User::query()->where('role', 'student')->first();

if (! $instructor || ! $student) {
    echo json_encode(['ok' => false, 'error' => 'missing_instructor_or_student'], JSON_PRETTY_PRINT).PHP_EOL;
    exit(1);
}

$course = AdvancedCourse::create([
    'title' => '[TEST] كورس فردي 1:1',
    'instructor_id' => $instructor->id,
    'delivery_type' => CourseSubscriptionService::DELIVERY_ONE_TO_ONE,
    'billing_mode' => CourseSubscriptionService::BILLING_MONTHLY,
    'monthly_price' => 500,
    'price' => 500,
    'is_active' => true,
    'description' => 'اختبار تلقائي',
]);

$enrollment = StudentCourseEnrollment::create([
    'user_id' => $student->id,
    'advanced_course_id' => $course->id,
    'status' => 'active',
    'billing_mode' => CourseSubscriptionService::BILLING_MONTHLY,
    'expires_at' => Carbon::now()->addMonth(),
    'auto_renew' => true,
    'enrolled_at' => now(),
]);

OneToOneSessionService::provisionSessionsForEnrollment($enrollment, $course);
$sessionCount = OneToOneSession::where('student_course_enrollment_id', $enrollment->id)->count();

$session = OneToOneSession::where('student_course_enrollment_id', $enrollment->id)->first();
$scheduleAt = Carbon::now()->addDays(2)->setTime(10, 0);
OneToOneAvailabilityService::syncRules($instructor->id, [[
    'day_of_week' => $scheduleAt->isoWeekday(),
    'start_time' => '09:00',
    'end_time' => '17:00',
    'slot_duration_minutes' => 60,
]]);
OneToOneSessionService::scheduleSession($session, $scheduleAt, 60, $instructor);
$session->refresh();

$result = [
    'ok' => $sessionCount === 4 && $session->status === OneToOneSession::STATUS_SCHEDULED && $session->classroom_meeting_id,
    'session_count' => $sessionCount,
    'scheduled_status' => $session->status,
    'has_meeting' => (bool) $session->classroom_meeting_id,
    'course_id' => $course->id,
];

// cleanup
OneToOneSession::where('student_course_enrollment_id', $enrollment->id)->delete();
$enrollment->delete();
$course->delete();

echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE).PHP_EOL;
exit($result['ok'] ? 0 : 1);
