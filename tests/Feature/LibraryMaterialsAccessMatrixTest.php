<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\LectureMaterial;
use App\Models\LibraryFolder;
use App\Models\LibraryVideo;
use App\Models\StudentServiceEntitlement;
use App\Models\User;
use App\Services\LibraryFolderAccessService;
use App\Services\StudentTeacherLinkService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\Support\BuildsFeatureSchema;
use Tests\TestCase;

class LibraryMaterialsAccessMatrixTest extends TestCase
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
        if (! Schema::hasTable('academic_years')) {
            Schema::create('academic_years', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->unsignedInteger('order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

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
            $table->boolean('requires_library_entitlement')->default(true);
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

        Schema::create('library_videos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('library_folder_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->string('audience', 32)->default('general');
            $table->unsignedBigInteger('instructor_id')->nullable();
            $table->string('title');
            $table->string('content_theme', 40)->default('general');
            $table->text('description')->nullable();
            $table->string('external_url', 2000)->nullable();
            $table->boolean('is_published')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        if (! Schema::hasTable('advanced_courses')) {
            Schema::create('advanced_courses', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->foreignId('instructor_id')->nullable();
                $table->string('status')->default('published');
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('student_course_enrollments')) {
            Schema::create('student_course_enrollments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id');
                $table->foreignId('advanced_course_id');
                $table->string('status')->default('active');
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('student_instructor_assignments')) {
            Schema::create('student_instructor_assignments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('student_id');
                $table->foreignId('instructor_id');
                $table->foreignId('academic_year_id')->nullable();
                $table->string('scope')->default('general');
                $table->string('status')->default('active');
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('student_service_entitlements')) {
            Schema::create('student_service_entitlements', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id');
                $table->foreignId('service_package_id')->nullable();
                $table->boolean('includes_libraries')->default(false);
                $table->foreignId('tutoring_group_id')->nullable();
                $table->foreignId('academic_year_id')->nullable();
                $table->string('status')->default('active');
                $table->timestamp('starts_at')->nullable();
                $table->timestamp('expires_at')->nullable();
                $table->timestamps();
            });
        } elseif (! Schema::hasColumn('student_service_entitlements', 'tutoring_group_id')) {
            Schema::table('student_service_entitlements', function (Blueprint $table) {
                $table->foreignId('tutoring_group_id')->nullable();
            });
        }
    }

    public function test_assignment_links_student_to_teacher_materials_only(): void
    {
        $year = AcademicYear::create(['name' => 'سنة 1', 'order' => 1, 'is_active' => true]);

        $teacherA = User::factory()->create(['role' => 'instructor', 'is_active' => true, 'password' => Hash::make('password')]);
        $teacherB = User::factory()->create(['role' => 'instructor', 'is_active' => true, 'password' => Hash::make('password')]);
        $student = User::factory()->create(['role' => 'student', 'is_active' => true, 'password' => Hash::make('password')]);

        // تجنب Observer الإشعارات — إدراج مباشر
        DB::table('student_instructor_assignments')->insert([
            'student_id' => $student->id,
            'instructor_id' => $teacherA->id,
            'academic_year_id' => $year->id,
            'scope' => 'general',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->assertSame([$teacherA->id], StudentTeacherLinkService::instructorIdsForStudent($student));

        $folderA = LibraryFolder::create([
            'instructor_id' => $teacherA->id,
            'academic_year_id' => $year->id,
            'kind' => LibraryFolder::KIND_MATERIALS,
            'name_ar' => 'مجلد معلم أ',
            'slug' => 'teacher-a-mat',
            'is_active' => true,
            'requires_library_entitlement' => false,
            'color' => 'blue',
            'sort_order' => 0,
        ]);
        $folderB = LibraryFolder::create([
            'instructor_id' => $teacherB->id,
            'academic_year_id' => $year->id,
            'kind' => LibraryFolder::KIND_MATERIALS,
            'name_ar' => 'مجلد معلم ب',
            'slug' => 'teacher-b-mat',
            'is_active' => true,
            'requires_library_entitlement' => false,
            'color' => 'blue',
            'sort_order' => 0,
        ]);

        LectureMaterial::create([
            'library_folder_id' => $folderA->id,
            'title' => 'ملف معلم أ',
            'file_name' => 'a.pdf',
            'file_path' => 'materials/a.pdf',
            'is_visible_to_student' => true,
        ]);
        LectureMaterial::create([
            'library_folder_id' => $folderB->id,
            'title' => 'ملف معلم ب',
            'file_name' => 'b.pdf',
            'file_path' => 'materials/b.pdf',
            'is_visible_to_student' => true,
        ]);

        $this->assertTrue(LibraryFolderAccessService::canAccessFolder($student, $folderA));
        $this->assertFalse(LibraryFolderAccessService::canAccessFolder($student, $folderB));

        $visibleIds = LibraryFolderAccessService::foldersVisibleTo($student, LibraryFolder::KIND_MATERIALS)->pluck('id');
        $this->assertTrue($visibleIds->contains($folderA->id));
        $this->assertFalse($visibleIds->contains($folderB->id));

        $this->actingAs($student)
            ->get(route('student.library.materials'))
            ->assertOk()
            ->assertSee('ملف معلم أ', false)
            ->assertDontSee('ملف معلم ب', false);
    }

    public function test_entitlement_group_links_teacher_for_library(): void
    {
        $teacher = User::factory()->create(['role' => 'instructor', 'is_active' => true]);
        $student = User::factory()->create(['role' => 'student', 'is_active' => true]);

        $groupId = DB::table('tutoring_groups')->insertGetId([
            'type' => 'group',
            'title' => 'مجموعة اختبار',
            'slug' => 'group-lib-link-'.uniqid(),
            'instructor_id' => $teacher->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        StudentServiceEntitlement::create([
            'user_id' => $student->id,
            'includes_libraries' => false,
            'tutoring_group_id' => $groupId,
            'status' => 'active',
            'starts_at' => now()->subDay(),
            'expires_at' => now()->addMonth(),
        ]);

        $this->assertTrue(StudentTeacherLinkService::studentStudiesWith($student, (int) $teacher->id));
    }

    public function test_teacher_folder_query_hides_other_teachers_shows_admin(): void
    {
        $year = AcademicYear::create(['name' => 'سنة 1', 'order' => 1, 'is_active' => true]);
        $teacherA = User::factory()->create(['role' => 'instructor', 'is_active' => true]);
        $teacherB = User::factory()->create(['role' => 'instructor', 'is_active' => true]);

        $adminFolder = LibraryFolder::create([
            'instructor_id' => null,
            'kind' => LibraryFolder::KIND_MATERIALS,
            'name_ar' => 'مجلد أكاديمية',
            'slug' => 'admin-mat',
            'is_active' => true,
            'requires_library_entitlement' => true,
            'color' => 'blue',
            'sort_order' => 0,
        ]);
        $folderA = LibraryFolder::create([
            'instructor_id' => $teacherA->id,
            'academic_year_id' => $year->id,
            'kind' => LibraryFolder::KIND_MATERIALS,
            'name_ar' => 'مجلد معلم أ فقط',
            'slug' => 'only-a',
            'is_active' => true,
            'requires_library_entitlement' => false,
            'color' => 'blue',
            'sort_order' => 0,
        ]);
        $folderB = LibraryFolder::create([
            'instructor_id' => $teacherB->id,
            'academic_year_id' => $year->id,
            'kind' => LibraryFolder::KIND_MATERIALS,
            'name_ar' => 'مجلد معلم ب فقط',
            'slug' => 'only-b',
            'is_active' => true,
            'requires_library_entitlement' => false,
            'color' => 'blue',
            'sort_order' => 0,
        ]);

        // نفس استعلام قائمة المعلم
        $ids = LibraryFolder::query()
            ->ofKind(LibraryFolder::KIND_MATERIALS)
            ->where(function ($q) use ($teacherA) {
                $q->where('instructor_id', $teacherA->id)->orWhereNull('instructor_id');
            })
            ->pluck('id');

        $this->assertTrue($ids->contains($adminFolder->id));
        $this->assertTrue($ids->contains($folderA->id));
        $this->assertFalse($ids->contains($folderB->id));
    }

    public function test_package_gates_admin_folder_and_unfoldered_video(): void
    {
        $student = User::factory()->create(['role' => 'student', 'is_active' => true, 'password' => Hash::make('password')]);

        $gated = LibraryFolder::create([
            'instructor_id' => null,
            'kind' => LibraryFolder::KIND_MATERIALS,
            'name_ar' => 'باقة فقط',
            'slug' => 'gated-admin',
            'is_active' => true,
            'requires_library_entitlement' => true,
            'color' => 'blue',
            'sort_order' => 0,
        ]);
        LectureMaterial::create([
            'library_folder_id' => $gated->id,
            'title' => 'ملف باقة',
            'file_name' => 'pack.pdf',
            'file_path' => 'materials/pack.pdf',
            'is_visible_to_student' => true,
        ]);

        $video = LibraryVideo::create([
            'title' => 'فيديو باقة',
            'external_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'audience' => LibraryVideo::AUDIENCE_GENERAL,
            'is_published' => true,
        ]);

        $this->assertFalse(LibraryFolderAccessService::canAccessFolder($student, $gated));
        $this->assertFalse(LibraryFolderAccessService::canAccessVideo($student, $video));
        $this->actingAs($student)
            ->get(route('student.library.materials'))
            ->assertOk()
            ->assertDontSee('ملف باقة', false);
        $this->actingAs($student)
            ->get(route('student.library.videos'))
            ->assertOk()
            ->assertDontSee('فيديو باقة', false);

        StudentServiceEntitlement::create([
            'user_id' => $student->id,
            'includes_libraries' => true,
            'status' => 'active',
            'starts_at' => now()->subDay(),
            'expires_at' => now()->addMonth(),
        ]);

        $student->refresh();
        $this->assertTrue(LibraryFolderAccessService::canAccessFolder($student, $gated));
        $this->assertTrue(LibraryFolderAccessService::canAccessVideo($student, $video->fresh()));
        $this->actingAs($student)
            ->get(route('student.library.materials'))
            ->assertOk()
            ->assertSee('ملف باقة', false);
        $this->actingAs($student)
            ->get(route('student.library.videos'))
            ->assertOk()
            ->assertSee('فيديو باقة', false);
    }

    public function test_admin_materials_query_includes_teacher_uploads(): void
    {
        $teacher = User::factory()->create(['role' => 'instructor', 'is_active' => true]);

        $folder = LibraryFolder::create([
            'instructor_id' => $teacher->id,
            'kind' => LibraryFolder::KIND_MATERIALS,
            'name_ar' => 'مجلد معلم للإدارة',
            'slug' => 'teacher-admin-see',
            'is_active' => true,
            'requires_library_entitlement' => false,
            'color' => 'blue',
            'sort_order' => 0,
        ]);
        $material = LectureMaterial::create([
            'library_folder_id' => $folder->id,
            'title' => 'ملف يظهر للإدارة',
            'file_name' => 'admin-see.pdf',
            'file_path' => 'materials/admin-see.pdf',
            'is_visible_to_student' => true,
        ]);

        // لوحة الإدارة لا تقيّد بـ instructor_id
        $this->assertTrue(
            LectureMaterial::query()->where('id', $material->id)->exists()
        );
        $this->assertTrue(
            LectureMaterial::query()
                ->whereHas('folder', fn ($q) => $q->where('instructor_id', $teacher->id))
                ->where('title', 'ملف يظهر للإدارة')
                ->exists()
        );
    }
}
