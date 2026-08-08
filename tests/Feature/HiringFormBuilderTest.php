<?php

namespace Tests\Feature;

use App\Models\HiringForm;
use App\Models\HiringFormField;
use App\Models\TutorApplication;
use App\Models\User;
use App\Services\HiringFormService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\Support\BuildsFeatureSchema;
use Tests\TestCase;

class HiringFormBuilderTest extends TestCase
{
    use BuildsFeatureSchema;

    protected function setUp(): void
    {
        parent::setUp();
        $this->buildFeatureSchema();
    }

    public function test_ensure_default_form_seeds_fields(): void
    {
        $form = HiringFormService::ensureDefaultForm();

        $this->assertDatabaseHas('hiring_forms', ['id' => $form->id, 'is_published' => 1]);
        $this->assertGreaterThan(5, $form->fields()->count());
        $this->assertTrue($form->fields()->where('system_key', 'full_name')->where('is_required', true)->exists());
    }

    public function test_admin_can_add_custom_required_field(): void
    {
        $admin = User::factory()->create([
            'role' => 'super_admin',
            'is_active' => true,
            'password' => Hash::make('password'),
        ]);

        HiringFormService::ensureDefaultForm();

        $this->actingAs($admin)
            ->post(route('admin.hiring-form.fields.store'), [
                'type' => HiringFormField::TYPE_SHORT_TEXT,
                'label' => 'لغات التدريس',
                'is_required' => 1,
                'system_key' => '',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('hiring_form_fields', [
            'label' => 'لغات التدريس',
            'is_required' => 1,
            'type' => HiringFormField::TYPE_SHORT_TEXT,
        ]);
    }

    public function test_required_custom_field_blocks_submit(): void
    {
        Storage::fake('public');
        config(['filesystems.public_media_disk' => 'public']);

        $form = HiringFormService::ensureDefaultForm();
        $custom = HiringFormField::create([
            'hiring_form_id' => $form->id,
            'type' => HiringFormField::TYPE_SHORT_TEXT,
            'label' => 'سؤال إضافي إجباري',
            'is_required' => true,
            'sort_order' => 999,
            'is_active' => true,
        ]);

        $user = User::factory()->create([
            'role' => 'instructor',
            'email' => 'tutor.dyn@example.com',
            'phone' => '+966500000010',
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

        $payload = $this->buildValidAnswersPayload($form);
        unset($payload['answers'][$custom->id]);

        $this->actingAs($user)
            ->post(route('public.tutor.apply.profile.store'), $payload)
            ->assertSessionHasErrors('answers.'.$custom->id);
    }

    public function test_dynamic_form_submit_stores_answers_and_maps_system_keys(): void
    {
        Storage::fake('public');
        config(['filesystems.public_media_disk' => 'public']);

        $form = HiringFormService::ensureDefaultForm();

        $user = User::factory()->create([
            'role' => 'instructor',
            'email' => 'tutor.ok@example.com',
            'phone' => '+966500000011',
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

        $payload = $this->buildValidAnswersPayload($form, '+966500000011');

        $this->actingAs($user)
            ->post(route('public.tutor.apply.profile.store'), $payload)
            ->assertRedirect(route('public.tutor.apply.profile'));

        $application->refresh();
        $this->assertSame(TutorApplication::STATUS_PENDING, $application->status);
        $this->assertSame($form->id, $application->hiring_form_id);
        $this->assertSame('معلم ديناميكي', $application->full_name);
        $this->assertNotEmpty($application->answers);
        $this->assertNotNull($application->photo_path);
    }

    public function test_optional_field_can_be_skipped(): void
    {
        Storage::fake('public');
        config(['filesystems.public_media_disk' => 'public']);

        $form = HiringFormService::ensureDefaultForm();
        HiringFormField::create([
            'hiring_form_id' => $form->id,
            'type' => HiringFormField::TYPE_SHORT_TEXT,
            'label' => 'ملاحظة اختيارية',
            'is_required' => false,
            'sort_order' => 1000,
            'is_active' => true,
        ]);

        $user = User::factory()->create([
            'role' => 'instructor',
            'email' => 'tutor.opt@example.com',
            'phone' => '+966500000012',
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

        $this->actingAs($user)
            ->post(route('public.tutor.apply.profile.store'), $this->buildValidAnswersPayload($form, '+966500000012'))
            ->assertRedirect(route('public.tutor.apply.profile'));

        $this->assertSame(TutorApplication::STATUS_PENDING, TutorApplication::query()->where('user_id', $user->id)->value('status'));
    }

    public function test_admin_hiring_form_controller_returns_builder_data(): void
    {
        $admin = User::factory()->create([
            'role' => 'super_admin',
            'is_active' => true,
            'password' => Hash::make('password'),
        ]);

        $this->actingAs($admin);
        $response = app(\App\Http\Controllers\Admin\HiringFormController::class)->edit();

        $this->assertSame('admin.hiring-form.edit', $response->name());
        $this->assertNotNull($response->getData()['form'] ?? null);
        $this->assertNotEmpty($response->getData()['typeLabels'] ?? []);
        $this->assertArrayHasKey('short_text', $response->getData()['typeLabels']);
    }

    /**
     * @return array<string, mixed>
     */
    private function buildValidAnswersPayload(HiringForm $form, string $phone = '+966500000099'): array
    {
        $form->load('activeFields');
        $answers = [];
        $uploads = [];

        foreach ($form->activeFields as $field) {
            if ($field->isSection()) {
                continue;
            }

            if ($field->isFile()) {
                $kind = $field->settings['file_kind'] ?? 'any';
                $uploads[$field->id] = match ($kind) {
                    'video' => UploadedFile::fake()->create('intro.mp4', 200, 'video/mp4'),
                    default => UploadedFile::fake()->image('doc-'.$field->id.'.jpg'),
                };
                continue;
            }

            $answers[$field->id] = match ($field->system_key ?: $field->type) {
                'full_name' => 'معلم ديناميكي',
                'phone' => $phone,
                'headline' => 'معلم قرآن',
                'bio' => 'نبذة تعريفية كافية للاختبار الديناميكي',
                'experience' => "خبرة 5 سنوات\nتحفيظ",
                'education' => 'إجازة',
                'years_experience' => 5,
                'gender' => 'male',
                'intro_video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                HiringFormField::TYPE_URL => 'https://example.com',
                HiringFormField::TYPE_EMAIL => 'x@example.com',
                HiringFormField::TYPE_NUMBER => 3,
                HiringFormField::TYPE_CHECKBOX => array_slice(array_map(
                    fn ($o) => is_array($o) ? ($o['value'] ?? '') : $o,
                    $field->options ?? ['a']
                ), 0, 1),
                HiringFormField::TYPE_SELECT, HiringFormField::TYPE_RADIO => (function () use ($field) {
                    $opt = $field->options[0] ?? 'yes';

                    return is_array($opt) ? ($opt['value'] ?? 'yes') : $opt;
                })(),
                default => 'قيمة اختبار',
            };
        }

        // Ensure video requirement via URL even if video file field left empty for optional
        $urlField = $form->activeFields->firstWhere('system_key', 'intro_video_url');
        if ($urlField) {
            $answers[$urlField->id] = 'https://www.youtube.com/watch?v=dQw4w9WgXcQ';
        }

        return [
            'answers' => $answers,
            'hiring_upload' => $uploads,
        ];
    }
}
