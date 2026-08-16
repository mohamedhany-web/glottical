<?php

namespace Tests\Feature;

use App\Models\CurriculumLibraryCategory;
use App\Models\CurriculumLibraryItem;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
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
        $this->buildCurriculumLibrarySchema();
    }

    protected function buildCurriculumLibrarySchema(): void
    {
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

        Schema::create('curriculum_library_category_user', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();
        });

        Schema::create('curriculum_library_item_files', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('curriculum_library_item_id');
            $table->string('file_type')->nullable();
            $table->string('label')->nullable();
            $table->string('path')->nullable();
            $table->string('storage_disk')->nullable();
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();
        });

        Schema::create('curriculum_library_sections', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('curriculum_library_item_id');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('title')->nullable();
            $table->unsignedInteger('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('curriculum_library_preview_opens', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('curriculum_library_item_id');
            $table->timestamp('opened_at')->nullable();
        });
    }

    public function test_curriculum_route_registered(): void
    {
        $this->assertTrue(\Illuminate\Support\Facades\Route::has('student.library.curriculum'));
        $this->assertTrue(\Illuminate\Support\Facades\Route::has('curriculum-library.index'));
    }

    public function test_student_sees_uploaded_curricula_not_enrolled_courses(): void
    {
        $student = User::factory()->create([
            'role' => 'student',
            'is_active' => true,
            'password' => Hash::make('password'),
        ]);

        $arabic = CurriculumLibraryCategory::query()->create([
            'name' => 'عربي',
            'slug' => 'arabic',
            'order' => 1,
            'is_active' => true,
        ]);
        $qeraa = CurriculumLibraryCategory::query()->create([
            'name' => 'قرائية',
            'slug' => 'qeraa',
            'order' => 2,
            'is_active' => true,
        ]);

        CurriculumLibraryItem::query()->create([
            'category_id' => $arabic->id,
            'title' => 'منهج النحو المرفوع',
            'slug' => 'nahw-uploaded',
            'subject' => 'لغة عربية',
            'grade_level' => 'المستوى الأول',
            'language' => 'ar',
            'is_active' => true,
            'order' => 1,
        ]);
        CurriculumLibraryItem::query()->create([
            'category_id' => $qeraa->id,
            'title' => 'منهج القرائية المرفوع',
            'slug' => 'qeraa-uploaded',
            'subject' => 'قرائية',
            'grade_level' => 'المبتدئ',
            'language' => 'ar',
            'is_active' => true,
            'order' => 1,
        ]);
        CurriculumLibraryItem::query()->create([
            'category_id' => $arabic->id,
            'title' => 'منهج مخفي',
            'slug' => 'hidden-item',
            'is_active' => false,
            'order' => 9,
        ]);

        $page = $this->actingAs($student)
            ->get(route('student.library.curriculum'))
            ->assertOk()
            ->assertSee('منهج النحو المرفوع', false)
            ->assertSee('منهج القرائية المرفوع', false)
            ->assertDontSee('منهج مخفي', false)
            ->assertDontSee('كورساتك المسجّل فيها', false)
            ->assertSee('المناهج التي ترفعها الأكاديمية', false)
            ->assertSee('عربي', false)
            ->assertSee('قرائية', false);

        $html = $page->getContent();
        $this->assertLessThan(
            strpos($html, 'منهج القرائية المرفوع'),
            strpos($html, 'منهج النحو المرفوع'),
            'Arabic category should appear before qeraa'
        );

        $this->actingAs($student)
            ->get(route('student.library.curriculum', ['category_id' => $arabic->id]))
            ->assertOk()
            ->assertSee('منهج النحو المرفوع', false)
            ->assertDontSee('منهج القرائية المرفوع', false);

        $this->actingAs($student)
            ->get(route('student.library.curriculum', ['grade' => 'المبتدئ']))
            ->assertOk()
            ->assertSee('منهج القرائية المرفوع', false)
            ->assertDontSee('منهج النحو المرفوع', false);

        $this->actingAs($student)
            ->get(route('curriculum-library.index'))
            ->assertOk()
            ->assertSee('منهج النحو المرفوع', false);

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
