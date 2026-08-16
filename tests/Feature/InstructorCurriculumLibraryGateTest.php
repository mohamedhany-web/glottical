<?php

namespace Tests\Feature;

use App\Models\CurriculumLibraryCategory;
use App\Models\CurriculumLibraryItem;
use App\Models\CurriculumLibraryMaterial;
use App\Models\CurriculumLibrarySection;
use App\Models\InstructorProfile;
use App\Models\TutorApplication;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\Support\BuildsFeatureSchema;
use Tests\TestCase;

class InstructorCurriculumLibraryGateTest extends TestCase
{
    use BuildsFeatureSchema;

    protected function setUp(): void
    {
        parent::setUp();
        $this->buildFeatureSchema();
        $this->buildCurriculumLibrarySchema();
    }

    protected function buildCurriculumLibrarySchema(): void
    {
        Schema::create('academic_years', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->nullable();
            $table->string('slug')->nullable();
            $table->unsignedInteger('order')->default(0);
            $table->unsignedInteger('level_number')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('academic_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->nullable();
            $table->string('name');
            $table->string('code')->nullable();
            $table->string('slug')->nullable();
            $table->unsignedInteger('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('advanced_courses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->foreignId('academic_year_id')->nullable();
            $table->foreignId('academic_subject_id')->nullable();
            $table->foreignId('instructor_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('course_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('advanced_course_id');
            $table->foreignId('parent_id')->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedInteger('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('curriculum_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_section_id');
            $table->string('item_type');
            $table->unsignedBigInteger('item_id');
            $table->unsignedInteger('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('lectures', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->foreignId('course_id')->nullable();
            $table->foreignId('advanced_course_id')->nullable();
            $table->foreignId('instructor_id')->nullable();
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamps();
        });

        Schema::create('academic_year_instructors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id');
            $table->foreignId('instructor_id');
            $table->text('assigned_courses')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('curriculum_library_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->nullable();
            $table->text('description')->nullable();
            $table->unsignedInteger('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_restricted')->default(false);
            $table->timestamps();
        });

        Schema::create('curriculum_library_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->longText('content')->nullable();
            $table->string('grade_level')->nullable();
            $table->string('subject')->nullable();
            $table->string('language', 8)->nullable();
            $table->string('item_type')->nullable();
            $table->json('meta')->nullable();
            $table->unsignedInteger('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_free_preview')->default(false);
            $table->timestamps();
        });

        Schema::create('curriculum_library_category_user', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();
        });

        Schema::create('curriculum_library_item_files', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('curriculum_library_item_id');
            $table->string('file_type')->nullable();
            $table->string('label')->nullable();
            $table->string('path')->nullable();
            $table->string('storage_disk')->nullable();
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();
        });

        Schema::create('curriculum_library_sections', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('curriculum_library_item_id');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->unsignedInteger('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('curriculum_library_materials', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('curriculum_library_section_id')->nullable();
            $table->string('title')->nullable();
            $table->string('path')->nullable();
            $table->string('storage_disk', 32)->nullable();
            $table->string('original_name')->nullable();
            $table->string('file_kind', 20)->default('other');
            $table->boolean('view_in_platform')->default(true);
            $table->boolean('allow_download')->default(false);
            $table->unsignedInteger('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    private function makeActivatedInstructor(): User
    {
        $teacher = User::factory()->create([
            'role' => 'instructor',
            'is_active' => true,
            'password' => Hash::make('password'),
        ]);
        InstructorProfile::create([
            'user_id' => $teacher->id,
            'status' => InstructorProfile::STATUS_APPROVED,
        ]);
        TutorApplication::create([
            'user_id' => $teacher->id,
            'full_name' => $teacher->name,
            'email' => $teacher->email,
            'status' => TutorApplication::STATUS_ACTIVATED,
            'activated_at' => now(),
        ]);

        return $teacher->fresh();
    }

    public function test_new_registrant_cannot_open_curriculum_library(): void
    {
        $newbie = User::factory()->create([
            'role' => 'instructor',
            'is_active' => true,
            'password' => Hash::make('password'),
        ]);
        InstructorProfile::create([
            'user_id' => $newbie->id,
            'status' => InstructorProfile::STATUS_DRAFT,
        ]);
        TutorApplication::create([
            'user_id' => $newbie->id,
            'full_name' => $newbie->name,
            'email' => $newbie->email,
            'status' => TutorApplication::STATUS_DRAFT,
        ]);

        $this->assertFalse($newbie->fresh()->isAcademyWorkingInstructor());

        $this->actingAs($newbie)
            ->get(route('instructor.libraries.curriculum.index'))
            ->assertRedirect(route('public.tutor.apply.profile'));
    }

    public function test_activated_instructor_can_open_curriculum_library(): void
    {
        $teacher = $this->makeActivatedInstructor();
        $item = CurriculumLibraryItem::query()->create([
            'title' => 'منهج تفاعلي للمعلم',
            'slug' => 'manahij-teacher-'.uniqid(),
            'is_active' => true,
        ]);

        $this->assertTrue($teacher->isAcademyWorkingInstructor());
        $this->assertTrue($teacher->hasCurriculumLibraryAccess());

        $this->actingAs($teacher)
            ->get(route('instructor.libraries.curriculum.index'))
            ->assertOk()
            ->assertSee('منهج تفاعلي للمعلم', false);

        $this->actingAs($teacher)
            ->get(route('instructor.libraries.curriculum.show', $item))
            ->assertOk()
            ->assertSee('منهج تفاعلي للمعلم', false);
    }

    public function test_activated_instructor_sees_restricted_manahij_without_allowlist(): void
    {
        $teacher = $this->makeActivatedInstructor();
        $category = CurriculumLibraryCategory::query()->create([
            'name' => 'قسم خاص',
            'slug' => 'restricted-'.uniqid(),
            'is_active' => true,
            'is_restricted' => true,
        ]);
        $item = CurriculumLibraryItem::query()->create([
            'category_id' => $category->id,
            'title' => 'منهج مقيّد للمعلمين',
            'slug' => 'restricted-item-'.uniqid(),
            'is_active' => true,
        ]);

        $this->assertFalse($item->isAccessibleByStudent($teacher));
        $this->assertTrue($item->isAccessibleByViewer($teacher));

        $this->actingAs($teacher)
            ->get(route('instructor.libraries.curriculum.index'))
            ->assertOk()
            ->assertSee('منهج مقيّد للمعلمين', false);
    }

    public function test_activated_instructor_can_open_manahij_files_without_package(): void
    {
        $teacher = $this->makeActivatedInstructor();
        $item = CurriculumLibraryItem::query()->create([
            'title' => 'منهج شرائح',
            'slug' => 'manahij-slides-'.uniqid(),
            'is_active' => true,
        ]);
        $section = CurriculumLibrarySection::query()->create([
            'curriculum_library_item_id' => $item->id,
            'title' => 'قسم',
            'is_active' => true,
        ]);
        $material = CurriculumLibraryMaterial::query()->create([
            'curriculum_library_section_id' => $section->id,
            'title' => 'عرض HTML',
            'path' => 'missing.html',
            'storage_disk' => 'public',
            'file_kind' => 'html',
            'view_in_platform' => true,
            'is_active' => true,
        ]);

        $this->actingAs($teacher)
            ->get(route('curriculum-library.material.html', [$item, $material]))
            ->assertStatus(404);
    }

    public function test_student_cannot_open_instructor_manahij_index(): void
    {
        $student = User::factory()->create([
            'role' => 'student',
            'is_active' => true,
            'password' => Hash::make('password'),
        ]);

        $this->actingAs($student)
            ->get(route('instructor.libraries.curriculum.index'))
            ->assertRedirect();
    }

    public function test_new_registrant_cannot_stream_manahij_files(): void
    {
        $newbie = User::factory()->create([
            'role' => 'instructor',
            'is_active' => true,
            'password' => Hash::make('password'),
        ]);
        InstructorProfile::create([
            'user_id' => $newbie->id,
            'status' => InstructorProfile::STATUS_DRAFT,
        ]);
        TutorApplication::create([
            'user_id' => $newbie->id,
            'full_name' => $newbie->name,
            'email' => $newbie->email,
            'status' => TutorApplication::STATUS_DRAFT,
        ]);

        $item = CurriculumLibraryItem::query()->create([
            'title' => 'منهج مغلق',
            'slug' => 'manahij-closed-'.uniqid(),
            'is_active' => true,
        ]);
        $section = CurriculumLibrarySection::query()->create([
            'curriculum_library_item_id' => $item->id,
            'title' => 'قسم',
            'is_active' => true,
        ]);
        $material = CurriculumLibraryMaterial::query()->create([
            'curriculum_library_section_id' => $section->id,
            'title' => 'عرض',
            'path' => 'missing.html',
            'file_kind' => 'html',
            'view_in_platform' => true,
            'is_active' => true,
        ]);

        $this->actingAs($newbie)
            ->get(route('curriculum-library.material.html', [$item, $material]))
            ->assertForbidden();
    }
}
