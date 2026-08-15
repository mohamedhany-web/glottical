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

    public function test_applicant_cannot_open_instructor_dashboard_until_activated(): void
    {
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

        $this->assertFalse($user->fresh()->canAccessInstructorPanel());

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertRedirect(route('public.tutor.apply.profile'));

        $this->actingAs($user)
            ->get(route('instructor.calendar'))
            ->assertRedirect(route('public.tutor.apply.profile'));

        TutorApplication::query()->where('user_id', $user->id)->update([
            'status' => TutorApplication::STATUS_PENDING,
        ]);
        $this->assertFalse($user->fresh()->canAccessInstructorPanel());

        TutorApplication::query()->where('user_id', $user->id)->update([
            'status' => TutorApplication::STATUS_APPROVED,
        ]);
        $this->assertFalse($user->fresh()->canAccessInstructorPanel());

        TutorApplication::query()->where('user_id', $user->id)->update([
            'status' => TutorApplication::STATUS_ACTIVATED,
        ]);
        $this->assertTrue($user->fresh()->canAccessInstructorPanel());

        $this->actingAs($user)
            ->get(route('public.tutor.apply.profile'))
            ->assertRedirect(route('dashboard'));
    }

    public function test_pending_and_approved_show_wait_page_without_dashboard_links(): void
    {
        $user = User::factory()->create([
            'role' => 'instructor',
            'is_active' => true,
            'password' => Hash::make('password'),
        ]);
        $application = TutorApplication::create([
            'user_id' => $user->id,
            'full_name' => $user->name,
            'email' => $user->email,
            'status' => TutorApplication::STATUS_PENDING,
        ]);

        $pending = $this->actingAs($user)->get(route('public.tutor.apply.profile'));
        $pending->assertOk()
            ->assertSee('طلبك قيد المراجعة', false)
            ->assertDontSee('الذهاب إلى لوحة المعلم', false)
            ->assertDontSee(route('instructor.personal-branding.edit'), false);

        $application->update(['status' => TutorApplication::STATUS_APPROVED]);

        $this->actingAs($user)
            ->get(route('public.tutor.apply.profile'))
            ->assertOk()
            ->assertSee('بانتظار التفعيل', false)
            ->assertDontSee('الذهاب إلى لوحة المعلم', false);
    }

    public function test_unactivated_instructor_login_goes_to_profile_not_dashboard(): void
    {
        $user = User::factory()->create([
            'role' => 'instructor',
            'email' => 'waiting.tutor@example.com',
            'is_active' => true,
            'password' => Hash::make('Password123'),
        ]);
        TutorApplication::create([
            'user_id' => $user->id,
            'full_name' => $user->name,
            'email' => $user->email,
            'status' => TutorApplication::STATUS_PENDING,
        ]);

        $this->post('/login', [
            'email' => 'waiting.tutor@example.com',
            'password' => 'Password123',
        ])->assertRedirect(route('public.tutor.apply.profile'));

        $this->assertAuthenticatedAs($user);

        $this->actingAs($user)
            ->get(route('instructor.personal-branding.edit'))
            ->assertRedirect(route('public.tutor.apply.profile'));
    }

    public function test_admin_created_instructor_without_application_keeps_dashboard(): void
    {
        $user = User::factory()->create([
            'role' => 'instructor',
            'is_active' => true,
            'password' => Hash::make('password'),
        ]);

        $this->assertTrue($user->fresh()->canAccessInstructorPanel());
    }

    public function test_admin_application_photo_is_served_through_authenticated_file_route(): void
    {
        Storage::fake('public');
        config(['filesystems.public_media_disk' => 'public']);

        $admin = User::factory()->create([
            'role' => 'super_admin',
            'is_active' => true,
            'password' => Hash::make('password'),
        ]);

        $photo = UploadedFile::fake()->image('toqa.jpg', 200, 200);
        $photoPath = \App\Services\TutorApplicationStorage::storePhoto($photo);

        $application = TutorApplication::create([
            'full_name' => 'Toqa Omar',
            'email' => 'toqa@example.com',
            'city' => 'October gardens - Giza',
            'headline' => 'معلمة',
            'photo_path' => $photoPath,
            'status' => TutorApplication::STATUS_PENDING,
        ]);

        $fileUrl = route('admin.tutor-applications.file', [$application, 'photo']);

        $this->actingAs($admin)
            ->get(route('admin.tutor-applications.show', $application))
            ->assertOk()
            ->assertSee($fileUrl, false)
            ->assertDontSee('r2.dev', false);

        $this->actingAs($admin)
            ->get($fileUrl)
            ->assertOk()
            ->assertHeader('content-type', 'image/jpeg')
            ->assertStreamedContent(Storage::disk('public')->get($photoPath));

        $this->post('/logout');

        $this->get($fileUrl)->assertRedirect();
    }

    public function test_media_proxy_streams_application_photo_from_cloud_disk_without_redirect(): void
    {
        Storage::fake('r2');
        config([
            'filesystems.public_media_disk' => 'r2',
            'filesystems.r2_public_url' => 'https://pub-example.r2.dev',
        ]);

        $path = 'tutor-applications/photos/toqa.jpg';
        Storage::disk('r2')->put($path, 'fake-jpeg-bytes');

        $this->get('/media/'.$path)
            ->assertOk()
            ->assertStreamedContent('fake-jpeg-bytes');
    }
}
