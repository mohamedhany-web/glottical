<?php

namespace App\Services;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LibraryVideoUploadService
{
    public static function uploadDiskName(): string
    {
        if (config('filesystems.disks.r2.key') || env('AWS_ACCESS_KEY_ID')) {
            return 'r2';
        }

        return 'live_recordings_r2';
    }

    public static function presign(int $userId, ?string $contentType, ?string $filename): JsonResponse
    {
        @set_time_limit(120);

        $diskName = self::uploadDiskName();
        $disk = Storage::disk($diskName);
        if (! $disk->providesTemporaryUploadUrls()) {
            return response()->json([
                'direct_upload' => false,
                'message' => 'التخزين الحالي لا يدعم الرفع المباشر إلى Cloudflare. فعّل قرص R2 في الإعدادات.',
            ], 503);
        }

        $mime = self::normalizeVideoMime((string) ($contentType ?? 'video/mp4'));
        $ext = self::mimeToExt($mime, (string) ($filename ?? ''));
        $directory = 'library-videos/'.now()->format('Y/m');
        $fileName = sprintf('lib-%s-%s.%s', now()->format('Ymd-His'), Str::lower(Str::random(10)), $ext);
        $path = $directory.'/'.$fileName;

        $token = Str::random(64);
        Cache::put(
            'library_video_presign:'.$token,
            [
                'path' => $path,
                'user_id' => $userId,
                'mime' => $mime,
                'disk' => $diskName,
            ],
            now()->addMinutes(120)
        );

        try {
            $signed = $disk->temporaryUploadUrl(
                $path,
                now()->addMinutes(100),
                ['ContentType' => $mime]
            );
        } catch (\Throwable $e) {
            Cache::forget('library_video_presign:'.$token);
            Log::error('Library video presign failed', ['error' => $e->getMessage()]);

            return response()->json([
                'direct_upload' => false,
                'message' => 'تعذر تجهيز رابط الرفع إلى Cloudflare. تحقق من إعدادات R2 وCORS.',
            ], 503);
        }

        return response()->json([
            'direct_upload' => true,
            'upload_url' => $signed['url'],
            'upload_token' => $token,
            'content_type' => $mime,
            'headers' => $signed['headers'] ?? [],
            'disk' => $diskName,
        ]);
    }

    public static function complete(int $userId, string $uploadToken, ?int $durationSeconds = null): JsonResponse
    {
        @set_time_limit(120);

        $cacheKey = 'library_video_presign:'.$uploadToken;
        $payload = Cache::pull($cacheKey);
        if (! is_array($payload) || (int) ($payload['user_id'] ?? 0) !== $userId) {
            return response()->json(['message' => 'انتهت صلاحية رابط الرفع أو أنه غير صالح.'], 422);
        }

        $path = (string) ($payload['path'] ?? '');
        $mime = (string) ($payload['mime'] ?? 'video/mp4');
        $diskName = (string) ($payload['disk'] ?? self::uploadDiskName());
        if ($path === '' || str_contains($path, '..')) {
            return response()->json(['message' => 'مسار التخزين غير صالح.'], 422);
        }

        $disk = Storage::disk($diskName);
        if (! $disk->exists($path)) {
            return response()->json([
                'message' => 'الملف غير ظاهر على Cloudflare بعد. انتظر لحظة ثم أكّد مرة أخرى.',
            ], 422);
        }

        $size = (int) $disk->size($path);
        if ($size <= 0) {
            return response()->json(['message' => 'الملف المرفوع فارغ.'], 422);
        }

        return response()->json([
            'ok' => true,
            'file_path' => $path,
            'storage_disk' => $diskName,
            'file_size' => $size,
            'mime_type' => $mime,
            'duration_seconds' => (int) ($durationSeconds ?? 0),
            'file_size_human' => self::humanBytes($size),
        ]);
    }

    public static function deleteStored(?string $path, ?string $diskName = null): void
    {
        if (! $path) {
            return;
        }
        try {
            Storage::disk($diskName ?: self::uploadDiskName())->delete($path);
        } catch (\Throwable $e) {
            // ignore
        }
    }

    private static function normalizeVideoMime(string $mime): string
    {
        $mime = strtolower(trim(explode(';', $mime)[0]));

        return match ($mime) {
            'video/mp4', 'video/webm', 'video/quicktime', 'video/x-msvideo', 'video/x-matroska', 'application/octet-stream' => $mime === 'application/octet-stream' ? 'video/mp4' : $mime,
            default => 'video/mp4',
        };
    }

    private static function mimeToExt(string $mime, string $filename = ''): string
    {
        $fromName = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if (in_array($fromName, ['mp4', 'webm', 'mov', 'avi', 'mkv', 'm4v'], true)) {
            return $fromName;
        }

        return match ($mime) {
            'video/webm' => 'webm',
            'video/quicktime' => 'mov',
            'video/x-matroska' => 'mkv',
            'video/x-msvideo' => 'avi',
            default => 'mp4',
        };
    }

    private static function humanBytes(int $bytes): string
    {
        if ($bytes < 1048576) {
            return round($bytes / 1024, 1).' KB';
        }
        if ($bytes < 1073741824) {
            return round($bytes / 1048576, 1).' MB';
        }

        return round($bytes / 1073741824, 2).' GB';
    }
}
