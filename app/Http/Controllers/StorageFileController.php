<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

/**
 * تقديم ملفات /media و /storage — محلي أولاً إن وُجد، ثم R2/CDN.
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
                'mp4' => 'video/mp4',
                'webm' => 'video/webm',
                'mov' => 'video/quicktime',
                'm4v' => 'video/mp4',
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

        // 1) الملف المحلي أولاً — عبر قرص public (يشمل Storage::fake في الاختبارات)
        $localResponse = $this->serveLocalPublicFile($path, $mimeFromExtension, $headersFor);
        if ($localResponse !== null) {
            return $localResponse;
        }

        // 2) السحابة — نمرّر الملف عبر Laravel. التحويل إلى رابط R2 العام يفشل بـ 404 عندما يكون الـ bucket خاصاً.
        foreach (['r2', 's3'] as $cloudDisk) {
            try {
                $disk = Storage::disk($cloudDisk);
                if (! $disk->exists($path)) {
                    continue;
                }

                $mimeType = $mimeFromExtension($path);
                $headers = $headersFor($mimeType, $path);

                $stream = $disk->readStream($path);
                if (is_resource($stream)) {
                    return response()->stream(function () use ($stream): void {
                        fpassthru($stream);
                        if (is_resource($stream)) {
                            fclose($stream);
                        }
                    }, 200, $headers);
                }

                return response($disk->get($path), 200, $headers);
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

    /**
     * @param  callable(string): string  $mimeFromExtension
     * @param  callable(string, ?string): array<string, string>  $headersFor
     */
    private function serveLocalPublicFile(string $path, callable $mimeFromExtension, callable $headersFor): ?Response
    {
        try {
            $disk = Storage::disk('public');
            if ($disk->exists($path)) {
                $mimeType = $mimeFromExtension($path);
                $headers = $headersFor($mimeType, $path);
                $stream = $disk->readStream($path);
                if (is_resource($stream)) {
                    return response()->stream(function () use ($stream): void {
                        fpassthru($stream);
                        if (is_resource($stream)) {
                            fclose($stream);
                        }
                    }, 200, $headers);
                }

                return response($disk->get($path), 200, $headers);
            }
        } catch (\Throwable) {
        }

        $basePath = storage_path('app/public');
        $filePath = $basePath.DIRECTORY_SEPARATOR.str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);

        if (! @file_exists($filePath) || ! @is_file($filePath)) {
            return null;
        }

        $realPath = @realpath($filePath) ?: $filePath;
        $allowedPath = @realpath($basePath) ?: $basePath;
        $normalizedRealPath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $realPath);
        $normalizedAllowedPath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $allowedPath);

        if ($allowedPath === '' || ! str_starts_with($normalizedRealPath, $normalizedAllowedPath) || ! @is_readable($realPath)) {
            return null;
        }

        $mimeType = @mime_content_type($realPath) ?: $mimeFromExtension($realPath);

        return response()->file($realPath, $headersFor($mimeType, $realPath));
    }
}
