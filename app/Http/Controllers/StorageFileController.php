<?php

namespace App\Http\Controllers;

use App\Services\PublicStorageUrl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

/**
 * تقديم ملفات /storage/* — يُفضّل إعادة التوجيه إلى R2/CDN بدل تمرير المحتوى عبر PHP.
 */
class StorageFileController extends Controller
{
    public function show(Request $request, string $path): Response
    {
        $path = rawurldecode($path);
        $path = str_replace('..', '', $path);
        $path = ltrim(str_replace('\\', '/', $path), '/');

        if ($path === '') {
            abort(404);
        }

        $mimeFromExtension = static function (string $filePath): string {
            $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

            return match ($extension) {
                'jpg', 'jpeg' => 'image/jpeg',
                'png' => 'image/png',
                'gif' => 'image/gif',
                'webp' => 'image/webp',
                'svg' => 'image/svg+xml',
                'pdf' => 'application/pdf',
                default => 'application/octet-stream',
            };
        };

        $headersFor = static function (string $mimeType, ?string $filename = null): array {
            $headers = [
                'Content-Type' => $mimeType,
                'Cache-Control' => 'public, max-age=31536000, immutable',
            ];
            if ($mimeType === 'application/pdf' && $filename) {
                $headers['Content-Disposition'] = 'inline; filename="'.basename($filename).'"';
            }

            return $headers;
        };

        foreach (['r2', 's3'] as $cloudDisk) {
            try {
                $directUrl = PublicStorageUrl::cloudDirectUrl($cloudDisk, $path);
                if ($directUrl !== null && ! PublicStorageUrl::isApplicationProxyUrl($directUrl)) {
                    return redirect()->away($directUrl, 302, [
                        'Cache-Control' => 'public, max-age=604800',
                    ]);
                }

                $signed = PublicStorageUrl::cloudSignedUrl($cloudDisk, $path);
                if ($signed !== null) {
                    return redirect()->away($signed, 302, [
                        'Cache-Control' => 'public, max-age=604800',
                    ]);
                }

                $disk = Storage::disk($cloudDisk);
                if (! $disk->exists($path)) {
                    continue;
                }

                $mimeType = $mimeFromExtension($path);

                return response($disk->get($path), 200, $headersFor($mimeType, $path));
            } catch (\Throwable $e) {
                if (config('app.debug')) {
                    Log::warning('Storage cloud read failed', [
                        'disk' => $cloudDisk,
                        'path' => $path,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }

        $basePath = storage_path('app/public');
        $filePath = $basePath.DIRECTORY_SEPARATOR.str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);

        if (@file_exists($filePath) && @is_file($filePath)) {
            $realPath = @realpath($filePath) ?: $filePath;
            $allowedPath = @realpath($basePath) ?: $basePath;
            $normalizedRealPath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $realPath);
            $normalizedAllowedPath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $allowedPath);

            if ($allowedPath !== '' && str_starts_with($normalizedRealPath, $normalizedAllowedPath) && @is_readable($realPath)) {
                $mimeType = @mime_content_type($realPath) ?: $mimeFromExtension($realPath);

                return response()->file($realPath, $headersFor($mimeType, $realPath));
            }
        }

        if (config('app.debug')) {
            Log::warning('Storage file not found', ['requested_path' => $path, 'url' => $request->fullUrl()]);
        }

        abort(404, 'File not found');
    }
}
