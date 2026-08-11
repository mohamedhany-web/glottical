<?php

namespace Tests\Feature;

use App\Models\AcademicSubject;
use App\Models\AcademicYear;
use App\Models\AdvancedCourse;
use App\Models\Lecture;
use App\Models\LectureMaterial;
use App\Models\LiveRecording;
use App\Models\LiveSession;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\Support\BuildsFeatureSchema;
use Tests\TestCase;

class AdminLibrariesHubTest extends TestCase
{
    use BuildsFeatureSchema;

    protected function setUp(): void
    {
        parent::setUp();
        $this->buildFeatureSchema();
        $this->buildLibrariesSchema();
    }

    protected function buildLibrariesSchema(): void
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
            $table->foreignId('instructor_id')->nullable();
            $table->string('recording_url')->nullable();
            $table->string('recording_file_path')->nullable();
            $table->string('video_platform')->nullable();
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamps();
        });

        Schema::create('lecture_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lecture_id');
            $table->string('title')->nullable();
            $table->string('file_name')->nullable();
            $table->string('file_path')->nullable();
            $table->string('storage_disk', 32)->default('public');
            $table->boolean('is_visible_to_student')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('live_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('room_name')->nullable();
            $table->string('status')->nullable();
            $table->foreignId('course_id')->nullable();
            $table->foreignId('instructor_id')->nullable();
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->timestamps();
        });

        Schema::create('live_recordings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id');
            $table->string('title')->nullable();
            $table->string('file_path')->nullable();
            $table->string('external_url')->nullable();
            $table->string('storage_disk')->nullable();
            $table->unsignedBigInteger('file_size')->default(0);
            $table->unsignedInteger('duration_seconds')->default(0);
            $table->string('status')->default('ready');
            $table->boolean('is_published')->default(false);
            $table->timestamps();
        });

        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->string('status')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->timestamps();
        });
    }

    protected function admin(): User
    {
        return User::factory()->create([
            'role' => 'super_admin',
            'is_active' => true,
            'password' => Hash::make('password'),
        ]);
    }

    public function test_library_routes_are_registered(): void
    {
        $this->assertTrue(\Illuminate\Support\Facades\Route::has('admin.libraries.index'));
        $this->assertTrue(\Illuminate\Support\Facades\Route::has('admin.libraries.materials.index'));
        $this->assertTrue(\Illuminate\Support\Facades\Route::has('admin.libraries.videos.index'));
        $this->assertTrue(\Illuminate\Support\Facades\Route::has('admin.libraries.curriculum.index'));
        $this->assertTrue(\Illuminate\Support\Facades\Route::has('admin.libraries.curriculum.course'));
    }

    public function test_admin_can_open_libraries_hub_and_sections(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->get(route('admin.libraries.index'))->assertOk();
        $this->actingAs($admin)->get(route('admin.libraries.materials.index'))->assertOk();
        $this->actingAs($admin)->get(route('admin.libraries.videos.index'))->assertOk();
        $this->actingAs($admin)->get(route('admin.libraries.curriculum.index'))->assertOk();
    }

    public function test_admin_can_upload_and_toggle_material(): void
    {
        config(['filesystems.lecture_materials_disk' => 'public', 'filesystems.public_media_disk' => 'public']);
        Storage::fake('public');
        $admin = $this->admin();
        $course = AdvancedCourse::create(['title' => 'كورس تجريبي', 'is_active' => true]);
        $lecture = Lecture::create(['title' => 'محاضرة 1', 'course_id' => $course->id]);

        $file = UploadedFile::fake()->create('notes.pdf', 120, 'application/pdf');

        $this->actingAs($admin)
            ->post(route('admin.libraries.materials.store'), [
                'lecture_id' => $lecture->id,
                'title' => 'مذكرة الوحدة',
                'sort_order' => 1,
                'is_visible_to_student' => 1,
                'file' => $file,
            ])
            ->assertRedirect(route('admin.libraries.materials.index'));

        $material = LectureMaterial::query()->first();
        $this->assertNotNull($material);
        $this->assertTrue((bool) $material->is_visible_to_student);
        $this->assertSame('public', $material->storage_disk);
        Storage::disk('public')->assertExists($material->file_path);

        $this->actingAs($admin)
            ->post(route('admin.libraries.materials.toggle', $material))
            ->assertRedirect();

        $this->assertFalse((bool) $material->fresh()->is_visible_to_student);
    }

    public function test_admin_can_create_and_publish_video(): void
    {
        $admin = $this->admin();
        $session = LiveSession::create(['title' => 'بث تجريبي', 'scheduled_at' => now()]);

        $this->actingAs($admin)
            ->post(route('admin.libraries.videos.store'), [
                'session_id' => $session->id,
                'title' => 'تسجيل البث',
                'external_url' => 'https://example.com/video.mp4',
                'status' => 'ready',
                'is_published' => 1,
            ])
            ->assertRedirect();

        $rec = LiveRecording::query()->first();
        $this->assertNotNull($rec);
        $this->assertTrue((bool) $rec->is_published);

        $this->actingAs($admin)
            ->post(route('admin.libraries.videos.toggle', $rec))
            ->assertRedirect();

        $this->assertFalse((bool) $rec->fresh()->is_published);
    }

    public function test_admin_can_view_curriculum_course(): void
    {
        $admin = $this->admin();
        $year = AcademicYear::create(['name' => 'سنة 1', 'order' => 1, 'is_active' => true]);
        $subject = AcademicSubject::create([
            'academic_year_id' => $year->id,
            'name' => 'رياضيات',
            'order' => 1,
            'is_active' => true,
        ]);
        $course = AdvancedCourse::create([
            'title' => 'جبر',
            'academic_subject_id' => $subject->id,
            'academic_year_id' => $year->id,
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.libraries.curriculum.course', $course))
            ->assertOk()
            ->assertSee('جبر', false);
    }
}
