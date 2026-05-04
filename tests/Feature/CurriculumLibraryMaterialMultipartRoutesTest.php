<?php

namespace Tests\Feature;

use App\Services\CurriculumLibraryR2MultipartService;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class CurriculumLibraryMaterialMultipartRoutesTest extends TestCase
{
    public function test_curriculum_material_upload_routes_are_registered(): void
    {
        $names = [
            'admin.curriculum-library.items.materials.multipart-init',
            'admin.curriculum-library.items.materials.multipart-sign-part',
            'admin.curriculum-library.items.materials.multipart-complete',
            'admin.curriculum-library.items.materials.multipart-abort',
            'admin.curriculum-library.items.materials.presign-upload',
            'admin.curriculum-library.items.materials.complete-direct',
            'admin.curriculum-library.items.materials.store',
        ];
        foreach ($names as $name) {
            $this->assertTrue(Route::has($name), 'Missing route: '.$name);
        }
    }

    public function test_upload_limits_config_has_curriculum_multipart_keys(): void
    {
        $c = config('upload_limits');
        $this->assertIsArray($c);
        $this->assertArrayHasKey('curriculum_material_max_bytes', $c);
        $this->assertArrayHasKey('curriculum_r2_multipart_threshold_bytes', $c);
        $this->assertArrayHasKey('curriculum_r2_multipart_part_bytes', $c);
        $this->assertGreaterThanOrEqual(5 * 1024 * 1024, (int) $c['curriculum_r2_multipart_part_bytes']);
    }

    public function test_multipart_service_is_resolvable(): void
    {
        $svc = $this->app->make(CurriculumLibraryR2MultipartService::class);
        $this->assertInstanceOf(CurriculumLibraryR2MultipartService::class, $svc);
    }

    public function test_guest_multipart_init_is_not_successful(): void
    {
        $response = $this->postJson('/admin/curriculum-library/items/fake-slug-xyz/sections/1/materials/multipart-init', [
            'original_name' => 'test.pdf',
            'file_size' => 1024,
        ]);
        $this->assertNotEquals(200, $response->getStatusCode());
        $this->assertContains($response->getStatusCode(), [401, 302, 403, 404, 419], 'Unexpected status for guest');
    }
}
