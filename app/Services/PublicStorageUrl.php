<?php

namespace App\Services;

use App\Support\ApplicationUrl;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * روابط ملفات التخزين العام — محلي (/storage/...) أو Cloudflare R2.
 */
class PublicStorageUrl
{
    /** @var array<int, string> */
    private const CLOUD_DISKS = ['r2', 's3'];

    public static function fromPath(?string $path, ?string $preferredDisk = null): ?string
    {
        if (! is_string($path) || trim($path) === '') {
            return null;
        }

        $path = str_replace('\\', '/', ltrim($path, '/'));

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return self::ensureHttpsInProduction($path);
        }

        foreach (PublicMediaStorage::disksToProbe($preferredDisk) as $disk) {
            if (! in_array($disk, ['public', ...self::CLOUD_DISKS], true)) {
                continue;
            }

            try {
                if ($disk === 'public') {
                    if (self::publicDiskHasFile($path)) {
                        return self::localWebUrl($path);
                    }

                    continue;
                }

                if (Storage::disk($disk)->exists($path)) {
                    return self::cloudAssetUrl($disk, $path);
                }
            } catch (\Throwable) {
                continue;
            }
        }

        if (PublicMediaStorage::resolvedDisk() !== 'public') {
            foreach (self::CLOUD_DISKS as $cloudDisk) {
                try {
                    if (Storage::disk($cloudDisk)->exists($path)) {
                        return self::cloudAssetUrl($cloudDisk, $path);
                    }
                } catch (\Throwable) {
                    continue;
                }
            }
        }

        return self::localWebUrl($path);
    }

    /**
     * رابط عام للملف على R2/S3 — أو بروكسي عبر /storage/ إن لم يُضبط رابط عام.
     */
    public static function cloudAssetUrl(string $disk, string $path): string
    {
        $path = str_replace('\\', '/', ltrim($path, '/'));
        $publicBase = self::cloudPublicBaseUrl($disk);

        if ($publicBase !== null) {
            return self::ensureHttpsInProduction($publicBase.'/'.$path);
        }

        if (in_array($disk, self::CLOUD_DISKS, true)) {
            $signed = self::cloudSignedUrl($disk, $path);
            if ($signed !== null) {
                return $signed;
            }
        }

        return self::localWebUrl($path);
    }

    /**
     * رابط موقّع لـ R2/S3 (bucket خاص) — يعمل مباشرة من المتصفح دون /storage/ على الاستضافة.
     */
    public static function cloudSignedUrl(string $disk, string $path): ?string
    {
        try {
            return Storage::disk($disk)->temporaryUrl($path, now()->addDays(7));
        } catch (\Throwable $e) {
            Log::debug('cloudSignedUrl failed', ['disk' => $disk, 'path' => $path, 'error' => $e->getMessage()]);

            return null;
        }
    }

    /**
     * قاعدة URL عامة للمتصفح (ليس endpoint الـ API).
     */
    public static function cloudPublicBaseUrl(string $disk): ?string
    {
        $candidates = [
            env('R2_PUBLIC_URL'),
            config("filesystems.disks.{$disk}.url"),
        ];

        foreach ($candidates as $base) {
            if (! is_string($base) || trim($base) === '') {
                continue;
            }

            $base = rtrim($base, '/');

            if (str_contains($base, 'cloudflarestorage.com')) {
                continue;
            }

            return $base;
        }

        return null;
    }

    public static function publicDiskHasFile(string $path): bool
    {
        $path = str_replace('\\', '/', ltrim($path, '/'));

        try {
            if (Storage::disk('public')->exists($path)) {
                return true;
            }
        } catch (\Throwable) {
        }

        $full = storage_path('app/public/'.str_replace('/', DIRECTORY_SEPARATOR, $path));

        return is_file($full) && is_readable($full);
    }

    /**
     * رابط بروكسي عبر Laravel (/storage/...) — يقرأ من المحلي أو R2.
     */
    public static function localWebUrl(string $path): string
    {
        $path = str_replace('\\', '/', ltrim($path, '/'));

        return rtrim(ApplicationUrl::resolveRootUrl(), '/').'/storage/'.$path;
    }

    private static function ensureHttpsInProduction(string $url): string
    {
        if (app()->environment('production') && str_starts_with($url, 'http://')) {
            return 'https://'.substr($url, 7);
        }

        return $url;
    }
}
