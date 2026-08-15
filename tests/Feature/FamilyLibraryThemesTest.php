<?php

namespace Tests\Feature;

use App\Models\LectureMaterial;
use App\Models\LibraryFolder;
use App\Models\User;
use App\Support\FamilyLibraryThemes;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\Support\BuildsFeatureSchema;
use Tests\TestCase;

class FamilyLibraryThemesTest extends TestCase
{
    use BuildsFeatureSchema;

    protected function setUp(): void
    {
        parent::setUp();
        $this->buildFeatureSchema();
        $this->buildExtra();
        Storage::fake('public');
    }

    protected function buildExtra(): void
    {
        Schema::create('library_folders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('instructor_id')->nullable();
            $table->unsignedBigInteger('academic_year_id')->nullable();
            $table->string('kind')->default('materials');
            $table->string('content_theme', 40)->default('general');
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

        Schema::create('lecture_materials', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lecture_id')->nullable();
            $table->unsignedBigInteger('library_folder_id')->nullable();
            $table->string('title')->nullable();
            $table->string('content_theme', 40)->default('general');
            $table->string('experience_mode', 20)->default('download');
            $table->text('description')->nullable();
            $table->string('file_name')->nullable();
            $table->string('file_path')->nullable();
            $table->string('storage_disk')->nullable();
            $table->boolean('is_visible_to_student')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
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
                $table->boolean('includes_libraries')->default(true);
                $table->foreignId('academic_year_id')->nullable();
                $table->string('status')->default('active');
                $table->timestamp('starts_at')->nullable();
                $table->timestamp('expires_at')->nullable();
                $table->timestamps();
            });
        }
    }

    public function test_theme_detection_and_experience_modes(): void
    {
        $this->assertSame(FamilyLibraryThemes::BOOKS, FamilyLibraryThemes::detectThemeFromFilename('story.pdf'));
        $this->assertSame(FamilyLibraryThemes::PRESENTATIONS, FamilyLibraryThemes::detectThemeFromFilename('unit.pptx'));
        $this->assertSame(FamilyLibraryThemes::HTML, FamilyLibraryThemes::detectThemeFromFilename('activity.html'));
        $this->assertSame(FamilyLibraryThemes::MODE_VIEW, FamilyLibraryThemes::detectExperienceMode('activity.html'));
        $this->assertSame(FamilyLibraryThemes::MODE_PLAY, FamilyLibraryThemes::detectExperienceMode('game.html', FamilyLibraryThemes::GAMES));
    }

    public function test_student_can_open_html_experience_inside_platform(): void
    {
        $student = User::factory()->create([
            'role' => 'student',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);

        $folder = LibraryFolder::create([
            'kind' => LibraryFolder::KIND_MATERIALS,
            'content_theme' => FamilyLibraryThemes::HTML,
            'name_ar' => 'تفاعلي',
            'slug' => 'interactive-family',
            'is_active' => true,
            'requires_library_entitlement' => false,
            'sort_order' => 0,
        ]);

        $path = 'library/folders/'.$folder->id.'/game.html';
        Storage::disk('public')->put($path, '<html><body><h1>Safe Game</h1></body></html>');

        $material = LectureMaterial::create([
            'library_folder_id' => $folder->id,
            'title' => 'لعبة آمنة',
            'content_theme' => FamilyLibraryThemes::GAMES,
            'experience_mode' => FamilyLibraryThemes::MODE_PLAY,
            'file_name' => 'game.html',
            'file_path' => $path,
            'storage_disk' => 'public',
            'is_visible_to_student' => true,
        ]);

        $this->actingAs($student)
            ->get(route('student.library.home'))
            ->assertOk()
            ->assertSee(__('student_timeline.family_library_title'));

        $this->actingAs($student)
            ->get(route('student.library.materials.experience', $material))
            ->assertOk()
            ->assertSee('لعبة آمنة');

        $this->actingAs($student)
            ->get(route('student.library.materials.experience.raw', $material))
            ->assertOk()
            ->assertSee('Safe Game', false)
            ->assertHeader('X-Frame-Options', 'SAMEORIGIN');
    }

    public function test_pdf_cannot_use_experience_route(): void
    {
        $student = User::factory()->create([
            'role' => 'student',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);

        $folder = LibraryFolder::create([
            'kind' => LibraryFolder::KIND_MATERIALS,
            'content_theme' => FamilyLibraryThemes::BOOKS,
            'name_ar' => 'كتب',
            'slug' => 'books-family',
            'is_active' => true,
            'requires_library_entitlement' => false,
            'sort_order' => 0,
        ]);

        $path = 'library/folders/'.$folder->id.'/book.pdf';
        Storage::disk('public')->put($path, '%PDF-1.4 fake');

        $material = LectureMaterial::create([
            'library_folder_id' => $folder->id,
            'title' => 'كتاب',
            'content_theme' => FamilyLibraryThemes::BOOKS,
            'experience_mode' => FamilyLibraryThemes::MODE_DOWNLOAD,
            'file_name' => 'book.pdf',
            'file_path' => $path,
            'storage_disk' => 'public',
            'is_visible_to_student' => true,
        ]);

        $this->actingAs($student)
            ->get(route('student.library.materials.experience', $material))
            ->assertNotFound();
    }

    public function test_theme_filters_materials_and_videos(): void
    {
        $student = User::factory()->create([
            'role' => 'student',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);

        \App\Models\StudentServiceEntitlement::create([
            'user_id' => $student->id,
            'includes_libraries' => true,
            'status' => 'active',
            'starts_at' => now()->subDay(),
            'expires_at' => now()->addMonth(),
        ]);

        $folder = LibraryFolder::create([
            'kind' => LibraryFolder::KIND_MATERIALS,
            'content_theme' => FamilyLibraryThemes::BOOKS,
            'name_ar' => 'كتب',
            'slug' => 'books-filter',
            'is_active' => true,
            'requires_library_entitlement' => false,
            'sort_order' => 0,
        ]);

        LectureMaterial::create([
            'library_folder_id' => $folder->id,
            'title' => 'كتاب ظاهر',
            'content_theme' => FamilyLibraryThemes::BOOKS,
            'experience_mode' => FamilyLibraryThemes::MODE_DOWNLOAD,
            'file_name' => 'a.pdf',
            'file_path' => 'library/folders/'.$folder->id.'/a.pdf',
            'storage_disk' => 'public',
            'is_visible_to_student' => true,
        ]);
        LectureMaterial::create([
            'library_folder_id' => $folder->id,
            'title' => 'لعبة مخفية في فلتر الكتب',
            'content_theme' => FamilyLibraryThemes::GAMES,
            'experience_mode' => FamilyLibraryThemes::MODE_PLAY,
            'file_name' => 'g.html',
            'file_path' => 'library/folders/'.$folder->id.'/g.html',
            'storage_disk' => 'public',
            'is_visible_to_student' => true,
        ]);

        \App\Models\LibraryVideo::create([
            'title' => 'فيديو أطفال',
            'content_theme' => FamilyLibraryThemes::KIDS,
            'audience' => \App\Models\LibraryVideo::AUDIENCE_GENERAL,
            'external_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'is_published' => true,
            'sort_order' => 0,
        ]);
        \App\Models\LibraryVideo::create([
            'title' => 'مسلسل إسلامي',
            'content_theme' => FamilyLibraryThemes::ISLAMIC,
            'series_title' => 'قصص الأنبياء',
            'age_label' => '4-8',
            'audience' => \App\Models\LibraryVideo::AUDIENCE_GENERAL,
            'external_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'is_published' => true,
            'sort_order' => 1,
        ]);

        $this->actingAs($student)
            ->get(route('student.library.materials', ['theme' => FamilyLibraryThemes::BOOKS]))
            ->assertOk()
            ->assertSee('كتاب ظاهر')
            ->assertDontSee('لعبة مخفية في فلتر الكتب');

        $this->actingAs($student)
            ->get(route('student.library.videos', ['theme' => FamilyLibraryThemes::ISLAMIC]))
            ->assertOk()
            ->assertSee('مسلسل إسلامي')
            ->assertSee('قصص الأنبياء')
            ->assertDontSee('فيديو أطفال');
    }

    public function test_hidden_material_cannot_be_experienced(): void
    {
        $student = User::factory()->create([
            'role' => 'student',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);

        $folder = LibraryFolder::create([
            'kind' => LibraryFolder::KIND_MATERIALS,
            'name_ar' => 'مخفي',
            'slug' => 'hidden-family',
            'is_active' => true,
            'requires_library_entitlement' => false,
            'sort_order' => 0,
        ]);

        $path = 'library/folders/'.$folder->id.'/secret.html';
        Storage::disk('public')->put($path, '<html><body>secret</body></html>');

        $material = LectureMaterial::create([
            'library_folder_id' => $folder->id,
            'title' => 'سري',
            'content_theme' => FamilyLibraryThemes::HTML,
            'experience_mode' => FamilyLibraryThemes::MODE_VIEW,
            'file_name' => 'secret.html',
            'file_path' => $path,
            'storage_disk' => 'public',
            'is_visible_to_student' => false,
        ]);

        $this->actingAs($student)
            ->get(route('student.library.materials.experience', $material))
            ->assertNotFound();
    }
}
