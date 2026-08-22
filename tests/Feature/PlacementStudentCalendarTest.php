<?php

namespace Tests\Feature;

use App\Models\OneToOneSession;
use App\Models\ServicePackage;
use App\Models\TutoringClassSession;
use App\Models\TutoringCohortEnrollment;
use App\Models\TutoringGroup;
use App\Models\TutoringGroupBooking;
use App\Models\TutoringGroupCohort;
use App\Models\User;
use App\Services\OneToOneSessionService;
use App\Services\StudentEntitlementService;
use App\Services\TeachingCalendarService;
use App\Services\TutoringClassService;
use App\Services\TutoringGroupOrchestrationService;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\Support\BuildsFeatureSchema;
use Tests\TestCase;

class PlacementStudentCalendarTest extends TestCase
{
    use BuildsFeatureSchema;

    protected function setUp(): void
    {
        parent::setUp();
        $this->buildFeatureSchema();
        $this->ensureOneToOneTables();
        Carbon::setTestNow(Carbon::parse('2026-08-20 10:00:00', 'UTC'));
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    protected function ensureOneToOneTables(): void
    {
        if (! Schema::hasTable('one_to_one_sessions')) {
            Schema::create('one_to_one_sessions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('student_course_enrollment_id')->nullable();
                $table->unsignedBigInteger('student_service_entitlement_id')->nullable();
                $table->unsignedBigInteger('advanced_course_id')->nullable();
                $table->foreignId('instructor_id');
                $table->foreignId('student_id');
                $table->unsignedInteger('session_number')->default(1);
                $table->timestamp('scheduled_at')->nullable();
                $table->unsignedSmallInteger('duration_minutes')->default(50);
                $table->boolean('is_private_lecture')->default(false);
                $table->string('system_channel')->nullable();
                $table->string('status', 32)->default('pending_schedule');
                $table->unsignedBigInteger('classroom_meeting_id')->nullable();
                $table->unsignedBigInteger('booked_by_user_id')->nullable();
                $table->text('notes')->nullable();
                $table->string('series_id', 36)->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('advanced_courses')) {
            Schema::create('advanced_courses', function (Blueprint $table) {
                $table->id();
                $table->foreignId('instructor_id')->nullable();
                $table->string('title')->nullable();
                $table->boolean('is_active')->default(true);
                $table->string('delivery_type')->nullable();
                $table->timestamps();
            });
        }

        if (Schema::hasTable('tutoring_group_bookings') && ! Schema::hasColumn('tutoring_group_bookings', 'cohort_id')) {
            Schema::table('tutoring_group_bookings', function (Blueprint $table) {
                $table->unsignedBigInteger('cohort_id')->nullable();
            });
        }

        if (Schema::hasTable('tutoring_group_bookings') && ! Schema::hasColumn('tutoring_group_bookings', 'classroom_meeting_id')) {
            Schema::table('tutoring_group_bookings', function (Blueprint $table) {
                $table->unsignedBigInteger('classroom_meeting_id')->nullable();
            });
        }
    }

    public function test_private_placement_sessions_appear_on_student_calendar(): void
    {
        $student = User::factory()->create([
            'role' => 'student',
            'is_active' => true,
            'password' => Hash::make('password'),
        ]);
        $instructor = User::factory()->create([
            'role' => 'instructor',
            'is_active' => true,
            'password' => Hash::make('password'),
        ]);
        $admin = User::factory()->create([
            'role' => 'super_admin',
            'is_active' => true,
            'password' => Hash::make('password'),
        ]);

        $entitlement = StudentEntitlementService::grantManual(
            (int) $student->id,
            ServicePackage::SCOPE_PRIVATE_LESSONS,
            3,
            null,
            90,
            'placement calendar credit'
        );

        $at = Carbon::parse('2026-08-25 15:00:00', 'UTC');
        $sessions = OneToOneSessionService::bookMultipleWithInstructor(
            $student,
            $instructor,
            [$at],
            $entitlement,
            $admin,
            'تسكين اختبار تقويم',
            false
        );

        $session = $sessions->first();
        $this->assertSame(OneToOneSession::STATUS_SCHEDULED, $session->status);
        $this->assertNotNull($session->scheduled_at);

        $lessons = TeachingCalendarService::lessonsForStudent(
            $student,
            $at->copy()->subDay(),
            $at->copy()->addDay()
        );
        $this->assertTrue(
            $lessons->contains(fn ($e) => ($e->calendar_id ?? null) === 'one_to_one_'.$session->id),
            'Scheduled 1:1 placement must appear in lessonsForStudent'
        );

        $payload = TeachingCalendarService::toFullCalendar($lessons);
        $this->assertTrue(
            collect($payload)->contains(fn ($row) => ($row['id'] ?? null) === 'one_to_one_'.$session->id),
            'Scheduled 1:1 placement must serialize for FullCalendar'
        );
    }

    public function test_confirming_group_booking_with_cohort_enrolls_and_shows_class_on_calendar(): void
    {
        $instructor = User::factory()->create(['role' => 'instructor', 'is_active' => true]);
        $student = User::factory()->create([
            'role' => 'student',
            'is_active' => true,
            'password' => Hash::make('password'),
        ]);

        $group = TutoringGroup::create([
            'type' => TutoringGroup::TYPE_COLLECTIVE,
            'title' => 'فصل تقويم',
            'slug' => 'cal-group-'.uniqid(),
            'instructor_id' => $instructor->id,
            'price' => 0,
            'capacity' => 10,
            'duration_minutes' => 60,
            'is_active' => true,
            'sort_order' => 0,
        ]);

        $startsAt = Carbon::parse('2026-08-22 16:00:00', 'UTC');
        $cohort = TutoringGroupCohort::create([
            'tutoring_group_id' => $group->id,
            'title' => 'دفعة تقويم',
            'slug' => 'cal-cohort-'.uniqid(),
            'starts_at' => $startsAt,
            'study_days' => [6],
            'study_time' => '18:00',
            'sessions_count' => 2,
            'session_duration_minutes' => 60,
            'timezone' => 'UTC',
            'capacity' => 10,
            'enrolled_count' => 0,
            'min_enrollment' => 1,
            'status' => TutoringGroupCohort::STATUS_OPEN,
            'is_visible' => true,
            'sort_order' => 0,
        ]);

        TutoringClassService::generateSchedule($cohort);

        $booking = TutoringGroupBooking::create([
            'tutoring_group_id' => $group->id,
            'cohort_id' => $cohort->id,
            'payment_status' => TutoringGroupBooking::PAYMENT_PAID,
            'instructor_id' => $instructor->id,
            'user_id' => $student->id,
            'starts_at' => $startsAt,
            'ends_at' => $startsAt->copy()->addHour(),
            'status' => TutoringGroupBooking::STATUS_PENDING,
            'admin_notes' => 'تسكين فصل اختبار',
        ]);

        TutoringGroupOrchestrationService::confirmBooking($booking);

        $this->assertDatabaseHas('tutoring_cohort_enrollments', [
            'tutoring_group_cohort_id' => $cohort->id,
            'user_id' => $student->id,
            'status' => TutoringCohortEnrollment::STATUS_ACTIVE,
        ]);

        $classSession = TutoringClassSession::query()
            ->where('tutoring_group_cohort_id', $cohort->id)
            ->orderBy('starts_at')
            ->first();
        $this->assertNotNull($classSession);

        $lessons = TeachingCalendarService::lessonsForStudent(
            $student,
            $classSession->starts_at->copy()->subDay(),
            $classSession->starts_at->copy()->addWeeks(4)
        );

        $this->assertTrue(
            $lessons->contains(fn ($e) => ($e->calendar_id ?? null) === 'class_session_'.$classSession->id),
            'Cohort class sessions must appear after placement/confirm enrollment'
        );
        $this->assertTrue(
            $lessons->contains(fn ($e) => ($e->calendar_id ?? null) === 'group_booking_'.$booking->id),
            'Confirmed group booking itself must also appear'
        );
    }
}
