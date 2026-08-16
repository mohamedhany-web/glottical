<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\AdvancedCourse;
use App\Models\CourseSection;
use App\Models\InstructorProfile;
use App\Models\Lecture;
use App\Models\LectureMaterial;
use App\Models\LibraryFolder;
use App\Models\TutorApplication;
use App\Models\User;
use App\Services\LibraryFolderAccessService;
use App\Services\StudentTeacherLinkService;
use App\Support\FamilyLibraryThemes;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\Support\BuildsFeatureSchema;
use Tests\TestCase;

class InstructorAccountLibraryUploadTest extends TestCase
{
    use BuildsFeatureSchema;

    protected function setUp(): void
    {
        parent::setUp();
        $this->buildFeatureSchema();
        $this->buildExtra();
        config(['filesystems.lecture_materials_disk' => 'public']);
        config(['filesystems.public_media_disk' => 'public']);
        Storage::fake('public');
    }

    protected function buildExtra(): void
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

        Schema::create('library_folders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('instructor_id')->nullable();
            $table->unsignedBigInteger('academic_year_id')->nullable();
            $table->string('kind')->default('materials');
            $table->string('content_theme')->nullable();
            $table->string('name_ar');
            $table->string('name_en')->nullable();
            $table->string('slug')->unique();
            $table->string('description_ar')->nullable();
            $table->string('description_en')->nullable();
            $table->string('icon')->nullable();
            $table->string('color')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('requires_library_entitlement')->default(false);
            $table->timestamps();
        });

        Schema::create('lecture_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lecture_id')->nullable();
            $table->foreignId('library_folder_id')->nullable();
            $table->string('title')->nullable();
            $table->string('file_name')->nullable();
            $table->string('file_path')->nullable();
            $table->string('storage_disk')->nullable();
            $table->string('content_theme')->nullable();
            $table->string('experience_mode')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_visible_to_student')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('advanced_courses', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->foreignId('instructor_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('student_course_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->foreignId('advanced_course_id');
            $table->string('status')->default('active');
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
            $table->string('unlock_rule')->nullable();
            $table->unsignedInteger('unlock_percent')->nullable();
            $table->timestamps();
        });

        Schema::create('lectures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->nullable();
            $table->foreignId('course_lesson_id')->nullable();
            $table->foreignId('instructor_id')->nullable();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('teams_registration_link')->nullable();
            $table->string('teams_meeting_link')->nullable();
            $table->string('recording_url')->nullable();
            $table->string('recording_file_path')->nullable();
            $table->string('video_platform')->nullable();
            $table->timestamp('scheduled_at')->nullable();
            $table->unsignedInteger('duration_minutes')->nullable();
            $table->unsignedInteger('min_watch_percent_to_unlock_next')->nullable();
            $table->string('status')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('has_attendance_tracking')->default(false);
            $table->boolean('has_assignment')->default(false);
            $table->boolean('has_evaluation')->default(false);
            $table->timestamps();
        });

        Schema::create('student_instructor_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id');
            $table->foreignId('instructor_id');
            $table->foreignId('academic_year_id')->nullable();
            $table->string('scope')->default('general');
            $table->string('status')->default('active');
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
    }

    public function test_profile_links_working_instructor_to_library_uploads(): void
    {
        $teacher = $this->makeActivatedInstructor();

        $this->actingAs($teacher)
            ->get(route('instructor.profile'))
            ->assertOk()
            ->assertSee('رفع الماتريال', false)
            ->assertSee('عرض مناهج الأكاديمية', false)
            ->assertSee('بناء منهج كورساتك', false)
            ->assertSee(route('instructor.libraries.materials.index', [], false), false)
            ->assertSee(route('instructor.libraries.curriculum.index', [], false), false);
    }

    public function test_activated_instructor_creates_folder_and_uploads_material_for_own_students(): void
    {
        $year = AcademicYear::create(['name' => 'سنة 1', 'order' => 1, 'is_active' => true]);
        $teacher = $this->makeActivatedInstructor();
        $peer = $this->makeActivatedInstructor();
        $ownStudent = User::factory()->create(['role' => 'student', 'is_active' => true, 'password' => Hash::make('password')]);
        $otherStudent = User::factory()->create(['role' => 'student', 'is_active' => true, 'password' => Hash::make('password')]);

        DB::table('student_instructor_assignments')->insert([
            'student_id' => $ownStudent->id,
            'instructor_id' => $teacher->id,
            'academic_year_id' => $year->id,
            'scope' => 'general',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($teacher)
            ->from(route('instructor.profile'))
            ->post(route('instructor.libraries.materials.folders.store'), [
                'name_ar' => 'ماتريال ملفي الشخصي',
                'name_en' => 'Profile materials',
                'academic_year_id' => $year->id,
                'content_theme' => FamilyLibraryThemes::BOOKS,
            ])
            ->assertRedirect();

        $folder = LibraryFolder::query()->where('instructor_id', $teacher->id)->first();
        $this->assertNotNull($folder);
        $this->assertSame(LibraryFolder::KIND_MATERIALS, $folder->kind);
        $this->assertFalse($folder->requires_library_entitlement);

        $pdf = UploadedFile::fake()->createWithContent('unit-one.pdf', "%PDF-1.4\n%test\n");

        $this->actingAs($teacher)
            ->from(route('instructor.libraries.materials.show', $folder))
            ->post(route('instructor.libraries.materials.upload', $folder), [
                'title' => 'وحدة من الملف الشخصي',
                'file' => $pdf,
                'is_visible_to_student' => '1',
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $material = LectureMaterial::query()->where('library_folder_id', $folder->id)->first();
        $this->assertNotNull($material);
        $this->assertSame('وحدة من الملف الشخصي', $material->title);
        $this->assertSame('public', $material->storage_disk);
        Storage::disk('public')->assertExists($material->file_path);

        $this->assertTrue(StudentTeacherLinkService::studentStudiesWith($ownStudent, (int) $teacher->id));
        $this->assertTrue(LibraryFolderAccessService::canAccessFolder($ownStudent, $folder));
        $this->assertFalse(LibraryFolderAccessService::canAccessFolder($otherStudent, $folder));

        $this->actingAs($teacher)
            ->get(route('instructor.libraries.materials.download', [$folder, $material]))
            ->assertOk();

        $this->actingAs($peer)
            ->post(route('instructor.libraries.materials.upload', $folder), [
                'title' => 'اختراق',
                'file' => UploadedFile::fake()->createWithContent('hack.pdf', "%PDF-1.4\n%x\n"),
            ])
            ->assertForbidden();
    }

    public function test_instructor_cannot_upload_to_academy_folder(): void
    {
        $teacher = $this->makeActivatedInstructor();
        $academy = LibraryFolder::create([
            'instructor_id' => null,
            'kind' => LibraryFolder::KIND_MATERIALS,
            'name_ar' => 'مجلد أكاديمية',
            'slug' => 'academy-mat-'.uniqid(),
            'is_active' => true,
            'requires_library_entitlement' => true,
            'color' => 'blue',
            'sort_order' => 0,
        ]);

        $this->actingAs($teacher)
            ->get(route('instructor.libraries.materials.show', $academy))
            ->assertOk()
            ->assertDontSee('رفع محتوى آمن', false);

        $this->actingAs($teacher)
            ->post(route('instructor.libraries.materials.upload', $academy), [
                'title' => 'محاولة رفع إداري',
                'file' => UploadedFile::fake()->createWithContent('admin.pdf', "%PDF-1.4\n%x\n"),
            ])
            ->assertForbidden();
    }

    public function test_unactivated_instructor_cannot_upload_materials(): void
    {
        $year = AcademicYear::create(['name' => 'سنة 1', 'order' => 1, 'is_active' => true]);
        $newbie = $this->makeDraftInstructor();

        $this->actingAs($newbie)
            ->post(route('instructor.libraries.materials.folders.store'), [
                'name_ar' => 'مجلد غير مفعّل',
                'academic_year_id' => $year->id,
                'content_theme' => FamilyLibraryThemes::BOOKS,
            ])
            ->assertRedirect(route('public.tutor.apply.profile'));

        $this->assertSame(0, LibraryFolder::query()->count());
    }

    public function test_instructor_builds_own_course_curriculum_but_cannot_upload_academy_manahij(): void
    {
        $teacher = $this->makeActivatedInstructor();
        $peer = $this->makeActivatedInstructor();

        $ownCourse = AdvancedCourse::create([
            'instructor_id' => $teacher->id,
            'title' => 'كورس المعلم',
            'is_active' => true,
        ]);
        $peerCourse = AdvancedCourse::create([
            'instructor_id' => $peer->id,
            'title' => 'كورس زميل',
            'is_active' => true,
        ]);

        $this->actingAs($teacher)
            ->postJson(route('instructor.courses.sections.store', $ownCourse), [
                'title' => 'وحدة من حسابي',
                'description' => 'قسم أضفته من ملفي',
            ])
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertTrue(CourseSection::query()->where('advanced_course_id', $ownCourse->id)->where('title', 'وحدة من حسابي')->exists());

        $this->actingAs($teacher)
            ->postJson(route('instructor.courses.sections.store', $peerCourse), [
                'title' => 'قسم على كورس غيري',
            ])
            ->assertForbidden();

        $this->actingAs($teacher)
            ->from(route('instructor.profile'))
            ->post(route('admin.curriculum-library.items.store'), [
                'title' => 'منهج أكاديمية من معلم',
            ])
            ->assertForbidden();

        $this->assertSame(0, DB::table('curriculum_library_items')->count());

        $this->actingAs($teacher)
            ->get(route('instructor.libraries.curriculum.index'))
            ->assertOk()
            ->assertSee('لا رفع من هنا', false);
    }

    public function test_instructor_attaches_material_file_when_creating_lecture_on_own_course(): void
    {
        $teacher = $this->makeActivatedInstructor();
        $course = AdvancedCourse::create([
            'instructor_id' => $teacher->id,
            'title' => 'كورس محاضرات',
            'is_active' => true,
        ]);

        $this->actingAs($teacher)
            ->post(route('instructor.lectures.store'), [
                'course_id' => $course->id,
                'title' => 'محاضرة مع ماتريال',
                'scheduled_at' => now()->addDay()->toDateTimeString(),
                'duration_minutes' => 45,
                'material_files' => [
                    UploadedFile::fake()->createWithContent('handout.pdf', "%PDF-1.4\n%handout\n"),
                ],
                'material_titles' => ['ملزمة المحاضرة'],
                'material_visible' => ['1'],
            ])
            ->assertRedirect();

        $lecture = Lecture::query()->where('instructor_id', $teacher->id)->first();
        $this->assertNotNull($lecture);

        $material = LectureMaterial::query()->where('lecture_id', $lecture->id)->first();
        $this->assertNotNull($material);
        $this->assertSame('ملزمة المحاضرة', $material->title);
        Storage::disk('public')->assertExists($material->file_path);
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

    private function makeDraftInstructor(): User
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

        return $newbie->fresh();
    }
}
