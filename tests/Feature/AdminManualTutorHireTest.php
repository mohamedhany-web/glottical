<?php

namespace Tests\Feature;

use App\Mail\ManualInstructorHiredMail;
use App\Models\InstructorProfile;
use App\Models\TutorApplication;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\Support\BuildsFeatureSchema;
use Tests\TestCase;

class AdminManualTutorHireTest extends TestCase
{
    use BuildsFeatureSchema;

    protected function setUp(): void
    {
        parent::setUp();
        $this->buildFeatureSchema();
        Mail::fake();
    }

    public function test_activated_page_shows_manual_hire_button(): void
    {
        $admin = $this->makeAdmin();

        $this->actingAs($admin)
            ->get(route('admin.tutor-applications.activated'))
            ->assertOk()
            ->assertSee('توظيف يدوي بالإيميل', false)
            ->assertSee(route('admin.tutor-applications.hire-manually', [], false), false);
    }

    public function test_admin_hires_teacher_by_email_and_sends_login_mail(): void
    {
        $admin = $this->makeAdmin();

        $this->actingAs($admin)
            ->post(route('admin.tutor-applications.hire-manually'), [
                'email' => 'hired.teacher@example.com',
                'full_name' => 'معلم يدوي',
                'phone' => '+201000000099',
            ])
            ->assertRedirect(route('admin.tutor-applications.activated'))
            ->assertSessionHas('hired_email', 'hired.teacher@example.com')
            ->assertSessionHas('hired_password');

        $user = User::query()->where('email', 'hired.teacher@example.com')->first();
        $this->assertNotNull($user);
        $this->assertSame('instructor', $user->role);
        $this->assertTrue($user->is_active);
        $this->assertTrue($user->canAccessInstructorPanel());

        $application = TutorApplication::query()->where('user_id', $user->id)->first();
        $this->assertNotNull($application);
        $this->assertTrue($application->isActivated());
        $this->assertSame($admin->id, $application->activated_by);

        $this->assertDatabaseHas('instructor_profiles', [
            'user_id' => $user->id,
            'status' => InstructorProfile::STATUS_APPROVED,
        ]);

        Mail::assertSent(ManualInstructorHiredMail::class, function (ManualInstructorHiredMail $mail) use ($user) {
            return $mail->instructor->is($user)
                && is_string($mail->temporaryPassword)
                && $mail->temporaryPassword !== '';
        });
    }

    public function test_manual_hire_rejects_student_email(): void
    {
        $admin = $this->makeAdmin();
        User::factory()->create([
            'role' => 'student',
            'email' => 'student.hire@example.com',
            'is_active' => true,
            'password' => Hash::make('password'),
        ]);

        $this->actingAs($admin)
            ->from(route('admin.tutor-applications.activated'))
            ->post(route('admin.tutor-applications.hire-manually'), [
                'email' => 'student.hire@example.com',
                'full_name' => 'طالب',
            ])
            ->assertRedirect(route('admin.tutor-applications.activated'))
            ->assertSessionHas('error');

        $this->assertSame(0, TutorApplication::query()->count());
    }

    public function test_manual_hire_rejects_already_activated_teacher(): void
    {
        $admin = $this->makeAdmin();
        $teacher = User::factory()->create([
            'role' => 'instructor',
            'email' => 'already@example.com',
            'is_active' => true,
            'password' => Hash::make('password'),
        ]);
        TutorApplication::create([
            'user_id' => $teacher->id,
            'full_name' => $teacher->name,
            'email' => $teacher->email,
            'status' => TutorApplication::STATUS_ACTIVATED,
            'activated_at' => now(),
            'activated_by' => $admin->id,
        ]);

        $this->actingAs($admin)
            ->from(route('admin.tutor-applications.activated'))
            ->post(route('admin.tutor-applications.hire-manually'), [
                'email' => 'already@example.com',
                'full_name' => $teacher->name,
            ])
            ->assertRedirect()
            ->assertSessionHas('error');
    }

    private function makeAdmin(): User
    {
        return User::factory()->create([
            'role' => 'super_admin',
            'is_active' => true,
            'password' => Hash::make('password'),
        ]);
    }
}
