<?php

namespace Tests\Feature;

use App\Models\InstructorProfile;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\Support\BuildsFeatureSchema;
use Tests\TestCase;

class TeacherPublicPagesPhase1Test extends TestCase
{
    use BuildsFeatureSchema;

    protected function setUp(): void
    {
        parent::setUp();
        $this->buildFeatureSchema();
    }

    public function test_groups_one_to_one_redirects_to_instructors(): void
    {
        $this->get(route('public.groups.one-to-one'))
            ->assertRedirect(route('public.instructors.index', ['focus' => 'private']));
    }

    public function test_teachers_alias_redirects_to_instructors(): void
    {
        $this->get('/teachers')->assertRedirect('/instructors');
    }

    public function test_student_learn_teacher_redirects_to_public_profile(): void
    {
        $student = User::factory()->create([
            'role' => 'student',
            'is_active' => true,
            'password' => Hash::make('password'),
        ]);
        $teacher = User::factory()->create([
            'role' => 'instructor',
            'is_active' => true,
            'password' => Hash::make('password'),
        ]);
        InstructorProfile::create([
            'user_id' => $teacher->id,
            'status' => InstructorProfile::STATUS_APPROVED,
            'headline' => 'Teacher',
            'bio' => 'Bio text',
        ]);

        $this->actingAs($student)
            ->get(route('student.learn.teacher', $teacher))
            ->assertRedirect(route('public.instructors.show', $teacher));
    }
}
