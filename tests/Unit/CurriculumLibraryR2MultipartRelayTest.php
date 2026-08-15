<?php

namespace Tests\Unit;

use App\Services\CurriculumLibraryR2MultipartService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CurriculumLibraryR2MultipartRelayTest extends TestCase
{
    public function test_relay_presigned_put_returns_etag_from_response(): void
    {
        Http::fake(function (\Illuminate\Http\Client\Request $request) {
            $this->assertSame('PUT', $request->method());

            return Http::response('', 200, ['ETag' => '"part-etag-1"']);
        });

        $svc = new CurriculumLibraryR2MultipartService('r2');
        $etag = $svc->relayPresignedPut('https://r2-bucket-test.example/object?X-Amz-Signed', [], str_repeat('a', 1024));

        $this->assertSame('"part-etag-1"', $etag);
    }

    public function test_relay_presigned_put_throws_on_http_error(): void
    {
        Http::fake([
            '*' => Http::response('Denied', 403),
        ]);

        $svc = new CurriculumLibraryR2MultipartService('r2');

        $this->expectException(\RuntimeException::class);
        $svc->relayPresignedPut('https://r2-bucket-test.example/object', [], 'x');
    }

    public function test_filter_presigned_upload_headers_strips_host(): void
    {
        $filtered = CurriculumLibraryR2MultipartService::filterPresignedUploadHeadersForBrowser([
            'Host' => ['wrong.example'],
            'x-amz-checksum-crc32' => ['AAAAAA=='],
        ]);

        $this->assertArrayNotHasKey('Host', $filtered);
        $this->assertArrayHasKey('x-amz-checksum-crc32', $filtered);
    }
}
