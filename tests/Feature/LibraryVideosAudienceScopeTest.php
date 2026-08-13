<?php

namespace Tests\Feature;

use App\Models\AdvancedCourse;
use App\Models\LibraryVideo;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\Support\BuildsFeatureSchema;
use Tests\TestCase;

class LibraryVideosAudienceScopeTest extends TestCase
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
        Schema::create('library_folders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('instructor_id')->nullable();
            $table->unsignedBigInteger('academic_year_id')->nullable();
            $table->string('kind')->default('videos');
            $table->string('name_ar');
            $table->string('name_en')->nullable();
            $table->string('slug')->unique();
            $table->boolean('is_active')->default(true);
            $table->boolean('requires_library_entitlement')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->string('icon')->nullable();
            $table->string('color')->nullable();
            $table->timestamps();
        });

        Schema::create('library_videos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('library_folder_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->string('audience', 32)->default('general');
            $table->unsignedBigInteger('instructor_id')->nullable();
            $table->string('title');
            $table->string('series_title')->nullable();
            $table->string('age_label', 40)->nullable();
            $table->string('content_theme', 40)->default('general');
            $table->text('description')->nullable();
            $table->string('external_url', 2000)->nullable();
            $table->string('file_path', 1000)->nullable();
            $table->string('storage_disk', 40)->nullable();
            $table->unsignedBigInteger('file_size')->default(0);
            $table->unsignedInteger('duration_seconds')->default(0);
            $table->string('mime_type', 120)->nullable();
            $table->boolean('is_published')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        if (! Schema::hasTable('student_service_entitlements')) {
            Schema::create('student_service_entitlements', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id');
                $table->boolean('includes_libraries')->default(false);
                $table->foreignId('academic_year_id')->nullable();
                $table->string('status')->default('active');
                $table->timestamp('starts_at')->nullable();
                $table->timestamp('expires_at')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('advanced_courses')) {
            Schema::create('advanced_courses', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->unsignedBigInteger('instructor_id')->nullable();
                $table->string('status')->default('published');
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('student_course_enrollments')) {
            Schema::create('student_course_enrollments', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('advanced_course_id');
                $table->string('status')->default('active');
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('certificates')) {
            Schema::create('certificates', function (Blueprint $table) {
                $table->id();
                $table->string('status')->nullable();
                $table->boolean('is_verified')->default(false);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('academic_years')) {
            Schema::create('academic_years', function (Blueprint $table) {
                $table->id();
                $table->string('name')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('academic_year_instructors')) {
            Schema::create('academic_year_instructors', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('instructor_id');
                $table->unsignedBigInteger('academic_year_id');
                $table->text('assigned_courses')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }
    }

    public function test_teacher_video_only_visible_to_own_students_and_admin_list(): void
    {
        $teacher = User::factory()->create([
            'role' => 'instructor',
            'is_active' => true,
            'password' => Hash::make('password'),
        ]);
        $otherTeacher = User::factory()->create([
            'role' => 'instructor',
            'is_active' => true,
            'password' => Hash::make('password'),
        ]);
        $myStudent = User::factory()->create([
            'role' => 'student',
            'is_active' => true,
            'password' => Hash::make('password'),
        ]);
        $otherStudent = User::factory()->create([
            'role' => 'student',
            'is_active' => true,
            'password' => Hash::make('password'),
        ]);
        $admin = User::factory()->create([
            'role' => 'super_admin',
            'is_active' => true,
            'password' => Hash::make('password'),
        ]);

        $course = AdvancedCourse::query()->create([
            'title' => 'كورس المعلم',
            'instructor_id' => $teacher->id,
            'status' => 'published',
        ]);
        DB::table('student_course_enrollments')->insert([
            'user_id' => $myStudent->id,
            'advanced_course_id' => $course->id,
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $otherCourse = AdvancedCourse::query()->create([
            'title' => 'كورس معلم آخر',
            'instructor_id' => $otherTeacher->id,
            'status' => 'published',
        ]);
        unset($otherCourse);

        LibraryVideo::create([
            'title' => 'فيديو عام أكاديمية',
            'external_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'audience' => LibraryVideo::AUDIENCE_GENERAL,
            'is_published' => true,
        ]);

        LibraryVideo::create([
            'title' => 'فيديو خاص بمعلمي',
            'external_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'audience' => LibraryVideo::AUDIENCE_TEACHER_STUDENTS,
            'instructor_id' => $teacher->id,
            'created_by' => $teacher->id,
            'is_published' => true,
        ]);

        $this->actingAs($myStudent)
            ->get(route('student.library.videos'))
            ->assertOk()
            ->assertSee('فيديو عام أكاديمية', false)
            ->assertSee('فيديو خاص بمعلمي', false);

        $this->actingAs($otherStudent)
            ->get(route('student.library.videos'))
            ->assertOk()
            ->assertSee('فيديو عام أكاديمية', false)
            ->assertDontSee('فيديو خاص بمعلمي', false);

        $private = LibraryVideo::query()->where('title', 'فيديو خاص بمعلمي')->first();
        $this->actingAs($otherStudent)
            ->get(route('student.library.videos.show', $private))
            ->assertForbidden();

        $this->actingAs($admin)
            ->get(route('admin.libraries.videos.index'))
            ->assertOk()
            ->assertSee('فيديو خاص بمعلمي', false)
            ->assertSee('فيديو عام أكاديمية', false);

        // معلم آخر لا يرى فيديوهات زميله في مكتبته
        $this->actingAs($otherTeacher)
            ->get(route('instructor.libraries.videos.index'))
            ->assertOk()
            ->assertDontSee('فيديو خاص بمعلمي', false);
    }

    public function test_admin_store_forces_general_audience(): void
    {
        $admin = User::factory()->create([
            'role' => 'super_admin',
            'is_active' => true,
            'password' => Hash::make('password'),
        ]);

        $this->actingAs($admin)
            ->post(route('admin.libraries.videos.store'), [
                'title' => 'درس إداري',
                'external_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'is_published' => 1,
                'sort_order' => 0,
            ])
            ->assertRedirect(route('admin.libraries.videos.index'));

        $this->assertDatabaseHas('library_videos', [
            'title' => 'درس إداري',
            'audience' => 'general',
            'instructor_id' => null,
        ]);
    }
}
