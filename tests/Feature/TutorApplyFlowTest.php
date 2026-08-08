<?php

namespace Tests\Feature;

use App\Models\HiringFormField;
use App\Models\InstructorProfile;
use App\Models\TutorApplication;
use App\Models\User;
use App\Services\HiringFormService;
use App\Services\TutorApplicationActivationService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\Support\BuildsFeatureSchema;
use Tests\TestCase;

class TutorApplyFlowTest extends TestCase
{
    use BuildsFeatureSchema;

    protected function setUp(): void
    {
        parent::setUp();
        $this->buildFeatureSchema();
    }

    public function test_register_creates_account_and_redirects_to_profile(): void
    {
        $response = $this->post(route('public.tutor.apply.register'), [
            'full_name' => 'معلم تجريبي',
            'email' => 'tutor.test@example.com',
            'phone' => '+966500000099',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
        ]);

        $response->assertRedirect(route('public.tutor.apply.profile'));
        $this->assertAuthenticated();

        $user = User::query()->where('email', 'tutor.test@example.com')->first();
        $this->assertNotNull($user);
        $this->assertSame('instructor', $user->role);
        $this->assertSame('+966500000099', $user->phone);
        $this->assertTrue(Hash::check('Password123', $user->password));

        $app = TutorApplication::query()->where('user_id', $user->id)->first();
        $this->assertNotNull($app);
        $this->assertSame(TutorApplication::STATUS_DRAFT, $app->status);

        $this->assertDatabaseHas('instructor_profiles', [
            'user_id' => $user->id,
            'status' => InstructorProfile::STATUS_DRAFT,
        ]);
    }

    public function test_profile_submit_and_admin_activate_publishes_profile(): void
    {
        Storage::fake('public');
        config(['filesystems.public_media_disk' => 'public']);

        $form = HiringFormService::ensureDefaultForm();

        $user = User::factory()->create([
            'role' => 'instructor',
            'email' => 'teacher2@example.com',
            'phone' => '+966500000001',
            'is_active' => true,
            'password' => Hash::make('password'),
        ]);

        $application = TutorApplication::create([
            'user_id' => $user->id,
            'full_name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'status' => TutorApplication::STATUS_DRAFT,
        ]);

        InstructorProfile::firstOrCreate(
            ['user_id' => $user->id],
            ['status' => InstructorProfile::STATUS_DRAFT]
        );

        $this->actingAs($user);

        $answers = [];
        $uploads = [];
        foreach ($form->activeFields as $field) {
            if ($field->isSection()) {
                continue;
            }
            if ($field->isFile()) {
                $kind = $field->settings['file_kind'] ?? 'any';
                if ($kind === 'video') {
                    continue;
                }
                $uploads[$field->id] = UploadedFile::fake()->image('f'.$field->id.'.jpg');
                continue;
            }
            $answers[$field->id] = match ($field->system_key) {
                'full_name' => 'معلم مكتمل',
                'phone' => '+966500000001',
                'headline' => 'معلم قرآن',
                'bio' => 'نبذة تعريفية كافية للاختبار',
                'experience' => "خبرة 5 سنوات\nتحفيظ",
                'education' => 'إجازة',
                'years_experience' => 5,
                'gender' => 'male',
                'intro_video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                default => 'اختبار',
            };
        }

        $response = $this->post(route('public.tutor.apply.profile.store'), [
            'answers' => $answers,
            'hiring_upload' => $uploads,
        ]);

        $response->assertRedirect(route('public.tutor.apply.profile'));
        $application->refresh();
        $this->assertSame(TutorApplication::STATUS_PENDING, $application->status);
        $this->assertNotNull($application->photo_path);
        $this->assertNotEmpty($application->answers);

        $admin = User::factory()->create([
            'role' => 'super_admin',
            'is_active' => true,
            'password' => Hash::make('password'),
        ]);

        $application->update([
            'status' => TutorApplication::STATUS_APPROVED,
            'reviewed_by' => $admin->id,
            'reviewed_at' => now(),
        ]);

        $result = TutorApplicationActivationService::activate($application->fresh(), $admin);
        $this->assertSame($user->id, $result['user']->id);
        $this->assertNull($result['password']);
        $this->assertSame(InstructorProfile::STATUS_APPROVED, $result['profile']->status);
        $this->assertSame(TutorApplication::STATUS_ACTIVATED, $application->fresh()->status);
    }

    public function test_apply_pages_render(): void
    {
        HiringFormService::ensureDefaultForm();

        $this->get(route('public.tutor.apply'))->assertOk();

        $user = User::factory()->create([
            'role' => 'instructor',
            'is_active' => true,
            'password' => Hash::make('password'),
        ]);
        TutorApplication::create([
            'user_id' => $user->id,
            'full_name' => $user->name,
            'email' => $user->email,
            'status' => TutorApplication::STATUS_DRAFT,
        ]);

        $this->actingAs($user)
            ->get(route('public.tutor.apply.profile'))
            ->assertOk()
            ->assertSee('إرسال للمراجعة', false);
    }
}
