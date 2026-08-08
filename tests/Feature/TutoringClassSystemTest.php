<?php

namespace Tests\Feature;

use App\Models\ClassroomMeeting;
use App\Models\TutoringClassAttendance;
use App\Models\TutoringClassSession;
use App\Models\TutoringCohortEnrollment;
use App\Models\TutoringGroup;
use App\Models\TutoringGroupCohort;
use App\Models\User;
use App\Services\TutoringClassService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use InvalidArgumentException;
use Tests\Support\BuildsFeatureSchema;
use Tests\TestCase;

class TutoringClassSystemTest extends TestCase
{
    use BuildsFeatureSchema;

    protected function setUp(): void
    {
        parent::setUp();
        $this->buildFeatureSchema();
    }

    public function test_generates_schedule_shared_room_and_enrollment(): void
    {
        [$group, $cohort, $instructor, $student] = $this->seedClass();

        $sessions = TutoringClassService::generateSchedule($cohort);
        $this->assertCount(4, $sessions);
        $this->assertTrue($sessions->every(fn ($s) => $s->status === TutoringClassSession::STATUS_SCHEDULED));

        $first = $sessions->first();
        $meeting = TutoringClassService::ensureSessionMeeting($first);
        $this->assertNotEmpty($meeting->code);
        $this->assertSame($meeting->id, $first->fresh()->classroom_meeting_id);
        $this->assertTrue(data_get($meeting->settings, 'shared_class_room'));

        $again = TutoringClassService::ensureSessionMeeting($first->fresh());
        $this->assertSame($meeting->id, $again->id);

        $enrollment = TutoringClassService::enrollStudent($cohort->fresh(), $student);
        $this->assertSame(TutoringCohortEnrollment::STATUS_ACTIVE, $enrollment->status);
        $this->assertSame(1, (int) $cohort->fresh()->enrolled_count);
        $this->assertTrue(TutoringClassService::userCanAccessCohort($student, $cohort->fresh()));
        $this->assertFalse(TutoringClassService::userCanAccessCohort(
            User::factory()->create(['role' => 'student', 'is_active' => true]),
            $cohort->fresh()
        ));
    }

    public function test_join_session_marks_attendance_and_redirects_to_classroom(): void
    {
        [$group, $cohort, $instructor, $student] = $this->seedClass();
        $sessions = TutoringClassService::generateSchedule($cohort);
        $session = $sessions->first();
        $session->update([
            'starts_at' => now()->addMinutes(10),
            'ends_at' => now()->addMinutes(70),
        ]);
        TutoringClassService::enrollStudent($cohort, $student);
        TutoringClassService::ensureSessionMeeting($session->fresh());

        $response = $this->actingAs($student)
            ->post(route('student.classes.sessions.join', $session));

        $meeting = $session->fresh()->classroomMeeting;
        $response->assertRedirect(url('classroom/join/'.$meeting->code));
        $this->assertSame(TutoringClassSession::STATUS_LIVE, $session->fresh()->status);
        $this->assertDatabaseHas('tutoring_class_attendances', [
            'tutoring_class_session_id' => $session->id,
            'user_id' => $student->id,
            'status' => TutoringClassAttendance::STATUS_PRESENT,
        ]);
    }

    public function test_join_blocked_for_non_enrolled_student(): void
    {
        [$group, $cohort] = $this->seedClass();
        $outsider = User::factory()->create([
            'role' => 'student',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $sessions = TutoringClassService::generateSchedule($cohort);
        $session = $sessions->first();
        $session->update([
            'starts_at' => now()->addMinutes(5),
            'ends_at' => now()->addMinutes(65),
        ]);

        $this->actingAs($outsider)
            ->post(route('student.classes.sessions.join', $session))
            ->assertForbidden();
    }

    public function test_admin_generate_schedule_and_add_student(): void
    {
        [$group, $cohort, $instructor, $student] = $this->seedClass();
        $admin = User::factory()->create([
            'role' => 'super_admin',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->post(route('admin.tutoring-groups.classes.generate-schedule', [$group, $cohort]))
            ->assertRedirect(route('admin.tutoring-groups.classes.show', [$group, $cohort]));

        $this->assertGreaterThanOrEqual(4, TutoringClassSession::query()
            ->where('tutoring_group_cohort_id', $cohort->id)
            ->count());
        $this->assertGreaterThanOrEqual(4, ClassroomMeeting::query()->count());

        $this->actingAs($admin)
            ->post(route('admin.tutoring-groups.classes.enrollments.store', [$group, $cohort]), [
                'user_id' => $student->id,
                'notes' => 'manual',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('tutoring_cohort_enrollments', [
            'tutoring_group_cohort_id' => $cohort->id,
            'user_id' => $student->id,
            'status' => TutoringCohortEnrollment::STATUS_ACTIVE,
        ]);
    }

    public function test_capacity_blocks_extra_enrollment(): void
    {
        [$group, $cohort] = $this->seedClass(capacity: 1);
        $s1 = User::factory()->create(['role' => 'student', 'is_active' => true]);
        $s2 = User::factory()->create(['role' => 'student', 'is_active' => true]);

        TutoringClassService::enrollStudent($cohort, $s1);
        $this->expectException(InvalidArgumentException::class);
        TutoringClassService::enrollStudent($cohort->fresh(), $s2);
    }

    public function test_cancel_enrollment_and_session(): void
    {
        [$group, $cohort, $instructor, $student] = $this->seedClass();
        $enrollment = TutoringClassService::enrollStudent($cohort, $student);
        TutoringClassService::cancelEnrollment($enrollment);
        $this->assertSame(TutoringCohortEnrollment::STATUS_CANCELLED, $enrollment->fresh()->status);
        $this->assertSame(0, (int) $cohort->fresh()->enrolled_count);

        $sessions = TutoringClassService::generateSchedule($cohort);
        TutoringClassService::cancelSession($sessions->first());
        $this->assertSame(TutoringClassSession::STATUS_CANCELLED, $sessions->first()->fresh()->status);
    }

    public function test_free_cohort_enroll_via_student_route(): void
    {
        [$group, $cohort] = $this->seedClass(price: 0);
        $student = User::factory()->create(['role' => 'student', 'is_active' => true]);

        $this->actingAs($student)
            ->post(route('student.classes.enroll', $cohort))
            ->assertRedirect(route('student.classes.show', $cohort));

        $this->assertDatabaseHas('tutoring_cohort_enrollments', [
            'tutoring_group_cohort_id' => $cohort->id,
            'user_id' => $student->id,
            'status' => TutoringCohortEnrollment::STATUS_ACTIVE,
        ]);
    }

    public function test_paid_cohort_enroll_redirects_to_checkout(): void
    {
        [$group, $cohort] = $this->seedClass(price: 199);
        $student = User::factory()->create(['role' => 'student', 'is_active' => true]);

        $this->actingAs($student)
            ->post(route('student.classes.enroll', $cohort))
            ->assertRedirect(route('public.groups.checkout', [
                'slug' => $group->slug,
                'cohort' => $cohort->id,
            ]));

        $this->assertDatabaseMissing('tutoring_cohort_enrollments', [
            'tutoring_group_cohort_id' => $cohort->id,
            'user_id' => $student->id,
        ]);
    }

    /**
     * @return array{0: TutoringGroup, 1: TutoringGroupCohort, 2: User, 3: User}
     */
    protected function seedClass(int $capacity = 10, float $price = 0): array
    {
        $instructor = User::factory()->create([
            'role' => 'instructor',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $student = User::factory()->create([
            'role' => 'student',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);

        $group = TutoringGroup::create([
            'type' => TutoringGroup::TYPE_COLLECTIVE,
            'title' => 'فصل تجريبي',
            'slug' => 'class-demo-'.uniqid(),
            'instructor_id' => $instructor->id,
            'price' => $price,
            'capacity' => $capacity,
            'duration_minutes' => 60,
            'sessions_per_month' => 4,
            'is_active' => true,
            'sort_order' => 0,
        ]);

        $start = Carbon::now()->next(Carbon::SATURDAY)->setTime(18, 0);

        $cohort = TutoringGroupCohort::create([
            'tutoring_group_id' => $group->id,
            'title' => 'دفعة أ',
            'slug' => 'cohort-a-'.uniqid(),
            'starts_at' => $start,
            'study_days' => [6, 2],
            'study_time' => '18:00',
            'sessions_count' => 4,
            'session_duration_minutes' => 60,
            'timezone' => 'Africa/Cairo',
            'capacity' => $capacity,
            'enrolled_count' => 0,
            'min_enrollment' => 1,
            'status' => TutoringGroupCohort::STATUS_OPEN,
            'is_visible' => true,
            'sort_order' => 0,
        ]);

        return [$group, $cohort, $instructor, $student];
    }
}
