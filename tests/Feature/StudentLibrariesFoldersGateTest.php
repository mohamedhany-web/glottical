<?php

namespace Tests\Feature;

use App\Models\LibraryFolder;
use App\Models\StudentServiceEntitlement;
use App\Models\User;
use App\Services\LibraryFolderAccessService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\Support\BuildsFeatureSchema;
use Tests\TestCase;

class StudentLibrariesFoldersGateTest extends TestCase
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

        Schema::create('library_folders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('instructor_id')->nullable();
            $table->unsignedBigInteger('academic_year_id')->nullable();
            $table->string('kind')->default('materials');
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
            $table->boolean('is_visible_to_student')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('student_course_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->foreignId('advanced_course_id');
            $table->string('status')->default('active');
            $table->timestamps();
        });

        if (! Schema::hasTable('student_service_entitlements')) {
            Schema::create('student_service_entitlements', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id');
                $table->foreignId('service_package_id')->nullable();
                $table->foreignId('order_id')->nullable();
                $table->string('scope')->nullable();
                $table->string('plan_type')->nullable();
                $table->unsignedInteger('term_months')->nullable();
                $table->unsignedInteger('weekly_group_sessions')->nullable();
                $table->unsignedInteger('weekly_private_sessions')->nullable();
                $table->boolean('includes_community')->default(false);
                $table->boolean('includes_libraries')->default(false);
                $table->foreignId('tutoring_group_id')->nullable();
                $table->foreignId('academic_year_id')->nullable();
                $table->foreignId('academic_subject_id')->nullable();
                $table->unsignedInteger('units_total')->default(0);
                $table->unsignedInteger('units_used')->default(0);
                $table->timestamp('starts_at')->nullable();
                $table->timestamp('expires_at')->nullable();
                $table->string('status')->default('active');
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        Schema::create('live_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->foreignId('course_id')->nullable();
            $table->foreignId('instructor_id')->nullable();
            $table->string('status')->default('ended');
            $table->boolean('require_enrollment')->default(true);
            $table->timestamps();
        });

        Schema::create('live_recordings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id');
            $table->foreignId('library_folder_id')->nullable();
            $table->string('title')->nullable();
            $table->string('status')->default('ready');
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });

        Schema::create('lectures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instructor_id')->nullable();
            $table->foreignId('course_id')->nullable();
            $table->string('title')->nullable();
            $table->string('recording_url')->nullable();
            $table->string('recording_file_path')->nullable();
            $table->timestamps();
        });
    }

    public function test_free_folders_visible_without_entitlement(): void
    {
        $student = User::factory()->create([
            'role' => 'student',
            'is_active' => true,
            'password' => Hash::make('password'),
        ]);

        $free = LibraryFolder::create([
            'name_ar' => 'مجاني',
            'slug' => 'free-mat',
            'kind' => LibraryFolder::KIND_MATERIALS,
            'is_active' => true,
            'requires_library_entitlement' => false,
            'color' => 'blue',
            'sort_order' => 0,
        ]);
        $gated = LibraryFolder::create([
            'name_ar' => 'باقة',
            'slug' => 'gated-mat',
            'kind' => LibraryFolder::KIND_MATERIALS,
            'academic_year_id' => 1,
            'is_active' => true,
            'requires_library_entitlement' => true,
            'color' => 'pink',
            'sort_order' => 1,
        ]);

        $ids = LibraryFolderAccessService::foldersVisibleTo($student, 'materials')->pluck('id');
        $this->assertTrue($ids->contains($free->id));
        $this->assertFalse($ids->contains($gated->id));
    }

    public function test_entitled_student_sees_year_folder_and_materials_page(): void
    {
        $student = User::factory()->create([
            'role' => 'student',
            'is_active' => true,
            'password' => Hash::make('password'),
        ]);

        $folder = LibraryFolder::create([
            'name_ar' => 'مجلد سنة',
            'slug' => 'year-mat',
            'kind' => LibraryFolder::KIND_MATERIALS,
            'academic_year_id' => 5,
            'is_active' => true,
            'requires_library_entitlement' => true,
            'color' => 'blue',
            'sort_order' => 0,
            'icon' => 'fas fa-folder',
        ]);

        StudentServiceEntitlement::create([
            'user_id' => $student->id,
            'includes_libraries' => true,
            'academic_year_id' => 5,
            'status' => StudentServiceEntitlement::STATUS_ACTIVE,
            'starts_at' => now()->subDay(),
            'expires_at' => now()->addMonth(),
            'units_total' => 0,
            'units_used' => 0,
        ]);

        $this->assertTrue(LibraryFolderAccessService::canAccessFolder($student, $folder));

        $this->actingAs($student)
            ->get(route('student.library.materials'))
            ->assertOk()
            ->assertSee('مجلد سنة', false);

        $this->actingAs($student)
            ->get(route('student.library.videos'))
            ->assertOk();
    }
}
