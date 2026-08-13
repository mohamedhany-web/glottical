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
                $table->timestamp('scheduled_at')->nullable();
                $table->unsignedInteger('duration_minutes')->default(50);
                $table->string('status')->default('scheduled');
                $table->timestamps();
            });
        }
    }
}
