<?php

namespace Tests\Unit;

use App\Http\Controllers\Admin\CurriculumLibraryStructureController;
use App\Jobs\ConvertCurriculumPresentationJob;
use App\Models\CurriculumLibraryItem;
use App\Models\CurriculumLibraryMaterial;
use App\Models\CurriculumLibrarySection;
use App\Models\CurriculumPresentationDerivative;
use App\Services\CurriculumPresentationConversionService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CurriculumPresentationConversionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Config::set('curriculum_presentation.enabled', true);
        Config::set('curriculum_presentation.queue', 'default');
        Config::set('curriculum_presentation.soffice_path', '/nonexistent/soffice-binary');
        Config::set('curriculum_presentation.pdftoppm_path', '/nonexistent/pdftoppm-binary');
        Config::set('curriculum_presentation.temp_disk', 'local');
        Config::set('curriculum_presentation.temp_prefix', 'curriculum-presentation-tmp-test');
        Config::set('filesystems.disks.r2', [
            'driver' => 'local',
            'root' => storage_path('framework/testing/r2-curriculum'),
        ]);

        Storage::fake('local');

        $this->ensureTables();
    }

    protected function ensureTables(): void
    {
        if (! Schema::hasTable('curriculum_library_items')) {
            Schema::create('curriculum_library_items', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('slug')->unique();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('curriculum_library_sections')) {
            Schema::create('curriculum_library_sections', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('curriculum_library_item_id');
                $table->unsignedBigInteger('parent_id')->nullable();
                $table->string('title');
                $table->unsignedInteger('order')->default(0);
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
                $table->unique(['source_type', 'source_id'], 'cpd_source_unique_test');
            });
        }
    }

    protected function makeMaterial(array $overrides = []): CurriculumLibraryMaterial
    {
        $path = $overrides['path'] ?? 'curriculum-library/materials/1/demo.pptx';
        $disk = $overrides['storage_disk'] ?? 'r2';

        Storage::disk($disk)->put($path, 'FAKE-PPTX-BYTES-DO-NOT-MUTATE');

        return CurriculumLibraryMaterial::query()->create(array_merge([
            'curriculum_library_section_id' => 1,
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
     * @return array{0: CurriculumLibraryItem, 1: CurriculumLibraryMaterial}
     */
    protected function makeOwnedMaterial(array $overrides = []): array
    {
        $item = CurriculumLibraryItem::query()->create([
            'title' => 'Admin conversion test',
            'slug' => 'admin-conversion-'.uniqid(),
        ]);
        $section = CurriculumLibrarySection::query()->create([
            'curriculum_library_item_id' => $item->id,
            'title' => 'Section',
            'order' => 1,
            'is_active' => true,
        ]);

        return [$item, $this->makeMaterial(array_merge([
            'curriculum_library_section_id' => $section->id,
        ], $overrides))];
    }

    public function test_config_file_exposes_expected_keys(): void
    {
        $c = config('curriculum_presentation');
        $this->assertIsArray($c);
        foreach ([
            'enabled', 'queue', 'soffice_path', 'pdftoppm_path', 'timeout_seconds',
            'image_format', 'image_quality', 'dpi', 'temp_disk', 'temp_prefix',
        ] as $key) {
            $this->assertArrayHasKey($key, $c, 'Missing config key: '.$key);
        }
    }

    public function test_retry_conversion_route_is_registered(): void
    {
        $this->assertTrue(Route::has('admin.curriculum-library.items.materials.retry-conversion'));
        $this->assertTrue(Route::has('admin.curriculum-library.items.materials.conversion-status'));
    }

    public function test_status_response_is_sanitized_and_contains_no_storage_details(): void
    {
        [$item, $material] = $this->makeOwnedMaterial();
        CurriculumPresentationDerivative::query()->create([
            'source_type' => CurriculumPresentationDerivative::SOURCE_MATERIAL,
            'source_id' => $material->id,
            'storage_disk' => 'r2',
            'manifest_path' => 'curriculum-library/derivatives/material/99/v1/manifest.json',
            'status' => CurriculumPresentationDerivative::STATUS_FAILED,
            'error_message' => 'Failed at /var/private/source.pptx; see https://storage.example/secret?token=abc '.str_repeat('x', 300),
            'version' => 1,
        ]);

        $controller = $this->app->make(CurriculumLibraryStructureController::class);
        $payload = $controller->materialPresentationConversionStatus($item, $material)->getData(true);
        $encoded = json_encode($payload);

        $this->assertSame(['status', 'slide_count', 'error_summary'], array_keys($payload));
        $this->assertSame('failed', $payload['status']);
        $this->assertNull($payload['slide_count']);
        $this->assertLessThanOrEqual(181, mb_strlen($payload['error_summary']));
        $this->assertStringNotContainsString('/var/private', $encoded);
        $this->assertStringNotContainsString('storage.example', $encoded);
        $this->assertStringNotContainsString('manifest_path', $encoded);
        $this->assertStringNotContainsString('storage_disk', $encoded);
        $this->assertStringNotContainsString($material->path, $encoded);
    }

    public function test_status_rejects_material_from_another_curriculum_item(): void
    {
        [, $material] = $this->makeOwnedMaterial();
        $otherItem = CurriculumLibraryItem::query()->create([
            'title' => 'Other item',
            'slug' => 'other-item-'.uniqid(),
        ]);

        $this->expectException(\Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class);

        $this->app->make(CurriculumLibraryStructureController::class)
            ->materialPresentationConversionStatus($otherItem, $material);
    }

    public function test_retry_does_not_dispatch_duplicate_for_pending_or_processing_status(): void
    {
        foreach ([
            CurriculumPresentationDerivative::STATUS_PENDING,
            CurriculumPresentationDerivative::STATUS_PROCESSING,
        ] as $status) {
            Bus::fake([ConvertCurriculumPresentationJob::class]);
            [$item, $material] = $this->makeOwnedMaterial();
            CurriculumPresentationDerivative::query()->create([
                'source_type' => CurriculumPresentationDerivative::SOURCE_MATERIAL,
                'source_id' => $material->id,
                'storage_disk' => 'r2',
                'status' => $status,
                'version' => 1,
            ]);

            $controller = $this->app->make(CurriculumLibraryStructureController::class);
            $response = $controller->retryMaterialPresentationConversion($item, $material);

            $this->assertTrue($response->isRedirect());
            Bus::assertNothingDispatched();
        }
    }

    public function test_material_without_derivative_still_reports_not_ready(): void
    {
        $material = $this->makeMaterial();
        $this->assertFalse($material->hasReadyPresentationDerivative());
        $this->assertSame(
            'curriculum-library/materials/1/demo.pptx',
            $material->fresh()->path
        );
        $this->assertTrue(Storage::disk('r2')->exists($material->path));
    }

    public function test_queue_dispatch_is_additive_and_does_not_touch_source_path(): void
    {
        Bus::fake([ConvertCurriculumPresentationJob::class]);

        $material = $this->makeMaterial([
            'path' => 'curriculum-library/materials/9/original-untouched.pptx',
        ]);
        $originalPath = $material->path;
        $originalBytes = Storage::disk('r2')->get($originalPath);

        $service = $this->app->make(CurriculumPresentationConversionService::class);
        $derivative = $service->queueMaterialIfEligible($material);

        $this->assertNotNull($derivative);
        $this->assertSame(CurriculumPresentationDerivative::STATUS_PENDING, $derivative->status);
        $this->assertSame($originalPath, $material->fresh()->path);
        $this->assertSame($originalBytes, Storage::disk('r2')->get($originalPath));

        Bus::assertDispatched(ConvertCurriculumPresentationJob::class, function (ConvertCurriculumPresentationJob $job) use ($material) {
            return $job->sourceType === CurriculumPresentationDerivative::SOURCE_MATERIAL
                && $job->sourceId === (int) $material->id;
        });
    }

    public function test_unavailable_binaries_leave_original_path_and_object_untouched(): void
    {
        $material = $this->makeMaterial([
            'path' => 'curriculum-library/materials/2/keep-me.pptx',
        ]);
        $originalPath = $material->path;
        $originalBytes = Storage::disk('r2')->get($originalPath);

        /** @var CurriculumPresentationConversionService $service */
        $service = $this->app->make(CurriculumPresentationConversionService::class);
        $derivative = $service->convertMaterial($material);

        $this->assertSame(CurriculumPresentationDerivative::STATUS_UNAVAILABLE, $derivative->status);
        $this->assertNotNull($derivative->error_message);

        $material->refresh();
        $this->assertSame($originalPath, $material->path);
        $this->assertSame('r2', $material->storage_disk);
        $this->assertTrue(Storage::disk('r2')->exists($originalPath));
        $this->assertSame($originalBytes, Storage::disk('r2')->get($originalPath));

        // No derived objects should claim the original key.
        $this->assertFalse(Storage::disk('r2')->exists(
            'curriculum-library/derivatives/material/'.$material->id.'/v1/manifest.json'
        ));
    }

    public function test_conversion_failure_cleans_derived_prefix_only_and_keeps_source(): void
    {
        $material = $this->makeMaterial([
            'path' => 'curriculum-library/materials/3/source-stays.pptx',
        ]);
        $originalPath = $material->path;
        $originalBytes = Storage::disk('r2')->get($originalPath);

        $service = new class extends CurriculumPresentationConversionService
        {
            public function detectBinaries(): array
            {
                return [
                    'soffice' => '/usr/bin/true',
                    'pdftoppm' => '/usr/bin/true',
                    'ready' => true,
                    'missing' => [],
                ];
            }

            protected function convertOfficeToPdf(string $soffice, string $localSource, string $tempRoot): string
            {
                throw new \RuntimeException('Simulated LibreOffice failure');
            }
        };

        $this->app->instance(CurriculumPresentationConversionService::class, $service);

        try {
            $service->convertMaterial($material);
            $this->fail('Expected conversion failure');
        } catch (\RuntimeException $e) {
            $this->assertStringContainsString('Simulated LibreOffice failure', $e->getMessage());
        }

        $material->refresh();
        $this->assertSame($originalPath, $material->path);
        $this->assertSame($originalBytes, Storage::disk('r2')->get($originalPath));

        $derivative = CurriculumPresentationDerivative::query()
            ->where('source_type', 'material')
            ->where('source_id', $material->id)
            ->first();

        $this->assertNotNull($derivative);
        $this->assertSame(CurriculumPresentationDerivative::STATUS_FAILED, $derivative->status);
        $this->assertFalse(Storage::disk('r2')->exists(
            'curriculum-library/derivatives/material/'.$material->id.'/v1/manifest.json'
        ));
    }

    public function test_queue_skipped_when_derivatives_table_missing_does_not_break(): void
    {
        Bus::fake();
        Schema::dropIfExists('curriculum_presentation_derivatives');

        $material = CurriculumLibraryMaterial::query()->create([
            'curriculum_library_section_id' => 1,
            'title' => 'No table',
            'path' => 'curriculum-library/materials/4/ok.pptx',
            'storage_disk' => 'r2',
            'original_name' => 'ok.pptx',
            'file_kind' => 'pptx',
            'view_in_platform' => true,
            'allow_download' => false,
            'order' => 1,
            'is_active' => true,
        ]);
        Storage::disk('r2')->put($material->path, 'bytes');

        $service = $this->app->make(CurriculumPresentationConversionService::class);
        $this->assertNull($service->queueMaterialIfEligible($material));
        Bus::assertNothingDispatched();
        $this->assertSame('curriculum-library/materials/4/ok.pptx', $material->fresh()->path);

        // Recreate for other tests in same process if any.
        $this->ensureTables();
    }

    public function test_non_pptx_materials_are_not_queued(): void
    {
        Bus::fake();
        $material = $this->makeMaterial([
            'file_kind' => 'pdf',
            'original_name' => 'doc.pdf',
            'path' => 'curriculum-library/materials/5/doc.pdf',
        ]);

        $service = $this->app->make(CurriculumPresentationConversionService::class);
        $this->assertNull($service->queueMaterialIfEligible($material));
        Bus::assertNothingDispatched();
    }

    public function test_artisan_command_is_registered(): void
    {
        $this->artisan('curriculum:convert-presentations', ['--help' => true])
            ->assertExitCode(0);
    }
}
