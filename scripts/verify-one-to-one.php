<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\AdvancedCourse;
use App\Models\OneToOneSession;
use App\Models\StudentCourseEnrollment;
use App\Models\User;
use App\Services\CourseSubscriptionService;
use App\Services\OneToOneSessionService;
use Illuminate\Support\Facades\Schema;

$checks = [];

$checks['one_to_one_sessions_table'] = Schema::hasTable('one_to_one_sessions');
$checks['orders_auto_renew_column'] = Schema::hasColumn('orders', 'auto_renew');

$course = AdvancedCourse::query()
    ->where('is_active', true)
    ->where('delivery_type', CourseSubscriptionService::DELIVERY_ONE_TO_ONE)
    ->first();

$checks['has_one_to_one_course'] = (bool) $course;

if ($course) {
    $student = User::query()->where('role', 'student')->first();
    $checks['has_student'] = (bool) $student;

    if ($student) {
        $enrollment = StudentCourseEnrollment::query()
            ->where('user_id', $student->id)
            ->where('advanced_course_id', $course->id)
            ->first();

        if ($enrollment && CourseSubscriptionService::enrollmentGrantsAccess($enrollment)) {
            $before = OneToOneSession::where('student_course_enrollment_id', $enrollment->id)->count();
            if ($before === 0) {
                OneToOneSessionService::provisionSessionsForEnrollment($enrollment, $course);
            }
            $after = OneToOneSession::where('student_course_enrollment_id', $enrollment->id)->count();
            $checks['sessions_provisioned'] = $after >= 1;
            $checks['session_count'] = $after;
        } else {
            $checks['sessions_provisioned'] = 'skipped_no_active_enrollment';
        }
    }
}

$checks['courses_filter_route'] = (bool) app('router')->getRoutes()->getByName('public.courses');
$checks['student_sessions_route'] = (bool) app('router')->getRoutes()->getByName('student.one-to-one-sessions.index');
$checks['instructor_sessions_route'] = (bool) app('router')->getRoutes()->getByName('instructor.one-to-one-sessions.index');

echo json_encode($checks, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE).PHP_EOL;
