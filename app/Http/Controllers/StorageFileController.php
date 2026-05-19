<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

/**
 * تقديم ملفات /storage/* من القرص المحلي أو Cloudflare R2.
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

        foreach (['r2', 's3'] as $cloudDisk) {
            try {
                $disk = Storage::disk($cloudDisk);
                if ($disk->exists($path)) {
                    $mimeType = $mimeFromExtension($path);
                    $contents = $disk->get($path);

                    return response($contents, 200, $headersFor($mimeType, $path));
                }
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

        if (config('app.debug')) {
            Log::warning('Storage file not found', ['requested_path' => $path, 'url' => $request->fullUrl()]);
        }

        abort(404, 'File not found');
    }
}
