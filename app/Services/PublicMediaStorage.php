<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * وسائط الموقع العامة (صور الكورسات، الباقات، الشعار، …) — محلي أو Cloudflare R2.
 *
 * .env: PUBLIC_MEDIA_DISK=r2 و AWS_* و (يفضّل) R2_PUBLIC_URL أو AWS_URL للرابط العام.
 */
class PublicMediaStorage
{
    /** @var array<int, string> */
    private const CLOUD_DISKS = ['r2', 's3'];

    public static function resolvedDisk(): string
    {
        $d = (string) config('filesystems.public_media_disk', 'public');

        if ($d === 'r2') {
            $bucket = config('filesystems.disks.r2.bucket');
            $endpoint = config('filesystems.disks.r2.endpoint');
            if (empty($bucket) || empty($endpoint)) {
                Log::warning('PUBLIC_MEDIA_DISK=r2 لكن إعدادات R2 غير مكتملة؛ يُستخدم القرص public.');

                return 'public';
            }
        }

        if ($d === 's3') {
            $bucket = config('filesystems.disks.s3.bucket');
            if (empty($bucket)) {
                return 'public';
            }
        }

        if (! in_array($d, ['public', 'r2', 's3'], true)) {
            return 'public';
        }

        return $d;
    }

    public static function publicUrl(?string $path, ?string $hintDisk = null): ?string
    {
        return PublicStorageUrl::fromPath($path, $hintDisk ?? self::resolvedDisk());
    }

    public static function exists(string $path): bool
    {
        $path = self::normalizePath($path);

        foreach (self::disksToProbe(null) as $disk) {
            try {
                if ($disk === 'public') {
                    if (PublicStorageUrl::publicDiskHasFile($path)) {
                        return true;
                    }

                    continue;
                }

                if (Storage::disk($disk)->exists($path)) {
                    return true;
                }
            } catch (\Throwable) {
                continue;
            }
        }

        return false;
    }

    /**
     * @return string المسار النسبي داخل القرص (يُحفظ في قاعدة البيانات)
     */
    public static function store(UploadedFile $file, string $directory, ?string $oldPath = null): string
    {
        $disk = self::resolvedDisk();
        $directory = trim(str_replace('\\', '/', $directory), '/');

        $ext = match ($file->getMimeType()) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'image/gif' => 'gif',
            default => strtolower($file->getClientOriginalExtension() ?: $file->guessExtension() ?: 'jpg'),
        };
        if (! in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true)) {
            $ext = 'jpg';
        }

        $name = Str::uuid()->toString().'.'.$ext;

        if ($disk === 'public') {
            Storage::disk('public')->makeDirectory($directory);
            $stored = $file->storeAs($directory, $name, 'public');
        } else {
            $stored = Storage::disk($disk)->putFileAs($directory, $file, $name, 'public');
        }

        if (! is_string($stored) || $stored === '') {
            throw new \RuntimeException('فشل رفع الملف على التخزين.');
        }

        if (is_string($oldPath) && $oldPath !== '' && $oldPath !== $stored) {
            self::delete($oldPath);
        }

        return str_replace('\\', '/', $stored);
    }

    public static function storeUploaded(UploadedFile $file, string $directory): string
    {
        return self::store($file, $directory, null);
    }

    public static function delete(?string $path): void
    {
        if (! is_string($path) || $path === '' || self::isExternalUrl($path)) {
            return;
        }

        $path = self::normalizePath($path);
        foreach (array_unique([self::resolvedDisk(), 'public', 'r2', 's3']) as $disk) {
            if (! in_array($disk, ['public', 'r2', 's3'], true)) {
                continue;
            }
            try {
                if ($disk === 'public') {
                    if (Storage::disk('public')->exists($path)) {
                        Storage::disk('public')->delete($path);
                    }

                    continue;
                }

                if (Storage::disk($disk)->exists($path)) {
                    Storage::disk($disk)->delete($path);
                }
            } catch (\Throwable) {
            }
        }
    }

    public static function isExternalUrl(?string $value): bool
    {
        if (! is_string($value) || $value === '') {
            return false;
        }

        return str_starts_with($value, 'http://') || str_starts_with($value, 'https://');
    }

    /**
     * نسخ ملف من القرص المحلي إلى R2 (للترحيل).
     */
    public static function mirrorLocalToR2(string $path): bool
    {
        $path = self::normalizePath($path);
        if ($path === '' || ! PublicStorageUrl::publicDiskHasFile($path)) {
            return false;
        }

        $targetDisk = self::resolvedDisk();
        if ($targetDisk === 'public') {
            return true;
        }

        try {
            if (Storage::disk($targetDisk)->exists($path)) {
                return true;
            }

            $contents = Storage::disk('public')->get($path);
            Storage::disk($targetDisk)->put($path, $contents, 'public');

            return Storage::disk($targetDisk)->exists($path);
        } catch (\Throwable $e) {
            Log::warning('mirrorLocalToR2 failed', ['path' => $path, 'error' => $e->getMessage()]);

            return false;
        }
    }

    /** @return array<int, string> */
    public static function disksToProbe(?string $preferredDisk): array
    {
        return array_values(array_unique(array_filter([
            $preferredDisk,
            self::resolvedDisk(),
            'public',
            ...self::CLOUD_DISKS,
        ])));
    }

    public static function normalizePath(string $path): string
    {
        return str_replace('\\', '/', ltrim($path, '/'));
    }
}
