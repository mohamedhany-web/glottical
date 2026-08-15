<?php

namespace Tests\Unit;

use App\Http\Middleware\FileUploadSecurityMiddleware;
use App\Services\SecurityService;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class FileUploadSecurityMiddlewareTest extends TestCase
{
    public function test_id_document_allows_image_uploads(): void
    {
        $middleware = new FileUploadSecurityMiddleware(new SecurityService);
        $file = UploadedFile::fake()->image('id.jpg');

        $request = Request::create('/tutor/apply/profile', 'POST', [], [], [
            'id_document' => $file,
            'photo' => UploadedFile::fake()->image('photo.jpg'),
            'certificate' => UploadedFile::fake()->image('cert.jpg'),
        ]);

        $called = false;
        $response = $middleware->handle($request, function () use (&$called) {
            $called = true;

            return response('ok');
        });

        $this->assertTrue($called);
        $this->assertSame('ok', $response->getContent());
    }

    public function test_generic_file_field_allows_library_videos(): void
    {
        $middleware = new FileUploadSecurityMiddleware(new SecurityService);
        $mimes = new \ReflectionMethod($middleware, 'getAllowedMimes');
        $allowed = $mimes->invoke($middleware, 'file');
        $this->assertContains('video/mp4', $allowed);
        $this->assertContains('mp4', $allowed);

        $size = new \ReflectionMethod($middleware, 'getMaxSize');
        $max = $size->invoke($middleware, Request::create('/admin/libraries/videos/proxy-upload', 'POST'), 'file');
        $this->assertGreaterThanOrEqual(200 * 1024 * 1024, $max);
    }
}
