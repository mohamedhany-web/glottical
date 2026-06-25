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
    /** مسار البروكسي عبر Laravel — يتجنب تعارض مجلد storage/ على الاستضافة */
    public const PROXY_PATH = 'media';

    /** @var array<int, string> */
    private const CLOUD_DISKS = ['r2', 's3'];

    private const SIGNED_URL_TTL_DAYS = 7;

    /** @var array<string, string|null> */
    private static array $signedUrlMemo = [];

    public static function fromPath(?string $path, ?string $preferredDisk = null): ?string
    {
        if (! is_string($path) || trim($path) === '') {
            return null;
        }

        $path = str_replace('\\', '/', ltrim($path, '/'));

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return self::ensureHttpsInProduction($path);
        }

        $disk = $preferredDisk ?? PublicMediaStorage::resolvedDisk();

        // مسار سريع: R2/S3 مع رابط عام (بدون exists — يُوفّر طلبات API ويُسرّع الصفحة)
        if (in_array($disk, self::CLOUD_DISKS, true)) {
            $direct = self::cloudDirectUrl($disk, $path);
            if ($direct !== null) {
                return $direct;
            }
        }

        if ($disk === 'public' && self::publicDiskHasFile($path)) {
            return self::localWebUrl($path);
        }

        $resolvedDefault = PublicMediaStorage::resolvedDisk();
        if (in_array($resolvedDefault, self::CLOUD_DISKS, true)) {
            return self::cloudDirectUrl($resolvedDefault, $path);
        }

        foreach (PublicMediaStorage::disksToProbe($preferredDisk) as $probeDisk) {
            if ($probeDisk === $disk) {
                continue;
            }

            if (in_array($probeDisk, self::CLOUD_DISKS, true)) {
                $direct = self::cloudDirectUrl($probeDisk, $path);
                if ($direct !== null) {
                    return $direct;
                }

                try {
                    if (Storage::disk($probeDisk)->exists($path)) {
                        return self::cloudAssetUrl($probeDisk, $path);
                    }
                } catch (\Throwable) {
                    continue;
                }

                continue;
            }

            if ($probeDisk === 'public' && self::publicDiskHasFile($path)) {
                return self::localWebUrl($path);
            }
        }

        return null;
    }

    /**
     * رابط ثابت للمحتوى الإداري (سلايدر الهيرو، إلخ) — CDN عام أو بروكسي /media/ فقط.
     * لا يستخدم روابط موقّعة متغيّرة حتى لا تتأثر الصور المرفوعة من لوحة التحكم.
     */
    public static function fromPathStable(?string $path, ?string $preferredDisk = null): ?string
    {
        if (! is_string($path) || trim($path) === '') {
            return null;
        }

        $path = str_replace('\\', '/', ltrim($path, '/'));

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return self::ensureHttpsInProduction($path);
        }

        $disk = $preferredDisk ?? PublicMediaStorage::resolvedDisk();

        if (in_array($disk, self::CLOUD_DISKS, true)) {
            $stable = self::cloudStableUrl($disk, $path);
            if ($stable !== null) {
                return $stable;
            }
        }

        if ($disk === 'public' && self::publicDiskHasFile($path)) {
            return self::localWebUrl($path);
        }

        $resolvedDefault = PublicMediaStorage::resolvedDisk();
        if (in_array($resolvedDefault, self::CLOUD_DISKS, true)) {
            $stable = self::cloudStableUrl($resolvedDefault, $path);
            if ($stable !== null) {
                return $stable;
            }
        }

        foreach (PublicMediaStorage::disksToProbe($preferredDisk) as $probeDisk) {
            if ($probeDisk === $disk) {
                continue;
            }

            if (in_array($probeDisk, self::CLOUD_DISKS, true)) {
                $stable = self::cloudStableUrl($probeDisk, $path);
                if ($stable !== null) {
                    return $stable;
                }

                continue;
            }

            if ($probeDisk === 'public' && self::publicDiskHasFile($path)) {
                return self::localWebUrl($path);
            }
        }

        return self::localWebUrl($path);
    }

    /**
     * رابط ثابت لـ R2/S3 — CDN عام أو بروكسي فقط (بدون توقيع مؤقت).
     */
    public static function cloudStableUrl(string $disk, string $path): ?string
    {
        if (! in_array($disk, self::CLOUD_DISKS, true)) {
            return null;
        }

        $path = str_replace('\\', '/', ltrim($path, '/'));
        $publicBase = self::cloudPublicBaseUrl($disk);

        if ($publicBase !== null) {
            return self::ensureHttpsInProduction($publicBase.'/'.$path);
        }

        return self::localWebUrl($path);
    }

    /**
     * رابط مباشر للمتصفح (CDN عام أو موقّع) — للبطاقات والصور العامة فقط.
     */
    public static function cloudDirectUrl(string $disk, string $path): ?string
    {
        if (! in_array($disk, self::CLOUD_DISKS, true)) {
            return null;
        }

        $path = str_replace('\\', '/', ltrim($path, '/'));
        $publicBase = self::cloudPublicBaseUrl($disk);

        if ($publicBase !== null) {
            return self::ensureHttpsInProduction($publicBase.'/'.$path);
        }

        $signed = self::cloudSignedUrl($disk, $path);
        if ($signed !== null) {
            return self::ensureHttpsInProduction($signed);
        }

        // بدون r2.dev: بروكسي عبر /media/ (أبطأ — يُفضّل ضبط R2_PUBLIC_URL)
        return self::localWebUrl($path);
    }

    /**
     * رابط عام للملف على R2/S3 — أو بروكسي عبر /storage/ إن لم يُضبط رابط عام.
     */
    public static function cloudAssetUrl(string $disk, string $path): string
    {
        $direct = self::cloudDirectUrl($disk, $path);
        if ($direct !== null) {
            return $direct;
        }

        return self::localWebUrl($path);
    }

    /**
     * رابط موقّع لـ R2/S3 (bucket خاص) — يعمل مباشرة من المتصفح دون /storage/ على الاستضافة.
     */
    public static function cloudSignedUrl(string $disk, string $path): ?string
    {
        $path = str_replace('\\', '/', ltrim($path, '/'));
        $memoKey = $disk.':'.$path;
        if (array_key_exists($memoKey, self::$signedUrlMemo)) {
            return self::$signedUrlMemo[$memoKey];
        }

        try {
            $url = Storage::disk($disk)->temporaryUrl($path, now()->addDays(self::SIGNED_URL_TTL_DAYS));
            self::$signedUrlMemo[$memoKey] = $url;

            return $url;
        } catch (\Throwable $e) {
            Log::debug('cloudSignedUrl failed', ['disk' => $disk, 'path' => $path, 'error' => $e->getMessage()]);
            self::$signedUrlMemo[$memoKey] = null;

            return null;
        }
    }

    /**
     * قاعدة URL عامة للمتصفح (ليس endpoint الـ API).
     */
    public static function cloudPublicBaseUrl(string $disk): ?string
    {
        $candidates = [
            PlatformMediaSettings::r2PublicBaseUrl(),
            config('filesystems.r2_public_url'),
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

    public static function isApplicationProxyUrl(string $url): bool
    {
        $root = rtrim(ApplicationUrl::resolveRootUrl(), '/');
        if ($root === '') {
            return false;
        }

        return str_starts_with($url, $root.'/storage/')
            || str_starts_with($url, $root.'/'.self::PROXY_PATH.'/');
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

        return rtrim(ApplicationUrl::resolveRootUrl(), '/').'/'.self::PROXY_PATH.'/'.$path;
    }

    private static function ensureHttpsInProduction(string $url): string
    {
        if (app()->environment('production') && str_starts_with($url, 'http://')) {
            return 'https://'.substr($url, 7);
        }

        return $url;
    }
}
