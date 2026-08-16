<?php

namespace Tests\Feature;

use App\Http\Middleware\EnsureInstructorPanelAccess;
use App\Models\OneToOneWeeklyAvailability;
use App\Models\TutorWorkSchedule;
use App\Models\User;
use App\Services\OneToOneAvailabilityService;
use App\Services\TutoringGroupAvailabilityService;
use App\Support\WeeklyScheduleTime;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Tests\Support\BuildsFeatureSchema;
use Tests\TestCase;

class InstructorWeeklyScheduleTest extends TestCase
{
    use BuildsFeatureSchema;

    protected function setUp(): void
    {
        parent::setUp();
        $this->buildFeatureSchema();
        $this->buildScheduleSchema();
        Carbon::setTestNow(Carbon::parse('2026-08-16 10:00:00', config('app.timezone')));
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    protected function buildScheduleSchema(): void
    {
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

        Schema::create('tutor_work_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instructor_id');
            $table->unsignedTinyInteger('day_of_week');
            $table->time('start_time');
            $table->time('end_time');
            $table->unsignedSmallInteger('slot_duration_minutes')->default(60);
            $table->string('applies_to')->default('both');
            $table->boolean('is_active')->default(true);
            $table->string('note')->nullable();
            $table->timestamps();
        });

        Schema::create('one_to_one_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instructor_id');
            $table->foreignId('student_id')->nullable();
            $table->string('status')->default('pending_schedule');
            $table->timestamp('scheduled_at')->nullable();
            $table->unsignedSmallInteger('duration_minutes')->default(50);
            $table->timestamps();
        });
    }

    public function test_teacher_can_save_more_than_three_weekly_windows(): void
    {
        $instructor = $this->makeInstructor();

        OneToOneAvailabilityService::syncRules($instructor->id, [
            ['day_of_week' => 1, 'start_time' => '16:00', 'end_time' => '18:00', 'slot_duration_minutes' => 50],
            ['day_of_week' => 2, 'start_time' => '16:00:00', 'end_time' => '18:00:00', 'slot_duration_minutes' => 50],
            ['day_of_week' => 3, 'start_time' => '16:00', 'end_time' => '18:00', 'slot_duration_minutes' => 50],
            ['day_of_week' => 4, 'start_time' => '16:00', 'end_time' => '18:00', 'slot_duration_minutes' => 50],
            ['day_of_week' => 5, 'start_time' => '20:00', 'end_time' => '00:00', 'slot_duration_minutes' => 50],
        ]);

        $this->assertSame(5, OneToOneWeeklyAvailability::query()->where('instructor_id', $instructor->id)->count());
    }

    public function test_short_window_is_rejected_instead_of_silently_dropped(): void
    {
        $instructor = $this->makeInstructor();

        OneToOneAvailabilityService::syncRules($instructor->id, [
            ['day_of_week' => 1, 'start_time' => '16:00', 'end_time' => '18:00', 'slot_duration_minutes' => 50],
        ]);

        try {
            OneToOneAvailabilityService::syncRules($instructor->id, [
                ['day_of_week' => 1, 'start_time' => '16:00', 'end_time' => '18:00', 'slot_duration_minutes' => 50],
                ['day_of_week' => 2, 'start_time' => '18:00', 'end_time' => '18:20', 'slot_duration_minutes' => 50],
            ]);
            $this->fail('Expected validation exception');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('slots.1.end_time', $e->errors());
        }

        $this->assertSame(1, OneToOneWeeklyAvailability::query()->where('instructor_id', $instructor->id)->count());
    }

    public function test_three_hour_window_of_sixty_minutes_yields_three_slots_four_hours_yields_four(): void
    {
        $this->assertSame(3, WeeklyScheduleTime::slotCount('09:00', '12:00', 60));
        $this->assertSame(4, WeeklyScheduleTime::slotCount('09:00', '13:00', 60));
        $this->assertSame(4, WeeklyScheduleTime::slotCount('20:00', '00:00', 50));

        $instructor = $this->makeInstructor();
        OneToOneAvailabilityService::syncRules($instructor->id, [
            ['day_of_week' => 1, 'start_time' => '09:00', 'end_time' => '13:00', 'slot_duration_minutes' => 60],
        ]);

        $slots = OneToOneAvailabilityService::availableSlots(
            $instructor->id,
            now(),
            now()->addDays(8),
            60
        );

        $monday = $slots->filter(fn ($slot) => $slot['starts_at']->timezone(config('app.timezone'))->toDateString() === '2026-08-17');
        $this->assertSame(4, $monday->count());
    }

    public function test_instructor_http_saves_four_windows_with_seconds(): void
    {
        $instructor = $this->makeInstructor();

        $this->withoutMiddleware(EnsureInstructorPanelAccess::class)
            ->actingAs($instructor)
            ->post(route('instructor.one-to-one-availability.update'), [
                'slots' => [
                    ['day_of_week' => 1, 'start_time' => '16:00:00', 'end_time' => '18:00:00', 'slot_duration_minutes' => 50],
                    ['day_of_week' => 2, 'start_time' => '16:00', 'end_time' => '18:00', 'slot_duration_minutes' => 50],
                    ['day_of_week' => 3, 'start_time' => '16:00', 'end_time' => '18:00', 'slot_duration_minutes' => 50],
                    ['day_of_week' => 4, 'start_time' => '16:00', 'end_time' => '18:00', 'slot_duration_minutes' => 50],
                ],
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertSame(4, OneToOneWeeklyAvailability::query()->where('instructor_id', $instructor->id)->count());
    }

    public function test_group_schedule_saves_four_windows(): void
    {
        $instructor = $this->makeInstructor();

        TutoringGroupAvailabilityService::syncRules($instructor->id, [
            ['day_of_week' => 1, 'start_time' => '16:00', 'end_time' => '18:00', 'slot_duration_minutes' => 60, 'applies_to' => 'both'],
            ['day_of_week' => 2, 'start_time' => '16:00', 'end_time' => '18:00', 'slot_duration_minutes' => 60, 'applies_to' => 'both'],
            ['day_of_week' => 3, 'start_time' => '16:00', 'end_time' => '18:00', 'slot_duration_minutes' => 60, 'applies_to' => 'both'],
            ['day_of_week' => 4, 'start_time' => '16:00', 'end_time' => '18:00', 'slot_duration_minutes' => 60, 'applies_to' => 'both'],
        ]);

        $this->assertSame(4, TutorWorkSchedule::query()->where('instructor_id', $instructor->id)->count());
    }

    protected function makeInstructor(): User
    {
        return User::factory()->create([
            'role' => 'instructor',
            'is_active' => true,
            'password' => Hash::make('password'),
        ]);
    }
}
