<?php

namespace Tests\Feature;

use App\Models\TutoringClassSession;
use App\Models\TutoringCohortEnrollment;
use App\Models\TutoringGroup;
use App\Models\TutoringGroupCohort;
use App\Models\User;
use App\Services\ClassFeedService;
use App\Services\StudentSchoolGameService;
use App\Services\TutoringClassService;
use Illuminate\Support\Facades\Hash;
use Tests\Support\BuildsFeatureSchema;
use Tests\TestCase;

class SchoolGamePhase2Test extends TestCase
{
    use BuildsFeatureSchema;

    protected function setUp(): void
    {
        parent::setUp();
        $this->buildFeatureSchema();
        $this->createGameTables();
    }

    public function test_attendance_awards_xp_streak_and_mission_progress(): void
    {
        $student = User::factory()->create(['role' => 'student', 'is_active' => true]);
        $instructor = User::factory()->create(['role' => 'instructor', 'is_active' => true]);
        [$group, $cohort, $session] = $this->seedClass($instructor, $student);

        TutoringClassService::markAttendanceOnJoin($session, $student);
        TutoringClassService::markAttendanceOnJoin($session, $student); // idempotent

        $xp = StudentSchoolGameService::totalXp($student);
        $this->assertGreaterThanOrEqual(50, $xp);
        $this->assertSame(1, \App\Models\StudentXpLedger::query()
            ->where('user_id', $student->id)
            ->where('reason', 'attendance_join')
            ->count());
        $this->assertSame(1, StudentSchoolGameService::streakFor($student)['current']);

        $missions = StudentSchoolGameService::missionsFor($student);
        $attend = $missions->firstWhere('code', 'daily_attend_1');
        $this->assertNotNull($attend);
        $this->assertTrue($attend->completed);

        $board = StudentSchoolGameService::cohortLeaderboard((int) $cohort->id);
        $this->assertTrue($board->contains(fn ($r) => $r->user_id === $student->id && $r->xp >= 50));
    }

    public function test_class_feed_post_and_moderation(): void
    {
        $student = User::factory()->create(['role' => 'student', 'is_active' => true, 'password' => Hash::make('x')]);
        $instructor = User::factory()->create(['role' => 'instructor', 'is_active' => true]);
        [, $cohort] = $this->seedClass($instructor, $student);

        $post = ClassFeedService::createPost($cohort, $student, 'حد فهم Question 7؟', 'question');
        $this->assertDatabaseHas('class_feed_posts', ['id' => $post->id, 'is_hidden' => 0]);

        ClassFeedService::hidePost($post, $instructor);
        $this->assertTrue($post->fresh()->is_hidden);

        $visibleToStudent = ClassFeedService::postsFor($cohort, $student);
        $this->assertFalse($visibleToStudent->contains('id', $post->id));
    }

    private function seedClass(User $instructor, User $student): array
    {
        $group = TutoringGroup::create([
            'type' => TutoringGroup::TYPE_COLLECTIVE,
            'title' => 'فصل',
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
            'starts_at' => now()->subDay(),
            'study_days' => [1],
            'study_time' => '18:00',
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
            'enrolled_at' => now()->subDays(3),
        ]);
        $session = TutoringClassSession::create([
            'tutoring_group_cohort_id' => $cohort->id,
            'tutoring_group_id' => $group->id,
            'session_number' => 1,
            'title' => 'Lesson 1',
            'starts_at' => now()->subMinutes(5),
            'ends_at' => now()->addMinutes(55),
            'status' => TutoringClassSession::STATUS_SCHEDULED,
        ]);

        return [$group, $cohort, $session];
    }

    private function createGameTables(): void
    {
        $migration = require database_path('migrations/2026_08_10_010000_create_school_gamification_tables.php');
        $migration->up();
    }
}
