<?php

namespace Tests\Feature;

use App\Models\HiringFormField;
use App\Models\InstructorProfile;
use App\Models\TutorApplication;
use App\Models\User;
use App\Services\HiringFormService;
use App\Services\TutorApplicationStorage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\Support\BuildsFeatureSchema;
use Tests\TestCase;

class HiringFormSystemKeyMappingTest extends TestCase
{
    use BuildsFeatureSchema;

    protected function setUp(): void
    {
        parent::setUp();
        $this->buildFeatureSchema();
        Storage::fake('public');
        config(['filesystems.public_media_disk' => 'public']);
    }

    public function test_default_form_covers_every_system_key(): void
    {
        $form = HiringFormService::ensureDefaultForm();
        $keys = array_keys(HiringFormField::systemKeys());
        $present = $form->fields()->whereNotNull('system_key')->pluck('system_key')->all();

        foreach ($keys as $key) {
            $this->assertContains($key, $present, "Missing system_key field: {$key}");
        }
    }

    public function test_all_text_and_file_system_keys_map_and_upload_correctly(): void
    {
        $form = HiringFormService::ensureDefaultForm()->load('activeFields');

        $user = User::factory()->create([
            'role' => 'instructor',
            'email' => 'map.all@example.com',
            'phone' => '+966511111111',
            'is_active' => true,
            'password' => Hash::make('password'),
            'name' => 'Old Name',
        ]);

        InstructorProfile::firstOrCreate(
            ['user_id' => $user->id],
            ['status' => InstructorProfile::STATUS_DRAFT]
        );

        $application = TutorApplication::create([
            'user_id' => $user->id,
            'full_name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'status' => TutorApplication::STATUS_DRAFT,
        ]);

        $answers = [];
        $uploads = [];
        $byKey = [];

        foreach ($form->activeFields as $field) {
            if ($field->system_key) {
                $byKey[$field->system_key] = $field;
            }
        }

        foreach (HiringFormField::systemKeys() as $key => $_label) {
            $this->assertArrayHasKey($key, $byKey, "No field for {$key}");
            $field = $byKey[$key];

            if ($field->isFile()) {
                $uploads[$field->id] = match ($key) {
                    'photo' => UploadedFile::fake()->image('avatar.jpg'),
                    'id_document' => UploadedFile::fake()->create('id.pdf', 120, 'application/pdf'),
                    'certificate' => UploadedFile::fake()->create('cert.pdf', 140, 'application/pdf'),
                    'intro_video' => UploadedFile::fake()->create('intro.mp4', 300, 'video/mp4'),
                    default => UploadedFile::fake()->image('file.jpg'),
                };
                continue;
            }

            $answers[$field->id] = match ($key) {
                'full_name' => 'اسم مربوط كامل',
                'phone' => '+966522222222',
                'nationality' => 'سعودي',
                'city' => 'الرياض',
                'gender' => 'male',
                'headline' => 'عنوان مربوط',
                'bio' => 'نبذة مربوطة بالنظام للاختبار الشامل',
                'experience' => "خبرة مربوطة\nسطرين",
                'education' => 'بكالوريوس',
                'years_experience' => 7,
                'intro_video_url' => 'https://www.youtube.com/watch?v=mappedKeyTest',
                default => 'قيمة-'.$key,
            };
        }

        // Keep require_intro_video satisfied even if video file mapping fails in fake env:
        // we upload both file + URL.
        $this->actingAs($user)
            ->post(route('public.tutor.apply.profile.store'), [
                'answers' => $answers,
                'hiring_upload' => $uploads,
            ])
            ->assertRedirect(route('public.tutor.apply.profile'))
            ->assertSessionHasNoErrors();

        $application->refresh();
        $user->refresh();
        $profile = InstructorProfile::query()->where('user_id', $user->id)->first();

        $this->assertSame(TutorApplication::STATUS_PENDING, $application->status);
        $this->assertSame($form->id, $application->hiring_form_id);

        // Text / scalar system keys
        $this->assertSame('اسم مربوط كامل', $application->full_name);
        $this->assertSame('+966522222222', $application->phone);
        $this->assertSame('سعودي', $application->nationality);
        $this->assertSame('الرياض', $application->city);
        $this->assertSame('male', $application->gender);
        $this->assertSame('عنوان مربوط', $application->headline);
        $this->assertSame('نبذة مربوطة بالنظام للاختبار الشامل', $application->bio);
        $this->assertSame("خبرة مربوطة\nسطرين", $application->experience);
        $this->assertSame('بكالوريوس', $application->education);
        $this->assertSame(7, $application->years_experience);
        $this->assertSame('https://www.youtube.com/watch?v=mappedKeyTest', $application->intro_video_url);

        // File system keys stored on application columns
        $this->assertNotEmpty($application->photo_path);
        $this->assertNotEmpty($application->id_document_path);
        $this->assertNotEmpty($application->certificate_path);
        $this->assertNotEmpty($application->intro_video_path);

        $this->assertStringContainsString(TutorApplicationStorage::DIR_PHOTOS, $application->photo_path);
        $this->assertStringContainsString(TutorApplicationStorage::DIR_IDS, $application->id_document_path);
        $this->assertStringContainsString(TutorApplicationStorage::DIR_CERTIFICATES, $application->certificate_path);
        $this->assertStringContainsString(TutorApplicationStorage::DIR_VIDEOS, $application->intro_video_path);

        Storage::disk('public')->assertExists($application->photo_path);
        Storage::disk('public')->assertExists($application->id_document_path);
        Storage::disk('public')->assertExists($application->certificate_path);
        Storage::disk('public')->assertExists($application->intro_video_path);

        // Answers JSON includes every system key field
        $answerKeys = collect($application->answers)->pluck('system_key')->filter()->values()->all();
        foreach (array_keys(HiringFormField::systemKeys()) as $key) {
            $this->assertContains($key, $answerKeys, "answers missing system_key {$key}");
        }

        // User + instructor profile sync
        $this->assertSame('اسم مربوط كامل', $user->name);
        $this->assertSame('+966522222222', $user->phone);
        $this->assertSame('male', $user->gender);
        $this->assertSame('نبذة مربوطة بالنظام للاختبار الشامل', $user->bio);
        $this->assertSame($application->photo_path, $user->profile_image);
        $this->assertNotNull($profile);
        $this->assertSame('عنوان مربوط', $profile->headline);
        $this->assertSame(InstructorProfile::STATUS_PENDING_REVIEW, $profile->status);
    }

    public function test_intro_video_file_alone_satisfies_video_requirement(): void
    {
        $form = HiringFormService::ensureDefaultForm()->load('activeFields');
        $form->update(['settings' => ['require_intro_video' => true]]);

        $user = User::factory()->create([
            'role' => 'instructor',
            'email' => 'video.only@example.com',
            'phone' => '+966533333333',
            'is_active' => true,
            'password' => Hash::make('password'),
        ]);

        TutorApplication::create([
            'user_id' => $user->id,
            'full_name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'status' => TutorApplication::STATUS_DRAFT,
        ]);

        $answers = [];
        $uploads = [];
        foreach ($form->fresh()->activeFields as $field) {
            if ($field->isSection()) {
                continue;
            }
            if ($field->isFile()) {
                $uploads[$field->id] = match ($field->system_key) {
                    'intro_video' => UploadedFile::fake()->create('solo.mp4', 220, 'video/mp4'),
                    'id_document', 'certificate' => UploadedFile::fake()->create($field->system_key.'.pdf', 80, 'application/pdf'),
                    default => UploadedFile::fake()->image($field->system_key.'.jpg'),
                };
                continue;
            }
            if ($field->system_key === 'intro_video_url') {
                // intentionally empty — video file should be enough
                continue;
            }
            $answers[$field->id] = match ($field->system_key) {
                'full_name' => 'معلم فيديو فقط',
                'phone' => '+966533333333',
                'headline' => 'عنوان',
                'bio' => 'نبذة كافية للاختبار',
                'experience' => 'خبرة',
                'gender' => 'female',
                'years_experience' => 2,
                default => 'x',
            };
        }

        $this->actingAs($user)
            ->post(route('public.tutor.apply.profile.store'), [
                'answers' => $answers,
                'hiring_upload' => $uploads,
            ])
            ->assertRedirect(route('public.tutor.apply.profile'))
            ->assertSessionHasNoErrors();

        $app = TutorApplication::query()->where('user_id', $user->id)->first();
        $this->assertNotEmpty($app->intro_video_path);
        $this->assertNull($app->intro_video_url);
        Storage::disk('public')->assertExists($app->intro_video_path);
    }

    public function test_duplicate_mapped_phone_is_rejected(): void
    {
        HiringFormService::ensureDefaultForm();

        User::factory()->create([
            'role' => 'student',
            'email' => 'other@example.com',
            'phone' => '+966544444444',
            'is_active' => true,
            'password' => Hash::make('password'),
        ]);

        $user = User::factory()->create([
            'role' => 'instructor',
            'email' => 'dup.phone@example.com',
            'phone' => '+966555555555',
            'is_active' => true,
            'password' => Hash::make('password'),
        ]);

        TutorApplication::create([
            'user_id' => $user->id,
            'full_name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'status' => TutorApplication::STATUS_DRAFT,
        ]);

        $form = HiringFormService::publishedForm();
        $answers = [];
        $uploads = [];
        foreach ($form->activeFields as $field) {
            if ($field->isSection()) {
                continue;
            }
            if ($field->isFile()) {
                if ($field->system_key === 'intro_video') {
                    continue;
                }
                $uploads[$field->id] = $field->system_key === 'photo'
                    ? UploadedFile::fake()->image('p.jpg')
                    : UploadedFile::fake()->create($field->id.'.pdf', 50, 'application/pdf');
                continue;
            }
            $answers[$field->id] = match ($field->system_key) {
                'full_name' => 'معلم',
                'phone' => '+966544444444', // taken
                'headline' => 'عنوان',
                'bio' => 'نبذة كافية للاختبار',
                'experience' => 'خبرة',
                'education' => 'إجازة',
                'nationality' => 'مصري',
                'city' => 'جدة',
                'gender' => 'male',
                'years_experience' => 3,
                'intro_video_url' => 'https://www.youtube.com/watch?v=abc',
                default => 'y',
            };
        }

        $this->actingAs($user)
            ->post(route('public.tutor.apply.profile.store'), [
                'answers' => $answers,
                'hiring_upload' => $uploads,
            ])
            ->assertSessionHasErrors('phone');
    }
}
