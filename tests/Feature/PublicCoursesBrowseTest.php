<?php

namespace Tests\Feature;

use App\Models\AcademicSubject;
use App\Models\AcademicYear;
use App\Models\InstructorProfile;
use App\Models\TutoringGroup;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\Support\BuildsFeatureSchema;
use Tests\TestCase;

class PublicCoursesBrowseTest extends TestCase
{
    use BuildsFeatureSchema;

    protected function setUp(): void
    {
        parent::setUp();
        $this->buildFeatureSchema();
        $this->buildAcademicCatalogSchema();
    }

    public function test_approved_teacher_appears_without_one_to_one_course(): void
    {
        $this->makeTeacher(['name' => 'Ahmed Quran Teacher']);

        $this->get(route('public.courses'))
            ->assertOk()
            ->assertSee('Ahmed Quran Teacher', false)
            ->assertSee('معلم قرآن', false)
            ->assertSee(__('public.browse_tab_private'), false)
            ->assertSee(__('public.browse_tab_groups'), false)
            ->assertSee(__('public.browse_view_teacher'), false)
            ->assertDontSee('حصة خاصة 50 دقيقة', false)
            ->assertDontSee('4 حصص خاصة', false)
            ->assertDontSee('خطة 3 أشهر', false)
            ->assertDontSee('عرض المعلم والحجز', false);
    }

    public function test_draft_and_inactive_teachers_are_hidden(): void
    {
        $this->makeTeacher(['name' => 'Visible Approved']);
        $this->makeTeacher(['name' => 'Draft Teacher'], ['status' => InstructorProfile::STATUS_DRAFT]);
        $this->makeTeacher(['name' => 'Inactive Teacher', 'is_active' => false]);

        $this->get(route('public.courses'))
            ->assertOk()
            ->assertSee('Visible Approved', false)
            ->assertDontSee('Draft Teacher', false)
            ->assertDontSee('Inactive Teacher', false);
    }

    public function test_search_filters_teachers_by_name(): void
    {
        $this->makeTeacher(['name' => 'Sara Arabic']);
        $this->makeTeacher(['name' => 'Omar Tajweed']);

        $this->get(route('public.courses', ['q' => 'Sara']))
            ->assertOk()
            ->assertSee('Sara Arabic', false)
            ->assertDontSee('Omar Tajweed', false);
    }

    public function test_groups_tab_lists_active_tutoring_groups(): void
    {
        $teacher = $this->makeTeacher(['name' => 'Group Instructor']);
        TutoringGroup::query()->create([
            'type' => TutoringGroup::TYPE_COLLECTIVE,
            'title' => 'مجموعة تأسيس قرآن',
            'slug' => 'quran-foundations-public',
            'description' => 'تأسيس التلاوة',
            'instructor_id' => $teacher->id,
            'is_active' => true,
            'duration_minutes' => 45,
        ]);
        TutoringGroup::query()->create([
            'type' => TutoringGroup::TYPE_INDIVIDUAL,
            'title' => 'مجموعة مخفية',
            'slug' => 'hidden-group',
            'instructor_id' => $teacher->id,
            'is_active' => false,
        ]);

        $this->get(route('public.courses', ['tab' => 'groups']))
            ->assertOk()
            ->assertSee('مجموعة تأسيس قرآن', false)
            ->assertSee('Group Instructor', false)
            ->assertDontSee('مجموعة مخفية', false);
    }

    public function test_legacy_delivery_group_query_opens_groups_tab(): void
    {
        $teacher = $this->makeTeacher();
        TutoringGroup::query()->create([
            'type' => TutoringGroup::TYPE_COLLECTIVE,
            'title' => 'مجموعة من الرابط القديم',
            'slug' => 'legacy-group-link',
            'instructor_id' => $teacher->id,
            'is_active' => true,
        ]);

        $this->get(route('public.courses', ['delivery' => 'group']))
            ->assertOk()
            ->assertSee('مجموعة من الرابط القديم', false);
    }

    public function test_group_search_and_subject_filter(): void
    {
        $teacher = $this->makeTeacher();
        $year = AcademicYear::query()->create([
            'name' => 'Islamic Foundations 1',
            'slug' => 'if-1',
            'is_active' => true,
            'order' => 1,
        ]);
        $quran = AcademicSubject::query()->create([
            'name' => 'قرآن',
            'slug' => 'quran-public',
            'academic_year_id' => $year->id,
            'is_active' => true,
            'order' => 1,
        ]);
        $arabic = AcademicSubject::query()->create([
            'name' => 'عربية',
            'slug' => 'arabic-public',
            'academic_year_id' => $year->id,
            'is_active' => true,
            'order' => 2,
        ]);

        TutoringGroup::query()->create([
            'type' => TutoringGroup::TYPE_COLLECTIVE,
            'title' => 'حلقة القرآن',
            'slug' => 'quran-halaqa',
            'instructor_id' => $teacher->id,
            'academic_year_id' => $year->id,
            'academic_subject_id' => $quran->id,
            'is_active' => true,
        ]);
        TutoringGroup::query()->create([
            'type' => TutoringGroup::TYPE_COLLECTIVE,
            'title' => 'حلقة العربية',
            'slug' => 'arabic-halaqa',
            'instructor_id' => $teacher->id,
            'academic_year_id' => $year->id,
            'academic_subject_id' => $arabic->id,
            'is_active' => true,
        ]);

        $this->get(route('public.courses', ['tab' => 'groups', 'q' => 'القرآن']))
            ->assertOk()
            ->assertSee('حلقة القرآن', false)
            ->assertDontSee('حلقة العربية', false);

        $this->get(route('public.courses', ['tab' => 'groups', 'subject_id' => $arabic->id]))
            ->assertOk()
            ->assertSee('حلقة العربية', false)
            ->assertDontSee('حلقة القرآن', false);
    }

    /**
     * @param  array<string, mixed>  $userAttrs
     * @param  array<string, mixed>  $profileAttrs
     */
    private function makeTeacher(array $userAttrs = [], array $profileAttrs = []): User
    {
        $user = User::factory()->create(array_merge([
            'role' => 'instructor',
            'is_active' => true,
            'password' => Hash::make('password'),
        ], $userAttrs));

        InstructorProfile::query()->create(array_merge([
            'user_id' => $user->id,
            'status' => InstructorProfile::STATUS_APPROVED,
            'headline' => 'معلم قرآن',
            'bio' => 'تعليم القرآن للأطفال',
            'skills' => 'قرآن, تجويد',
            'reviewed_at' => now(),
        ], $profileAttrs));

        return $user->fresh();
    }

    private function buildAcademicCatalogSchema(): void
    {
        if (! Schema::hasTable('academic_years')) {
            Schema::create('academic_years', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('code')->nullable();
                $table->string('slug')->nullable();
                $table->unsignedInteger('order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('academic_subjects')) {
            Schema::create('academic_subjects', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('academic_year_id')->nullable();
                $table->string('name');
                $table->string('slug')->nullable();
                $table->unsignedInteger('order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }
    }
}
