<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\Support\BuildsFeatureSchema;
use Tests\TestCase;

class StudentMeetingHostGuardTest extends TestCase
{
    use BuildsFeatureSchema;

    protected function setUp(): void
    {
        parent::setUp();
        $this->buildFeatureSchema();
    }

    private function student(): User
    {
        return User::factory()->create([
            'role' => 'student',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
    }

    public function test_student_cannot_open_classroom_create_page(): void
    {
        $student = $this->student();

        $this->actingAs($student)
            ->get(route('student.classroom.create'))
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('error');
    }

    public function test_student_cannot_post_classroom_store(): void
    {
        $student = $this->student();

        $this->actingAs($student)
            ->post(route('student.classroom.store'), [
                'title' => 'اجتماع طالب',
                'max_participants' => 10,
            ])
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('error');
    }

    public function test_student_cannot_open_classroom_index(): void
    {
        $student = $this->student();

        $this->actingAs($student)
            ->get(route('student.classroom.index'))
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('error');
    }

    public function test_student_cannot_open_instructor_live_session_create(): void
    {
        $student = $this->student();

        $this->actingAs($student)
            ->get(route('instructor.live-sessions.create'))
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('error');
    }

    public function test_my_courses_index_stays_hidden_when_courses_disabled(): void
    {
        $student = $this->student();

        $this->actingAs($student)
            ->get(route('my-courses.index'))
            ->assertRedirect(route('dashboard'));
    }
}
