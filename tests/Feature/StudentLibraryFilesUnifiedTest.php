<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Tests\Support\BuildsFeatureSchema;
use Tests\TestCase;

class StudentLibraryFilesUnifiedTest extends TestCase
{
    use BuildsFeatureSchema;

    protected function setUp(): void
    {
        parent::setUp();
        $this->buildFeatureSchema();

        if (! Schema::hasTable('curriculum_library_items')) {
            Schema::create('curriculum_library_items', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('slug')->unique();
                $table->boolean('is_active')->default(true);
                $table->unsignedInteger('order')->default(0);
                $table->timestamps();
            });
        }
    }

    public function test_files_route_is_registered(): void
    {
        $this->assertTrue(Route::has('student.library.files'));
    }

    public function test_student_can_open_unified_files_page(): void
    {
        $student = User::query()->create([
            'name' => 'Files Student',
            'email' => 'files-student-'.uniqid().'@example.com',
            'password' => Hash::make('password'),
            'role' => 'student',
            'is_active' => true,
        ]);

        $this->actingAs($student)
            ->get(route('student.library.files'))
            ->assertOk();

        $this->actingAs($student)
            ->get(route('student.library.files', ['tab' => 'manahij']))
            ->assertOk();

        $this->actingAs($student)
            ->get(route('student.library.files', ['tab' => 'materials']))
            ->assertOk();
    }

    public function test_curriculum_uploads_hardcode_r2_disk(): void
    {
        $controller = file_get_contents(app_path('Http/Controllers/Admin/CurriculumLibraryStructureController.php'));
        $this->assertStringContainsString("store('curriculum-library/materials/", $controller);
        $this->assertStringContainsString("'storage_disk' => 'r2'", $controller);
        $this->assertStringContainsString("\$diskName = 'r2'", $controller);
    }

    public function test_home_and_admin_hub_use_files_language(): void
    {
        $home = file_get_contents(resource_path('views/student/library/home.blade.php'));
        $this->assertStringContainsString("route('student.library.files')", $home);
        $this->assertStringContainsString("tab' => 'manahij'", $home);

        $hub = file_get_contents(resource_path('views/admin/libraries/index.blade.php'));
        $this->assertStringContainsString('مكتبة الملفات', $hub);
        $this->assertStringContainsString('route(\'admin.curriculum-library.index\')', $hub);
        $this->assertStringContainsString('route(\'admin.libraries.materials.index\')', $hub);
    }
}
