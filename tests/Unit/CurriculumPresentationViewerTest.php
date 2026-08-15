<?php

namespace Tests\Unit;

use App\Http\Middleware\SecurityHeadersMiddleware;
use App\Models\CurriculumLibraryItem;
use App\Models\CurriculumLibraryItemFile;
use App\Models\CurriculumLibraryMaterial;
use App\Models\CurriculumPresentationDerivative;
use App\Services\CurriculumPresentationViewerService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class CurriculumPresentationViewerTest extends TestCase
{
    protected CurriculumPresentationViewerService $viewer;

    protected function setUp(): void
    {
        parent::setUp();

        Config::set('filesystems.disks.r2', [
            'driver' => 'local',
            'root' => storage_path('framework/testing/r2-viewer'),
            'url' => 'https://cdn.example.com',
        ]);
        Config::set('app.url', 'https://app.muallimx.test');
        Config::set('app.debug', false);
        putenv('DISABLE_CSP=false');
        $_ENV['DISABLE_CSP'] = 'false';
        $_SERVER['DISABLE_CSP'] = 'false';

        // Rebuild disk after config change.
        Storage::forgetDisk('r2');
        if (! is_dir(storage_path('framework/testing/r2-viewer'))) {
            mkdir(storage_path('framework/testing/r2-viewer'), 0777, true);
        }

        Storage::disk('r2')->buildTemporaryUrlsUsing(function (string $path, $expiration) {
            return 'https://cdn.example.com/'.ltrim($path, '/').'?e='.$expiration->getTimestamp();
        });

        $this->ensureTables();
        $this->viewer = $this->app->make(CurriculumPresentationViewerService::class);
    }

    protected function ensureTables(): void
    {
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

        if (! Schema::hasTable('curriculum_library_item_files')) {
            Schema::create('curriculum_library_item_files', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('curriculum_library_item_id');
                $table->string('path');
                $table->string('storage_disk', 32)->nullable();
                $table->string('label')->nullable();
                $table->string('file_type', 32)->default('presentation');
                $table->unsignedInteger('order')->default(0);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('curriculum_presentation_derivatives')) {
            Schema::create('curriculum_presentation_derivatives', function (Blueprint $table) {
                $table->id();
                $table->string('source_type', 32);
                $table->unsignedBigInteger('source_id');
                $table->string('storage_disk', 32)->default('r2');
                $table->string('manifest_path')->nullable();
                $table->string('status', 32)->default('pending');
                $table->unsignedInteger('slide_count')->nullable();
                $table->unsignedInteger('width')->nullable();
                $table->unsignedInteger('height')->nullable();
                $table->unsignedInteger('version')->default(1);
                $table->string('source_checksum', 128)->nullable();
                $table->text('error_message')->nullable();
                $table->string('engine', 64)->nullable();
                $table->timestamp('ready_at')->nullable();
                $table->timestamps();
                $table->unique(['source_type', 'source_id'], 'cpd_source_unique_viewer_test');
            });
        }
    }

    protected function makeItem(): CurriculumLibraryItem
    {
        return CurriculumLibraryItem::query()->create([
            'title' => 'Test Curriculum',
            'slug' => 'test-curriculum-'.uniqid(),
            'is_active' => true,
        ]);
    }

    protected function makeMaterial(CurriculumLibraryItem $item, array $overrides = []): CurriculumLibraryMaterial
    {
        $sectionId = DB::table('curriculum_library_sections')->insertGetId([
            'curriculum_library_item_id' => $item->id,
            'title' => 'Section',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $path = $overrides['path'] ?? 'curriculum-library/materials/'.$item->id.'/demo.pptx';
        $disk = $overrides['storage_disk'] ?? 'r2';
        Storage::disk($disk)->put($path, 'FAKE-PPTX-ORIGINAL-BYTES');

        return CurriculumLibraryMaterial::query()->create(array_merge([
            'curriculum_library_section_id' => $sectionId,
            'title' => 'Demo deck',
            'path' => $path,
            'storage_disk' => $disk,
            'original_name' => 'demo.pptx',
            'file_kind' => 'pptx',
            'view_in_platform' => true,
            'allow_download' => false,
            'order' => 1,
            'is_active' => true,
        ], $overrides));
    }

    /**
     * @return array{0: CurriculumPresentationDerivative, 1: array<string, mixed>}
     */
    protected function seedReadyDerivative(CurriculumLibraryMaterial $material, int $slides = 2): array
    {
        $version = 1;
        $prefix = sprintf(
            'curriculum-library/derivatives/material/%d/v%d',
            (int) $material->id,
            $version
        );
        $disk = Storage::disk($material->storage_disk ?: 'r2');

        $png1x1 = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==');
        $slidesMeta = [];
        for ($i = 1; $i <= $slides; $i++) {
            $slideKey = $prefix.'/slides/slide-'.str_pad((string) $i, 3, '0', STR_PAD_LEFT).'.png';
            $thumbKey = $prefix.'/thumbs/thumb-'.str_pad((string) $i, 3, '0', STR_PAD_LEFT).'.png';
            $disk->put($slideKey, $png1x1);
            $disk->put($thumbKey, $png1x1);
            $slidesMeta[] = [
                'index' => $i,
                'path' => $slideKey,
                'thumb' => $thumbKey,
            ];
        }

        $manifest = [
            'source_type' => CurriculumPresentationDerivative::SOURCE_MATERIAL,
            'source_id' => (int) $material->id,
            'version' => $version,
            'slide_count' => $slides,
            'width' => 1280,
            'height' => 720,
            'format' => 'png',
            'dpi' => 144,
            'engine' => 'libreoffice+pdftoppm',
            'source_checksum' => 'abc123',
            'source_path_unchanged' => $material->path,
            'slides' => $slidesMeta,
            'generated_at' => now()->toIso8601String(),
        ];

        $manifestPath = $prefix.'/manifest.json';
        $disk->put($manifestPath, json_encode($manifest, JSON_UNESCAPED_UNICODE));

        $derivative = CurriculumPresentationDerivative::query()->create([
            'source_type' => CurriculumPresentationDerivative::SOURCE_MATERIAL,
            'source_id' => $material->id,
            'storage_disk' => $material->storage_disk ?: 'r2',
            'manifest_path' => $manifestPath,
            'status' => CurriculumPresentationDerivative::STATUS_READY,
            'slide_count' => $slides,
            'width' => 1280,
            'height' => 720,
            'version' => $version,
            'source_checksum' => 'abc123',
            'engine' => 'libreoffice+pdftoppm',
            'ready_at' => now(),
        ]);

        return [$derivative, $manifest];
    }

    public function test_slide_routes_are_registered(): void
    {
        $this->assertTrue(Route::has('curriculum-library.material.slides.manifest'));
        $this->assertTrue(Route::has('curriculum-library.material.slides.image'));
        $this->assertTrue(Route::has('curriculum-library.material.slides.thumb'));
        $this->assertTrue(Route::has('curriculum-library.file.slides.manifest'));
        $this->assertTrue(Route::has('curriculum-library.file.slides.image'));
        $this->assertTrue(Route::has('curriculum-library.file.slides.thumb'));
        $this->assertTrue(Route::has('curriculum-library.material.presentation'));
        $this->assertTrue(Route::has('curriculum-library.file.presentation'));
    }

    public function test_ready_derivative_selects_native_player(): void
    {
        $item = $this->makeItem();
        $material = $this->makeMaterial($item);
        $this->seedReadyDerivative($material, 2);

        $payload = $this->viewer->playerPayloadForMaterial($item, $material);

        $this->assertSame('native', $payload['mode']);
        $this->assertNotEmpty($payload['manifestUrl']);
        $this->assertSame(2, $payload['slideCount']);
        $this->assertSame(1280, $payload['width']);
        $this->assertSame(720, $payload['height']);
        $this->assertStringContainsString('/slides/manifest', $payload['manifestUrl']);
        // Original PPTX bytes must remain untouched.
        $this->assertSame(
            'FAKE-PPTX-ORIGINAL-BYTES',
            Storage::disk('r2')->get($material->path)
        );
    }

    public function test_missing_derivative_retains_office_payload(): void
    {
        $item = $this->makeItem();
        $material = $this->makeMaterial($item, [
            'path' => 'curriculum-library/materials/x/office-only.pptx',
        ]);

        $payload = $this->viewer->playerPayloadForMaterial($item, $material);

        $this->assertSame('office', $payload['mode']);
        $this->assertNull($payload['manifestUrl']);
        $this->assertNull($payload['slideCount']);
        $this->assertNotEmpty($payload['publicUrl']);
        $this->assertTrue($payload['canUseOfficeViewer']);
        $this->assertNotNull($payload['embedUrl']);
        $this->assertStringContainsString('view.officeapps.live.com', $payload['embedUrl']);
    }

    public function test_failed_derivative_falls_back_to_office(): void
    {
        $item = $this->makeItem();
        $material = $this->makeMaterial($item);

        CurriculumPresentationDerivative::query()->create([
            'source_type' => CurriculumPresentationDerivative::SOURCE_MATERIAL,
            'source_id' => $material->id,
            'storage_disk' => 'r2',
            'manifest_path' => null,
            'status' => CurriculumPresentationDerivative::STATUS_FAILED,
            'version' => 1,
            'error_message' => 'boom',
        ]);

        $payload = $this->viewer->playerPayloadForMaterial($item, $material);
        $this->assertSame('office', $payload['mode']);
        $this->assertNull($payload['manifestUrl']);
    }

    public function test_legacy_file_without_derivative_uses_office(): void
    {
        $item = $this->makeItem();
        Storage::disk('r2')->put('legacy/deck.pptx', 'LEGACY');
        $file = CurriculumLibraryItemFile::query()->create([
            'curriculum_library_item_id' => $item->id,
            'path' => 'legacy/deck.pptx',
            'storage_disk' => 'r2',
            'label' => 'Legacy PPT',
            'file_type' => 'presentation',
            'order' => 1,
        ]);

        $payload = $this->viewer->playerPayloadForFile($item, $file);
        $this->assertSame('office', $payload['mode']);
        $this->assertNull($payload['manifestUrl']);
    }

    public function test_manifest_is_sanitized_without_original_path_or_raw_keys(): void
    {
        $item = $this->makeItem();
        $material = $this->makeMaterial($item);
        [$derivative] = $this->seedReadyDerivative($material, 2);

        $validated = $this->viewer->loadAndValidateManifest($derivative);
        $this->assertNotNull($validated);

        $sanitized = $this->viewer->sanitizedManifestPayload(
            $item,
            'material',
            $material,
            $derivative,
            $validated
        );

        $json = json_encode($sanitized);
        $this->assertIsString($json);
        $this->assertStringNotContainsString($material->path, $json);
        $this->assertStringNotContainsString('source_path_unchanged', $json);
        $this->assertStringNotContainsString('source_checksum', $json);
        $this->assertStringNotContainsString('curriculum-library/derivatives/', $json);
        $this->assertStringNotContainsString('FAKE-PPTX', $json);

        $this->assertSame(2, $sanitized['slide_count']);
        $this->assertCount(2, $sanitized['slides']);
        foreach ($sanitized['slides'] as $slide) {
            $this->assertArrayHasKey('image_url', $slide);
            $this->assertArrayHasKey('thumb_url', $slide);
            $this->assertStringContainsString('/slides/', $slide['image_url']);
            $this->assertStringNotContainsString('curriculum-library/derivatives/', $slide['image_url']);
        }
    }

    public function test_stream_slide_asset_is_bounded_and_inline(): void
    {
        $item = $this->makeItem();
        $material = $this->makeMaterial($item);
        [$derivative] = $this->seedReadyDerivative($material, 2);
        $validated = $this->viewer->loadAndValidateManifest($derivative);
        $this->assertNotNull($validated);

        $response = $this->viewer->streamSlideAsset($derivative, $validated, 1, 'image');
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('image/png', $response->headers->get('Content-Type'));
        $this->assertSame('nosniff', $response->headers->get('X-Content-Type-Options'));
        $this->assertStringContainsString('private', (string) $response->headers->get('Cache-Control'));
        $this->assertStringContainsString('inline', (string) $response->headers->get('Content-Disposition'));

        try {
            $this->viewer->streamSlideAsset($derivative, $validated, 99, 'image');
            $this->fail('Expected 404 for out-of-bounds slide');
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            $this->assertSame(404, $e->getStatusCode());
        }

        try {
            $this->viewer->streamSlideAsset($derivative, $validated, 0, 'image');
            $this->fail('Expected 404 for slide 0');
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            $this->assertSame(404, $e->getStatusCode());
        }

        // Original object never deleted/overwritten by streaming.
        $this->assertTrue(Storage::disk('r2')->exists($material->path));
        $this->assertSame('FAKE-PPTX-ORIGINAL-BYTES', Storage::disk('r2')->get($material->path));
    }

    public function test_invalid_manifest_paths_are_rejected(): void
    {
        $item = $this->makeItem();
        $material = $this->makeMaterial($item);
        $prefix = sprintf('curriculum-library/derivatives/material/%d/v1', $material->id);
        $disk = Storage::disk('r2');

        $badManifest = [
            'version' => 1,
            'slide_count' => 1,
            'format' => 'png',
            'slides' => [
                [
                    'index' => 1,
                    'path' => '../materials/escape.pptx',
                    'thumb' => null,
                ],
            ],
        ];
        $manifestPath = $prefix.'/manifest.json';
        $disk->put($manifestPath, json_encode($badManifest));

        $derivative = CurriculumPresentationDerivative::query()->create([
            'source_type' => CurriculumPresentationDerivative::SOURCE_MATERIAL,
            'source_id' => $material->id,
            'storage_disk' => 'r2',
            'manifest_path' => $manifestPath,
            'status' => CurriculumPresentationDerivative::STATUS_READY,
            'slide_count' => 1,
            'version' => 1,
            'ready_at' => now(),
        ]);

        $this->assertNull($this->viewer->loadAndValidateManifest($derivative));
        $payload = $this->viewer->playerPayloadForMaterial($item, $material);
        $this->assertSame('office', $payload['mode']);
    }

    public function test_csp_contains_office_online_frame_src(): void
    {
        Config::set('app.debug', false);
        putenv('DISABLE_CSP=false');
        $_ENV['DISABLE_CSP'] = 'false';
        $_SERVER['DISABLE_CSP'] = 'false';

        $middleware = new SecurityHeadersMiddleware;
        $request = Request::create('https://example.com/curriculum-library/demo', 'GET');
        /** @var Response $response */
        $response = $middleware->handle($request, function () {
            return new Response('ok', 200);
        });

        $csp = (string) $response->headers->get('Content-Security-Policy');
        $this->assertNotSame('', $csp);
        $this->assertStringContainsString('https://view.officeapps.live.com', $csp);
        $this->assertMatchesRegularExpression('/frame-src[^;]*https:\/\/view\.officeapps\.live\.com/', $csp);
        $this->assertMatchesRegularExpression("/media-src[^;]*'self'/", $csp);
        $this->assertMatchesRegularExpression('/media-src[^;]*https:/', $csp);
        $this->assertMatchesRegularExpression('/media-src[^;]*blob:/', $csp);
    }
}
