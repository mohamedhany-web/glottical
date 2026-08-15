<?php

namespace Tests\Feature;

use App\Models\AcademicSubject;
use App\Models\AcademicYear;
use App\Models\AdvancedCourse;
use App\Models\CourseSection;
use App\Models\CurriculumItem;
use App\Models\Lecture;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\Support\BuildsFeatureSchema;
use Tests\TestCase;

class StudentLibraryCurriculumTest extends TestCase
{
    use BuildsFeatureSchema;

    protected function setUp(): void
    {
        parent::setUp();
        $this->buildFeatureSchema();
        $this->buildExtra();
    }

    protected function buildExtra(): void
    {
        Schema::create('academic_years', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedInteger('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('academic_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->nullable();
            $table->string('name');
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
            $table->string('status')->default('published');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('student_course_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->foreignId('advanced_course_id');
            $table->string('status')->default('active');
            $table->unsignedInteger('progress')->default(0);
            $table->timestamp('enrolled_at')->nullable();
            $table->timestamp('activated_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->string('access_type')->nullable();
            $table->string('enrollment_type')->nullable();
            $table->timestamps();
        });

        Schema::create('course_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('advanced_course_id');
            $table->foreignId('parent_id')->nullable();
            $table->string('title');
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
            $table->string('title')->nullable();
            $table->foreignId('course_id')->nullable();
            $table->foreignId('instructor_id')->nullable();
            $table->timestamps();
        });

        if (! Schema::hasTable('student_service_entitlements')) {
            Schema::create('student_service_entitlements', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id');
                $table->boolean('includes_libraries')->default(false);
                $table->string('status')->default('active');
                $table->timestamp('starts_at')->nullable();
                $table->timestamp('expires_at')->nullable();
                $table->timestamps();
            });
        }
    }

    public function test_curriculum_route_registered(): void
    {
        $this->assertTrue(\Illuminate\Support\Facades\Route::has('student.library.curriculum'));
    }

    public function test_student_sees_only_enrolled_course_curriculum(): void
    {
        $student = User::factory()->create([
            'role' => 'student',
            'is_active' => true,
            'password' => Hash::make('password'),
        ]);
        $other = User::factory()->create([
            'role' => 'student',
            'is_active' => true,
            'password' => Hash::make('password'),
        ]);
        $teacher = User::factory()->create([
            'role' => 'instructor',
            'is_active' => true,
        ]);

        $year = AcademicYear::create(['name' => 'سنة 1', 'order' => 1, 'is_active' => true]);
        $subject = AcademicSubject::create([
            'academic_year_id' => $year->id,
            'name' => 'عربي',
            'order' => 1,
            'is_active' => true,
        ]);

        $mine = AdvancedCourse::query()->create([
            'title' => 'منهجي الظاهر',
            'academic_year_id' => $year->id,
            'academic_subject_id' => $subject->id,
            'instructor_id' => $teacher->id,
            'status' => 'published',
            'is_active' => true,
        ]);
        $otherCourse = AdvancedCourse::query()->create([
            'title' => 'منهج طالب آخر',
            'academic_year_id' => $year->id,
            'academic_subject_id' => $subject->id,
            'instructor_id' => $teacher->id,
            'status' => 'published',
            'is_active' => true,
        ]);

        DB::table('student_course_enrollments')->insert([
            'user_id' => $student->id,
            'advanced_course_id' => $mine->id,
            'status' => 'active',
            'activated_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('student_course_enrollments')->insert([
            'user_id' => $other->id,
            'advanced_course_id' => $otherCourse->id,
            'status' => 'active',
            'activated_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $section = CourseSection::query()->create([
            'advanced_course_id' => $mine->id,
            'title' => 'وحدة 1',
            'order' => 1,
            'is_active' => true,
        ]);
        $lectureId = DB::table('lectures')->insertGetId([
            'title' => 'درس 1',
            'course_id' => $mine->id,
            'instructor_id' => $teacher->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        CurriculumItem::query()->create([
            'course_section_id' => $section->id,
            'item_type' => Lecture::class,
            'item_id' => $lectureId,
            'order' => 1,
            'is_active' => true,
        ]);

        $this->actingAs($student)
            ->get(route('student.library.curriculum'))
            ->assertOk()
            ->assertSee('منهجي الظاهر', false)
            ->assertDontSee('منهج طالب آخر', false)
            ->assertSee('المناهج', false);

        $this->actingAs($student)
            ->get(route('student.library.home'))
            ->assertOk()
            ->assertSee(route('student.library.curriculum', [], false), false);
    }

    public function test_guest_cannot_open_curriculum_library(): void
    {
        $this->get(route('student.library.curriculum'))->assertRedirect();
    }
}
