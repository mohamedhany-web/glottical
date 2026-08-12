<?php

namespace Tests\Feature;

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
            ->assertForbidden();
    }

    public function test_activated_instructor_can_open_curriculum_library(): void
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

        $this->assertTrue($teacher->fresh()->isAcademyWorkingInstructor());

        $this->actingAs($teacher)
            ->get(route('instructor.libraries.curriculum.index'))
            ->assertOk();
    }
}
