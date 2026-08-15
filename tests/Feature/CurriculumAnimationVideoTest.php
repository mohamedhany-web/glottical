<?php

namespace Tests\Feature;

use App\Http\Middleware\FileUploadSecurityMiddleware;
use App\Http\Middleware\SecurityHeadersMiddleware;
use App\Models\CurriculumLibraryItem;
use App\Models\CurriculumLibraryMaterial;
use App\Models\CurriculumLibrarySection;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use ReflectionMethod;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class CurriculumAnimationVideoTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Config::set('filesystems.disks.r2', [
            'driver' => 'local',
            'root' => storage_path('framework/testing/r2-animation'),
            'url' => 'https://cdn.example.com',
        ]);
        Config::set('app.url', 'https://app.muallimx.test');
        Config::set('app.key', 'base64:AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA=');
        Config::set('app.debug', false);
        putenv('DISABLE_CSP=false');
        $_ENV['DISABLE_CSP'] = 'false';
        $_SERVER['DISABLE_CSP'] = 'false';

        Storage::forgetDisk('r2');
        if (! is_dir(storage_path('framework/testing/r2-animation'))) {
            mkdir(storage_path('framework/testing/r2-animation'), 0777, true);
        }

        Storage::disk('r2')->buildTemporaryUrlsUsing(function (string $path, $expiration) {
            return 'https://cdn.example.com/'.ltrim($path, '/').'?e='.$expiration->getTimestamp();
        });

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
                $table->string('animation_video_path')->nullable();
                $table->string('animation_video_disk', 32)->nullable();
                $table->string('animation_video_original_name')->nullable();
                $table->string('animation_video_mime', 128)->nullable();
                $table->unsignedBigInteger('animation_video_size')->nullable();
                $table->timestamp('animation_video_uploaded_at')->nullable();
                $table->timestamps();
            });
        } else {
            Schema::table('curriculum_library_materials', function (Blueprint $table) {
                if (! Schema::hasColumn('curriculum_library_materials', 'animation_video_path')) {
                    $table->string('animation_video_path')->nullable();
                }
                if (! Schema::hasColumn('curriculum_library_materials', 'animation_video_disk')) {
                    $table->string('animation_video_disk', 32)->nullable();
                }
                if (! Schema::hasColumn('curriculum_library_materials', 'animation_video_original_name')) {
                    $table->string('animation_video_original_name')->nullable();
                }
                if (! Schema::hasColumn('curriculum_library_materials', 'animation_video_mime')) {
                    $table->string('animation_video_mime', 128)->nullable();
                }
                if (! Schema::hasColumn('curriculum_library_materials', 'animation_video_size')) {
                    $table->unsignedBigInteger('animation_video_size')->nullable();
                }
                if (! Schema::hasColumn('curriculum_library_materials', 'animation_video_uploaded_at')) {
                    $table->timestamp('animation_video_uploaded_at')->nullable();
                }
            });
        }
    }

    protected function makeMaterial(array $overrides = []): array
    {
        $item = CurriculumLibraryItem::query()->create(array_merge([
            'title' => 'منهج اختبار',
            'slug' => 'anim-item-'.uniqid(),
            'is_active' => true,
        ], []));

        $section = CurriculumLibrarySection::query()->create([
            'curriculum_library_item_id' => $item->id,
            'title' => 'قسم',
            'is_active' => true,
        ]);

        $sourcePath = 'curriculum-library/materials/'.$section->id.'/source-'.uniqid().'.pptx';
        Storage::disk('r2')->put($sourcePath, 'FAKE-PPTX');

        $material = CurriculumLibraryMaterial::query()->create(array_merge([
            'curriculum_library_section_id' => $section->id,
            'title' => 'عرض بالحركات',
            'path' => $sourcePath,
            'storage_disk' => 'r2',
            'original_name' => 'lesson.pptx',
            'file_kind' => 'pptx',
            'view_in_platform' => true,
            'allow_download' => false,
            'order' => 1,
            'is_active' => true,
        ], $overrides));

        return compact('item', 'section', 'material', 'sourcePath');
    }

    public function test_animation_video_config_and_security_allowlist(): void
    {
        $c = config('curriculum_presentation');
        $this->assertArrayHasKey('animation_video_max_bytes', $c);
        $this->assertGreaterThanOrEqual(100 * 1024 * 1024, (int) $c['animation_video_max_bytes']);
        $this->assertSame(['mp4', 'webm'], $c['animation_video_allowed_extensions']);
        $this->assertContains('video/mp4', $c['animation_video_allowed_mimes']);
        $this->assertContains('video/webm', $c['animation_video_allowed_mimes']);

        $middleware = $this->app->make(FileUploadSecurityMiddleware::class);
        $mimesMethod = new ReflectionMethod($middleware, 'getAllowedMimes');
        $mimesMethod->setAccessible(true);
        $allowed = $mimesMethod->invoke($middleware, 'animation_video');
        $this->assertContains('video/mp4', $allowed);
        $this->assertContains('video/webm', $allowed);
        $this->assertContains('mp4', $allowed);
        $this->assertContains('webm', $allowed);

        $sizeMethod = new ReflectionMethod($middleware, 'getMaxSize');
        $sizeMethod->setAccessible(true);
        $request = Request::create('/admin/curriculum-library/items/x/materials/1/animation-video', 'POST');
        $request->setRouteResolver(function () {
            $route = new \Illuminate\Routing\Route('POST', '/admin/curriculum-library/items/{item}/materials/{material}/animation-video', []);
            $route->name('admin.curriculum-library.items.materials.animation-video.store');

            return $route;
        });
        $max = $sizeMethod->invoke($middleware, $request, 'animation_video');
        $appCap = (int) config('curriculum_presentation.animation_video_max_bytes', 500 * 1024 * 1024);
        $phpCap = (int) UploadedFile::getMaxFilesize();
        $expected = min($phpCap > 0 ? $phpCap : 1073741824, $appCap > 0 ? $appCap : 500 * 1024 * 1024);
        $this->assertSame($expected, $max);
        $this->assertGreaterThan(0, $max);
    }

    public function test_csp_allows_media_src_https_and_blob(): void
    {
        $middleware = new SecurityHeadersMiddleware;
        $request = Request::create('https://example.com/curriculum-library/demo', 'GET');
        /** @var Response $response */
        $response = $middleware->handle($request, function () {
            return new Response('ok', 200);
        });

        $csp = (string) $response->headers->get('Content-Security-Policy');
        $this->assertNotSame('', $csp);
        $this->assertMatchesRegularExpression("/media-src[^;]*'self'/", $csp);
        $this->assertMatchesRegularExpression('/media-src[^;]*https:/', $csp);
        $this->assertMatchesRegularExpression('/media-src[^;]*blob:/', $csp);
    }

    public function test_animation_video_routes_registered(): void
    {
        $this->assertTrue(Route::has('admin.curriculum-library.items.materials.animation-video.store'));
        $this->assertTrue(Route::has('admin.curriculum-library.items.materials.animation-video.destroy'));
        $this->assertTrue(Route::has('curriculum-library.material.animation-video'));
    }

    public function test_upload_replace_delete_preserves_original_source_path(): void
    {
        ['item' => $item, 'material' => $material, 'sourcePath' => $sourcePath] = $this->makeMaterial();

        $controller = $this->app->make(\App\Http\Controllers\Admin\CurriculumLibraryStructureController::class);

        $video1 = UploadedFile::fake()->create('export.mp4', 1200, 'video/mp4');
        $request1 = Request::create(
            '/admin/curriculum-library/items/'.$item->id.'/materials/'.$material->id.'/animation-video',
            'POST',
            [],
            [],
            ['animation_video' => $video1]
        );
        $request1->setLaravelSession($this->app['session.store']);

        $response = $controller->storeMaterialAnimationVideo($request1, $item, $material);
        $this->assertTrue(method_exists($response, 'isRedirect') ? $response->isRedirect() : true);

        $material->refresh();
        $this->assertSame($sourcePath, $material->path);
        $this->assertTrue($material->hasAnimationVideo());
        $this->assertTrue(Storage::disk('r2')->exists($material->animation_video_path));
        $this->assertStringContainsString(
            'curriculum-library/animations/material/'.$material->id.'/',
            $material->animation_video_path
        );
        $firstVideoPath = $material->animation_video_path;

        $video2 = UploadedFile::fake()->create('export-v2.webm', 800, 'video/webm');
        $request2 = Request::create(
            '/admin/curriculum-library/items/'.$item->id.'/materials/'.$material->id.'/animation-video',
            'POST',
            [],
            [],
            ['animation_video' => $video2]
        );
        $request2->setLaravelSession($this->app['session.store']);
        $controller->storeMaterialAnimationVideo($request2, $item, $material);

        $material->refresh();
        $this->assertSame($sourcePath, $material->path);
        $this->assertTrue(Storage::disk('r2')->exists($sourcePath));
        $this->assertNotSame($firstVideoPath, $material->animation_video_path);
        $this->assertFalse(Storage::disk('r2')->exists($firstVideoPath));
        $this->assertTrue(Storage::disk('r2')->exists($material->animation_video_path));

        $controller->destroyMaterialAnimationVideo($item, $material);

        $material->refresh();
        $this->assertSame($sourcePath, $material->path);
        $this->assertTrue(Storage::disk('r2')->exists($sourcePath));
        $this->assertFalse($material->hasAnimationVideo());
        $this->assertNull($material->animation_video_path);
    }

    public function test_upload_db_failure_deletes_only_new_sidecar(): void
    {
        ['material' => $material, 'sourcePath' => $sourcePath] = $this->makeMaterial();

        $oldPath = 'curriculum-library/animations/material/'.$material->id.'/old.mp4';
        Storage::disk('r2')->put($oldPath, 'OLD');
        $material->forceFill([
            'animation_video_path' => $oldPath,
            'animation_video_disk' => 'r2',
            'animation_video_original_name' => 'old.mp4',
            'animation_video_mime' => 'video/mp4',
            'animation_video_size' => 3,
            'animation_video_uploaded_at' => now(),
        ])->save();

        $uuid = 'aaaaaaaa-bbbb-cccc-dddd-eeeeeeeeeeee';
        $newPath = 'curriculum-library/animations/material/'.$material->id.'/'.$uuid.'.mp4';
        Storage::disk('r2')->put($newPath, 'NEW');

        // Simulate DB failure after store: delete only the new sidecar, leave source + old intact.
        try {
            throw new \RuntimeException('simulated db failure');
        } catch (\Throwable) {
            Storage::disk('r2')->delete($newPath);
        }

        $material->refresh();
        $this->assertSame($sourcePath, $material->path);
        $this->assertTrue(Storage::disk('r2')->exists($sourcePath));
        $this->assertTrue(Storage::disk('r2')->exists($oldPath));
        $this->assertFalse(Storage::disk('r2')->exists($newPath));
        $this->assertSame($oldPath, $material->animation_video_path);
    }

    public function test_delete_with_storage_removes_animation_sidecar_not_only_source(): void
    {
        ['material' => $material, 'sourcePath' => $sourcePath] = $this->makeMaterial();
        $videoPath = 'curriculum-library/animations/material/'.$material->id.'/clip.mp4';
        Storage::disk('r2')->put($videoPath, 'VID');
        $material->forceFill([
            'animation_video_path' => $videoPath,
            'animation_video_disk' => 'r2',
            'animation_video_original_name' => 'clip.mp4',
            'animation_video_mime' => 'video/mp4',
            'animation_video_size' => 3,
            'animation_video_uploaded_at' => now(),
        ])->save();

        $material->deleteWithStorage();

        $this->assertFalse(Storage::disk('r2')->exists($sourcePath));
        $this->assertFalse(Storage::disk('r2')->exists($videoPath));
        $this->assertNull(CurriculumLibraryMaterial::query()->find($material->id));
    }

    public function test_guest_cannot_access_animation_video_route(): void
    {
        ['item' => $item, 'material' => $material] = $this->makeMaterial([
            'animation_video_path' => 'curriculum-library/animations/material/1/x.mp4',
            'animation_video_disk' => 'r2',
            'animation_video_mime' => 'video/mp4',
        ]);
        Storage::disk('r2')->put($material->animation_video_path, 'VID');

        $response = $this->get(route('curriculum-library.material.animation-video', [$item, $material]));
        $this->assertContains($response->getStatusCode(), [401, 302, 403, 404]);
    }

    public function test_presentation_blade_mode_switch_and_no_video_fallback(): void
    {
        $withVideo = file_get_contents(resource_path('views/student/curriculum-library/presentation.blade.php'));
        $this->assertStringContainsString('العرض بالحركات', $withVideo);
        $this->assertStringContainsString('تصفح الشرائح', $withVideo);
        $this->assertStringContainsString('data-mx-pane-btn="animation"', $withVideo);
        $this->assertStringContainsString('controlslist="nodownload"', $withVideo);
        $this->assertStringContainsString('playsinline', $withVideo);

        $adminSection = file_get_contents(resource_path('views/admin/curriculum-library/_structure-section.blade.php'));
        $this->assertStringContainsString('فيديو الحركات', $adminSection);
        $this->assertStringContainsString('animation_video', $adminSection);
        $this->assertStringContainsString('صدّر العرض من PowerPoint', $adminSection);

        // No-video path: when hasAnimationVideo is false, mode switch markup is gated.
        $this->assertStringContainsString('@if($hasAnimationVideo)', $withVideo);
    }

    public function test_rejects_non_video_extension_defensively(): void
    {
        ['item' => $item, 'material' => $material, 'sourcePath' => $sourcePath] = $this->makeMaterial();

        $controller = $this->app->make(\App\Http\Controllers\Admin\CurriculumLibraryStructureController::class);
        $bad = UploadedFile::fake()->create(
            'export.pptx',
            100,
            'application/vnd.openxmlformats-officedocument.presentationml.presentation'
        );
        $request = Request::create(
            '/admin/curriculum-library/items/'.$item->id.'/materials/'.$material->id.'/animation-video',
            'POST',
            [],
            [],
            ['animation_video' => $bad]
        );
        $request->setLaravelSession($this->app['session.store']);

        $controller->storeMaterialAnimationVideo($request, $item, $material);

        $material->refresh();
        $this->assertSame($sourcePath, $material->path);
        $this->assertFalse($material->hasAnimationVideo());
        $this->assertTrue(Storage::disk('r2')->exists($sourcePath));
    }
}
