<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

/**
 * روابط ملفات التخزين العام (/storage/...) — تعمل محلياً وعلى الاستضافة.
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

        $disks = array_values(array_unique(array_filter([
            $preferredDisk,
            'public',
            ...self::CLOUD_DISKS,
        ])));

        foreach ($disks as $disk) {
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

                $storage = Storage::disk($disk);
                if ($storage->exists($path)) {
                    $cloudUrl = $storage->url($path);
                    if (is_string($cloudUrl) && $cloudUrl !== '') {
                        return self::ensureHttpsInProduction($cloudUrl);
                    }
                }
            } catch (\Throwable) {
                continue;
            }
        }

        return self::localWebUrl($path);
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
     * رابط ويب لملف داخل storage/app/public.
     */
    public static function localWebUrl(string $path): string
    {
        $path = str_replace('\\', '/', ltrim($path, '/'));

        if (! app()->runningInConsole()) {
            $request = request();
            if ($request) {
                return rtrim($request->root(), '/').'/storage/'.$path;
            }
        }

        return rtrim((string) config('app.url'), '/').'/storage/'.$path;
    }

    private static function ensureHttpsInProduction(string $url): string
    {
        if (app()->environment('production') && str_starts_with($url, 'http://')) {
            return 'https://'.substr($url, 7);
        }

        return $url;
    }
}
