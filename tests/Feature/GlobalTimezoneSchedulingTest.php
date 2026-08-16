<?php

namespace Tests\Feature;

use App\Models\OneToOneWeeklyAvailability;
use App\Models\TutoringGroup;
use App\Models\TutoringGroupCohort;
use App\Models\User;
use App\Services\OneToOneAvailabilityService;
use App\Services\TutoringClassService;
use App\Support\AppTimezone;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\Support\BuildsFeatureSchema;
use Tests\TestCase;

class GlobalTimezoneSchedulingTest extends TestCase
{
    use BuildsFeatureSchema;

    protected function setUp(): void
    {
        parent::setUp();
        $this->buildFeatureSchema();
        $this->extendSchema();
        config(['app.timezone' => 'UTC', 'platform.academy_timezone' => 'Africa/Cairo']);
        Carbon::setTestNow(Carbon::parse('2026-01-10 12:00:00', 'UTC'));
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_class_schedule_from_egypt_study_time_stores_utc(): void
    {
        [$group, $cohort] = $this->seedCohort();
        $sessions = TutoringClassService::generateSchedule($cohort);

        $this->assertNotEmpty($sessions);
        $first = $sessions->first();
        // study_time 18:00 Cairo => 16:00 UTC in January
        $this->assertSame('16:00', $first->starts_at->copy()->utc()->format('H:i'));
    }

    public function test_one_to_one_slots_use_academy_wall_clock(): void
    {
        $instructor = User::factory()->create(['role' => 'instructor', 'is_active' => true]);
        OneToOneWeeklyAvailability::create([
            'instructor_id' => $instructor->id,
            'day_of_week' => 1, // Monday
            'start_time' => '18:00:00',
            'end_time' => '19:00:00',
            'slot_duration_minutes' => 50,
            'is_active' => true,
        ]);

        $from = Carbon::parse('2026-01-12 00:00:00', 'UTC'); // Mon
        $to = Carbon::parse('2026-01-12 23:59:59', 'UTC');
        $slots = OneToOneAvailabilityService::availableSlots($instructor->id, $from, $to, 50);

        $this->assertTrue($slots->isNotEmpty());
        $slot = $slots->first();
        $this->assertSame('16:00', $slot['starts_at']->copy()->utc()->format('H:i'));
        $this->assertSame('11:00', $slot['starts_at']->copy()->timezone('America/New_York')->format('H:i'));
    }

    public function test_one_to_one_slots_use_instructor_america_wall_clock(): void
    {
        $instructor = User::factory()->create([
            'role' => 'instructor',
            'is_active' => true,
            'timezone' => 'America/New_York',
        ]);
        OneToOneWeeklyAvailability::create([
            'instructor_id' => $instructor->id,
            'day_of_week' => 1,
            'start_time' => '18:00:00',
            'end_time' => '19:00:00',
            'slot_duration_minutes' => 50,
            'is_active' => true,
        ]);

        $from = Carbon::parse('2026-01-12 00:00:00', 'UTC');
        $to = Carbon::parse('2026-01-13 05:00:00', 'UTC');
        $slots = OneToOneAvailabilityService::availableSlots($instructor->id, $from, $to, 50);

        $this->assertTrue($slots->isNotEmpty());
        $slot = $slots->first();
        $this->assertSame('23:00', $slot['starts_at']->copy()->utc()->format('H:i'));
        $this->assertSame('18:00', $slot['starts_at']->copy()->timezone('America/New_York')->format('H:i'));
        $this->assertTrue(
            OneToOneAvailabilityService::isSlotAvailable($instructor->id, $slot['starts_at'], 50)
        );

        $cairoEvening = AppTimezone::wallClockToUtc('2026-01-12', '18:00', 'Africa/Cairo');
        $this->assertFalse(
            OneToOneAvailabilityService::isSlotAvailable($instructor->id, $cairoEvening, 50)
        );
    }

    public function test_expand_weekly_pattern_honors_los_angeles_clock(): void
    {
        $from = Carbon::parse('2026-01-10 12:00:00', 'UTC');
        $dates = \App\Services\OneToOneSessionService::expandWeeklyPattern(
            [['day_of_week' => 1, 'time' => '18:00']],
            1,
            $from,
            'America/Los_Angeles'
        );

        $this->assertNotEmpty($dates);
        $first = $dates[0];
        $this->assertSame('18:00', $first->copy()->timezone('America/Los_Angeles')->format('H:i'));
        $this->assertSame('02:00', $first->copy()->utc()->format('H:i'));
    }

    public function test_cairo_noon_converts_for_pacific_student_and_calendar_iso(): void
    {
        $instructor = User::factory()->create([
            'role' => 'instructor',
            'is_active' => true,
            'timezone' => 'Africa/Cairo',
        ]);
        $student = User::factory()->create([
            'role' => 'student',
            'is_active' => true,
            'timezone' => 'America/Los_Angeles',
        ]);

        $utc = AppTimezone::wallClockToUtc('2026-01-15', '12:00', 'Africa/Cairo');
        $this->assertSame('10:00', $utc->format('H:i'));
        $this->assertSame('12:00', AppTimezone::formatFor($utc, 'Africa/Cairo', 'H:i'));
        $this->assertSame('02:00', AppTimezone::formatFor($utc, 'America/Los_Angeles', 'H:i'));

        $session = \App\Models\OneToOneSession::create([
            'instructor_id' => $instructor->id,
            'student_id' => $student->id,
            'session_number' => 1,
            'duration_minutes' => 50,
            'status' => \App\Models\OneToOneSession::STATUS_SCHEDULED,
            'scheduled_at' => $utc,
        ]);

        $payload = \App\Services\TeachingCalendarService::toFullCalendar(
            \App\Services\TeachingCalendarService::forInstructor($instructor, $utc->copy()->subDay(), $utc->copy()->addDay())
        );
        $this->assertNotEmpty($payload);
        $this->assertStringContainsString('T10:00:00', $payload[0]['start']);

        $studentPayload = \App\Services\TeachingCalendarService::toFullCalendar(
            \App\Services\TeachingCalendarService::lessonsForStudent($student, $utc->copy()->subDay(), $utc->copy()->addDay())
        );
        $this->assertNotEmpty($studentPayload);
        $this->assertSame($payload[0]['start'], $studentPayload[0]['start']);
        $this->assertSame('one_to_one_'.$session->id, $payload[0]['id']);
    }

    public function test_egypt_student_sees_new_york_slot_in_cairo_clock(): void
    {
        $utc = AppTimezone::wallClockToUtc('2026-01-12', '18:00', 'America/New_York');
        $this->assertSame('2026-01-13 01:00', AppTimezone::formatFor($utc, 'Africa/Cairo', 'Y-m-d H:i'));

        $parts = AppTimezone::dualLabel($utc, 'America/New_York', 'en', 'g:i A');
        $this->assertSame('6:00 PM', $parts['primary']);
        $this->assertNotNull($parts['secondary']);
        $this->assertStringContainsString('بتوقيت مصر', $parts['secondary']);
    }

    public function test_instructor_http_schedules_datetime_local_in_new_york(): void
    {
        $instructor = User::factory()->create([
            'role' => 'instructor',
            'is_active' => true,
            'timezone' => 'America/New_York',
            'password' => Hash::make('password'),
        ]);
        $student = User::factory()->create([
            'role' => 'student',
            'is_active' => true,
            'timezone' => 'Africa/Cairo',
            'password' => Hash::make('password'),
        ]);
        OneToOneWeeklyAvailability::create([
            'instructor_id' => $instructor->id,
            'day_of_week' => 1,
            'start_time' => '18:00:00',
            'end_time' => '19:00:00',
            'slot_duration_minutes' => 50,
            'is_active' => true,
        ]);
        $session = \App\Models\OneToOneSession::create([
            'instructor_id' => $instructor->id,
            'student_id' => $student->id,
            'session_number' => 1,
            'duration_minutes' => 50,
            'status' => \App\Models\OneToOneSession::STATUS_PENDING,
        ]);

        $this->withoutMiddleware(\App\Http\Middleware\EnsureInstructorPanelAccess::class)
            ->actingAs($instructor)
            ->post(route('instructor.one-to-one-sessions.schedule', $session), [
                'scheduled_at' => '2026-01-12T18:00',
                'timezone' => 'America/New_York',
                'duration_minutes' => 50,
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $scheduled = $session->fresh()->scheduled_at;
        $this->assertNotNull($scheduled);
        $this->assertSame('23:00', $scheduled->copy()->utc()->format('H:i'));
        $this->assertSame('18:00', $scheduled->copy()->timezone('America/New_York')->format('H:i'));
        $this->assertSame('01:00', $scheduled->copy()->timezone('Africa/Cairo')->format('H:i'));
        $this->assertSame(\App\Models\OneToOneSession::STATUS_SCHEDULED, $session->fresh()->status);
    }

    public function test_timezone_sync_sets_blank_user_timezone(): void
    {
        $student = User::factory()->create([
            'role' => 'student',
            'is_active' => true,
            'timezone' => null,
        ]);

        $this->actingAs($student)
            ->postJson(route('account.timezone.sync'), ['timezone' => 'America/New_York'])
            ->assertOk()
            ->assertJson(['ok' => true, 'updated' => true]);

        $this->assertSame('America/New_York', $student->fresh()->timezone);
    }

    public function test_timezone_sync_does_not_overwrite_manual_choice_without_force(): void
    {
        $student = User::factory()->create([
            'role' => 'student',
            'is_active' => true,
            'timezone' => 'Asia/Riyadh',
        ]);

        $this->actingAs($student)
            ->postJson(route('account.timezone.sync'), ['timezone' => 'America/New_York', 'force' => false])
            ->assertOk()
            ->assertJson(['updated' => false, 'timezone' => 'Asia/Riyadh']);

        $this->assertSame('Asia/Riyadh', $student->fresh()->timezone);
    }

    public function test_dual_label_for_us_student(): void
    {
        $utc = AppTimezone::wallClockToUtc('2026-01-15', '18:00');
        $parts = AppTimezone::dualLabel($utc, 'America/New_York', 'en', 'g:i A');
        $this->assertSame('11:00 AM', $parts['primary']);
        $this->assertNotNull($parts['secondary']);
    }

    /**
     * @return array{0: TutoringGroup, 1: TutoringGroupCohort}
     */
    protected function seedCohort(): array
    {
        $instructor = User::factory()->create([
            'role' => 'instructor',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);

        $group = TutoringGroup::create([
            'type' => TutoringGroup::TYPE_COLLECTIVE,
            'title' => 'فصل توقيت',
            'slug' => 'tz-class-'.uniqid(),
            'instructor_id' => $instructor->id,
            'price' => 0,
            'capacity' => 10,
            'duration_minutes' => 60,
            'sessions_per_month' => 4,
            'is_active' => true,
            'sort_order' => 0,
        ]);

        $startsLocal = AppTimezone::parseLocalToUtc('2026-01-12 00:00:00', 'Africa/Cairo');
        $cohort = TutoringGroupCohort::create([
            'tutoring_group_id' => $group->id,
            'title' => 'دفعة توقيت',
            'slug' => 'tz-cohort-'.uniqid(),
            'starts_at' => $startsLocal,
            'study_days' => [1], // Monday
            'study_time' => '18:00',
            'sessions_count' => 2,
            'session_duration_minutes' => 60,
            'timezone' => 'Africa/Cairo',
            'capacity' => 10,
            'enrolled_count' => 0,
            'min_enrollment' => 1,
            'status' => TutoringGroupCohort::STATUS_OPEN,
            'is_visible' => true,
            'sort_order' => 0,
        ]);

        return [$group, $cohort];
    }

    protected function extendSchema(): void
    {
        if (! Schema::hasColumn('users', 'timezone')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('timezone', 64)->nullable();
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

        if (! Schema::hasTable('one_to_one_weekly_availability')) {
            Schema::create('one_to_one_weekly_availability', function (Blueprint $table) {
                $table->id();
                $table->foreignId('instructor_id');
                $table->unsignedTinyInteger('day_of_week');
                $table->time('start_time');
                $table->time('end_time');
                $table->unsignedSmallInteger('slot_duration_minutes')->default(50);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('one_to_one_sessions')) {
            Schema::create('one_to_one_sessions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('instructor_id');
                $table->foreignId('student_id')->nullable();
                $table->unsignedInteger('session_number')->default(1);
                $table->timestamp('scheduled_at')->nullable();
                $table->unsignedInteger('duration_minutes')->default(50);
                $table->string('status')->default('pending_schedule');
                $table->unsignedBigInteger('classroom_meeting_id')->nullable();
                $table->unsignedBigInteger('booked_by_user_id')->nullable();
                $table->unsignedBigInteger('student_service_entitlement_id')->nullable();
                $table->unsignedBigInteger('advanced_course_id')->nullable();
                $table->unsignedBigInteger('student_course_enrollment_id')->nullable();
                $table->text('notes')->nullable();
                $table->string('series_id', 36)->nullable();
                $table->timestamps();
            });
        }
    }
}
