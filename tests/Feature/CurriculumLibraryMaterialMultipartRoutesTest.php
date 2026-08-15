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
            'admin.curriculum-library.items.materials.multipart-proxy-part',
            'admin.curriculum-library.items.materials.presign-upload',
            'admin.curriculum-library.items.materials.complete-direct',
            'admin.curriculum-library.items.materials.store',
            'admin.curriculum-library.items.materials.conversion-status',
            'admin.curriculum-library.items.materials.retry-conversion',
            'admin.curriculum-library.items.materials.animation-video.store',
            'admin.curriculum-library.items.materials.animation-video.destroy',
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
        $this->assertArrayHasKey('curriculum_r2_multipart_browser_first', $c);
        $this->assertGreaterThanOrEqual(5 * 1024 * 1024, (int) $c['curriculum_r2_multipart_part_bytes']);
    }

    public function test_multipart_service_is_resolvable(): void
    {
        $svc = $this->app->make(CurriculumLibraryR2MultipartService::class);
        $this->assertInstanceOf(CurriculumLibraryR2MultipartService::class, $svc);
    }

    public function test_presentation_conversion_config_is_loaded(): void
    {
        $c = config('curriculum_presentation');
        $this->assertIsArray($c);
        $this->assertArrayHasKey('enabled', $c);
        $this->assertArrayHasKey('soffice_path', $c);
        $this->assertArrayHasKey('pdftoppm_path', $c);
        $this->assertArrayHasKey('animation_video_max_bytes', $c);
        $this->assertArrayHasKey('animation_video_allowed_extensions', $c);
    }

    public function test_admin_structure_contains_conversion_labels_and_five_second_polling(): void
    {
        $structure = file_get_contents(resource_path('views/admin/curriculum-library/structure.blade.php'));
        $section = file_get_contents(resource_path('views/admin/curriculum-library/_structure-section.blade.php'));
        $source = $structure."\n".$section;

        foreach ([
            'غير مجدول',
            'قيد الانتظار',
            'جاري التحويل',
            'جاهز',
            'فشل التحويل',
            'غير متاح',
            'يحتاج إعادة بناء',
            'إعادة المحاولة',
            'إعادة بناء',
            'فيديو الحركات',
            'العرض بالحركات',
        ] as $label) {
            // "العرض بالحركات" is on student presentation; admin has "فيديو الحركات"
            if ($label === 'العرض بالحركات') {
                continue;
            }
            $this->assertStringContainsString($label, $source);
        }

        $this->assertStringContainsString('data-conversion-row', $source);
        $this->assertStringContainsString('pollActiveConversions', $structure);
        $this->assertStringContainsString('5000', $structure);
        $this->assertStringContainsString('animation-video', $section);
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
