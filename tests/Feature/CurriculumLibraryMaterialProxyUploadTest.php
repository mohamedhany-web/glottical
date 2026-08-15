<?php

namespace Tests\Feature;

use App\Http\Controllers\Admin\CurriculumLibraryStructureController;
use App\Models\CurriculumLibraryItem;
use App\Models\CurriculumLibraryMaterial;
use App\Models\CurriculumLibrarySection;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CurriculumLibraryMaterialProxyUploadTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Config::set('filesystems.disks.r2.key', 'test-key');
        Config::set('filesystems.disks.r2.secret', 'test-secret');
        Config::set('filesystems.disks.r2.bucket', 'test-bucket');
        Config::set('filesystems.disks.r2.endpoint', 'https://r2.example.com');
        Storage::fake('r2');

        $this->ensureTables();
    }

    protected function ensureTables(): void
    {
        if (! Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->string('name')->nullable();
                $table->string('email')->nullable();
                $table->string('password')->nullable();
                $table->string('role')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('curriculum_library_items')) {
            Schema::create('curriculum_library_items', function (Blueprint $table) {
                $table->id();
                $table->string('title')->nullable();
                $table->string('slug')->unique();
                $table->boolean('is_active')->default(true);
                $table->unsignedBigInteger('category_id')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('curriculum_library_sections')) {
            Schema::create('curriculum_library_sections', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('curriculum_library_item_id');
                $table->string('title')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('curriculum_library_materials')) {
            Schema::create('curriculum_library_materials', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('curriculum_library_section_id')->nullable();
                $table->string('title')->nullable();
                $table->string('path');
                $table->string('storage_disk', 32)->default('r2');
                $table->string('original_name')->nullable();
                $table->string('file_kind', 20)->default('other');
                $table->boolean('view_in_platform')->default(true);
                $table->boolean('allow_download')->default(false);
                $table->unsignedInteger('order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }
    }

    protected function makeSection(): array
    {
        $item = CurriculumLibraryItem::query()->create([
            'title' => 'منهج رفع',
            'slug' => 'proxy-item-'.uniqid(),
            'is_active' => true,
        ]);
        $section = CurriculumLibrarySection::query()->create([
            'curriculum_library_item_id' => $item->id,
            'title' => 'قسم',
            'is_active' => true,
        ]);

        return compact('item', 'section');
    }

    public function test_proxy_rejects_invalid_token(): void
    {
        ['item' => $item, 'section' => $section] = $this->makeSection();
        $user = User::query()->create([
            'name' => 'Admin',
            'email' => 'admin-proxy-'.uniqid().'@example.com',
            'password' => 'secret',
            'role' => 'super_admin',
        ]);
        $this->actingAs($user);

        $controller = $this->app->make(CurriculumLibraryStructureController::class);
        $file = UploadedFile::fake()->create('lesson.pdf', 40, 'application/pdf');
        $request = Request::create(
            '/admin/curriculum-library/items/'.$item->id.'/sections/'.$section->id.'/materials/proxy-upload',
            'POST',
            ['upload_token' => str_repeat('a', 64)],
            [],
            ['file' => $file]
        );
        $request->setUserResolver(fn () => $user);
        $request->headers->set('Accept', 'application/json');

        $response = $controller->proxyMaterialUpload($request, $item, $section);

        $this->assertSame(422, $response->getStatusCode());
        $this->assertFalse((bool) $response->getData(true)['ok']);
    }

    public function test_proxy_writes_file_then_complete_creates_material(): void
    {
        ['item' => $item, 'section' => $section] = $this->makeSection();
        $user = User::query()->create([
            'name' => 'Admin',
            'email' => 'admin-proxy-ok-'.uniqid().'@example.com',
            'password' => 'secret',
            'role' => 'super_admin',
        ]);
        $this->actingAs($user);

        $path = 'curriculum-library/materials/'.$section->id.'/proxy-'.uniqid().'.pdf';
        $token = str_repeat('b', 64);
        Cache::put('curriculum_library_mat_presign:'.$token, [
            'path' => $path,
            'curriculum_library_item_id' => (int) $item->id,
            'curriculum_library_section_id' => (int) $section->id,
            'user_id' => (int) $user->id,
            'mime' => 'application/pdf',
            'disk' => 'r2',
            'original_name' => 'lesson.pdf',
            'max_bytes' => 150 * 1024 * 1024,
        ], now()->addMinutes(10));

        $controller = $this->app->make(CurriculumLibraryStructureController::class);
        $file = UploadedFile::fake()->createWithContent('lesson.pdf', "%PDF-1.4\n".str_repeat('x', 2048));
        $proxyRequest = Request::create(
            '/admin/curriculum-library/items/'.$item->id.'/sections/'.$section->id.'/materials/proxy-upload',
            'POST',
            ['upload_token' => $token],
            [],
            ['file' => $file]
        );
        $proxyRequest->setUserResolver(fn () => $user);

        $proxyResponse = $controller->proxyMaterialUpload($proxyRequest, $item, $section);
        $this->assertSame(200, $proxyResponse->getStatusCode());
        $this->assertTrue((bool) $proxyResponse->getData(true)['ok']);
        $this->assertTrue(Storage::disk('r2')->exists($path));

        $completeRequest = Request::create(
            '/admin/curriculum-library/items/'.$item->id.'/sections/'.$section->id.'/materials/complete-direct',
            'POST',
            ['upload_token' => $token, 'title' => 'درس تجريبي', 'view_in_platform' => 1]
        );
        $completeRequest->setUserResolver(fn () => $user);
        $completeRequest->setLaravelSession($this->app['session.store']);

        $completeResponse = $controller->completeMaterialDirectUpload($completeRequest, $item, $section);
        $this->assertSame(200, $completeResponse->getStatusCode());
        $this->assertTrue((bool) $completeResponse->getData(true)['ok']);

        $material = CurriculumLibraryMaterial::query()->where('path', $path)->first();
        $this->assertNotNull($material);
        $this->assertSame('pdf', $material->file_kind);
        $this->assertSame('lesson.pdf', $material->original_name);
        $this->assertSame('r2', $material->storage_disk);
    }
}
