<?php

namespace Tests\Feature;

use App\Models\TutoringClassAttendance;
use App\Models\TutoringClassSession;
use App\Models\TutoringCohortEnrollment;
use App\Models\TutoringGroup;
use App\Models\TutoringGroupCohort;
use App\Models\User;
use App\Services\InstructorCohortCommandCenterService;
use App\Services\StudentSchoolHomeService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Tests\Support\BuildsFeatureSchema;
use Tests\TestCase;

class SchoolHomePhase1Test extends TestCase
{
    use BuildsFeatureSchema;

    protected function setUp(): void
    {
        parent::setUp();
        $this->buildFeatureSchema();
        $this->createExtraTables();
    }

    public function test_student_school_home_shows_primary_class_and_mission(): void
    {
        $student = User::factory()->create(['role' => 'student', 'is_active' => true, 'password' => Hash::make('password')]);
        $instructor = User::factory()->create(['role' => 'instructor', 'is_active' => true]);

        [$group, $cohort, $session] = $this->seedCohortWithSession($instructor, $student);

        $payload = app(StudentSchoolHomeService::class)->build($student);

        $this->assertTrue($payload['hasSchoolLife']);
        $this->assertNotNull($payload['primaryClass']);
        $this->assertSame($cohort->title, $payload['primaryClass']->title);
        $this->assertNotNull($payload['todayMission']);
        $this->assertSame($session->id, $payload['todayMission']->session_id);

        $this->actingAs($student)
            ->get(route('dashboard', ['lang' => 'ar']))
            ->assertOk()
            ->assertSee('dir="rtl"', false)
            ->assertSee('is-rtl', false)
            ->assertSee($cohort->title, false)
            ->assertSee('student-timeline.css', false)
            ->assertSee(__('student_timeline.timeline'), false)
            ->assertSee(__('student_timeline.subjects'), false)
            ->assertSee(__('student_timeline.events'), false)
            ->assertSee('stRailToggle', false)
            ->assertSee('img/student-timeline/nav/home.svg', false)
            ->assertSee(__('student_timeline.path_progress'), false)
            ->assertSee(__('student_timeline.session_credits'), false)
            ->assertSee(__('student_timeline.my_week'), false);

        $this->actingAs($student)
            ->get(route('dashboard', ['lang' => 'en']))
            ->assertOk()
            ->assertSee('dir="ltr"', false)
            ->assertSee('is-ltr', false)
            ->assertSee('Timeline', false);
    }

    public function test_instructor_command_center_flags_silent_student(): void
    {
        $student = User::factory()->create(['role' => 'student', 'is_active' => true]);
        $instructor = User::factory()->create(['role' => 'instructor', 'is_active' => true, 'password' => Hash::make('password')]);

        [$group, $cohort] = $this->seedCohortWithSession($instructor, $student);

        // Completed session without attendance → at risk
        $past = TutoringClassSession::query()
            ->where('tutoring_group_cohort_id', $cohort->id)
            ->first();
        $past->update([
            'starts_at' => now()->subDays(8),
            'ends_at' => now()->subDays(8)->addHour(),
            'status' => TutoringClassSession::STATUS_COMPLETED,
        ]);

        TutoringClassSession::create([
            'tutoring_group_cohort_id' => $cohort->id,
            'tutoring_group_id' => $group->id,
            'session_number' => 2,
            'title' => 'حصة 2',
            'starts_at' => now()->subDays(6),
            'ends_at' => now()->subDays(6)->addHour(),
            'status' => TutoringClassSession::STATUS_COMPLETED,
        ]);

        $center = app(InstructorCohortCommandCenterService::class)->build($cohort->fresh());

        $this->assertSame(1, $center['students_count']);
        $this->assertTrue($center['at_risk']->isNotEmpty());
        $this->assertTrue(
            $center['roster']->contains(fn ($row) => $row->name === $student->name && $row->is_at_risk)
        );
    }

    /**
     * @return array{0: TutoringGroup, 1: TutoringGroupCohort, 2: TutoringClassSession}
     */
    private function seedCohortWithSession(User $instructor, User $student): array
    {
        $starts = now()->addHours(2);

        $group = TutoringGroup::create([
            'type' => TutoringGroup::TYPE_COLLECTIVE,
            'title' => 'فصل المدرسة',
            'slug' => 'g-'.uniqid(),
            'instructor_id' => $instructor->id,
            'price' => 0,
            'capacity' => 20,
            'duration_minutes' => 60,
            'is_active' => true,
            'sort_order' => 0,
        ]);

        $cohort = TutoringGroupCohort::create([
            'tutoring_group_id' => $group->id,
            'title' => 'Class 3-A',
            'slug' => 'c-'.uniqid(),
            'starts_at' => $starts->copy()->subWeek(),
            'study_days' => [Carbon::now()->dayOfWeekIso],
            'study_time' => $starts->format('H:i'),
            'sessions_count' => 8,
            'session_duration_minutes' => 60,
            'timezone' => 'Africa/Cairo',
            'capacity' => 20,
            'enrolled_count' => 1,
            'min_enrollment' => 1,
            'status' => TutoringGroupCohort::STATUS_OPEN,
            'is_visible' => true,
            'sort_order' => 0,
        ]);

        TutoringCohortEnrollment::create([
            'tutoring_group_cohort_id' => $cohort->id,
            'user_id' => $student->id,
            'status' => TutoringCohortEnrollment::STATUS_ACTIVE,
            'enrolled_at' => now()->subDays(10),
        ]);

        $session = TutoringClassSession::create([
            'tutoring_group_cohort_id' => $cohort->id,
            'tutoring_group_id' => $group->id,
            'session_number' => 1,
            'title' => 'Mathematics — Lesson 12',
            'starts_at' => $starts,
            'ends_at' => $starts->copy()->addMinutes(60),
            'status' => TutoringClassSession::STATUS_SCHEDULED,
        ]);

        return [$group, $cohort, $session];
    }

    private function createExtraTables(): void
    {
        // Reuse helpers already created by BuildsFeatureSchema + StudentHomeScheduleTest patterns.
        if (! \Illuminate\Support\Facades\Schema::hasTable('tutoring_groups')) {
            $this->markTestSkipped('tutoring schema missing');
        }
    }
}
