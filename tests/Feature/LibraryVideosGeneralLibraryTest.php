<?php

namespace Tests\Feature;

use App\Models\LibraryVideo;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\Support\BuildsFeatureSchema;
use Tests\TestCase;

class LibraryVideosGeneralLibraryTest extends TestCase
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

    public function test_student_library_shows_general_video_not_live_stream(): void
    {
        $student = User::factory()->create([
            'role' => 'student',
            'is_active' => true,
            'password' => Hash::make('password'),
        ]);

        LibraryVideo::create([
            'title' => 'فيديو عام تجريبي',
            'external_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'is_published' => true,
            'sort_order' => 0,
        ]);

        $this->actingAs($student)
            ->get(route('student.library.videos'))
            ->assertOk()
            ->assertSee('فيديو عام تجريبي', false)
            ->assertDontSee('تسجيلات البث', false);

        $video = LibraryVideo::query()->first();
        $this->actingAs($student)
            ->get(route('student.library.videos.show', $video))
            ->assertOk()
            ->assertSee('فيديو عام تجريبي', false);
    }

    public function test_admin_can_store_link_video_without_live_session(): void
    {
        $admin = User::factory()->create([
            'role' => 'super_admin',
            'is_active' => true,
            'password' => Hash::make('password'),
        ]);

        $this->actingAs($admin)
            ->post(route('admin.libraries.videos.store'), [
                'title' => 'درس تجريبي',
                'external_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'is_published' => 1,
                'sort_order' => 0,
                'duration_seconds' => 0,
            ])
            ->assertRedirect(route('admin.libraries.videos.index'));

        $this->assertDatabaseHas('library_videos', [
            'title' => 'درس تجريبي',
            'is_published' => 1,
        ]);
        $this->assertNull(LibraryVideo::query()->where('title', 'درس تجريبي')->value('file_path'));
    }
}
