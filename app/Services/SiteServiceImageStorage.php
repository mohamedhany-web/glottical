<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * صور خدمات الموقع العامة — محلي (public) أو Cloudflare R2 عبر قرص s3 المتوافق.
 */
class SiteServiceImageStorage
{
    public const DIRECTORY = 'site-services';

    public static function resolvedDisk(): string
    {
        $d = (string) config('filesystems.site_services_disk', 'public');

        if ($d === 'r2') {
            $bucket = config('filesystems.disks.r2.bucket');
            $endpoint = config('filesystems.disks.r2.endpoint');
            if (empty($bucket) || empty($endpoint)) {
                Log::warning('SITE_SERVICES_DISK=r2 لكن إعدادات R2 غير مكتملة؛ يُستخدم القرص public.');

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

    public static function publicUrl(?string $path): ?string
    {
        if (! is_string($path) || $path === '') {
            return null;
        }

        $path = str_replace('\\', '/', ltrim($path, '/'));
        $disk = self::resolvedDisk();

        if (! Storage::disk($disk)->exists($path)) {
            if ($disk !== 'public' && Storage::disk('public')->exists($path)) {
                return self::publicStorageUrl($path);
            }

            return null;
        }

        if ($disk === 'public') {
            return self::publicStorageUrl($path);
        }

        return Storage::disk($disk)->url($path);
    }

    private static function publicStorageUrl(string $path): string
    {
        return PublicStorageUrl::localWebUrl($path);
    }

    /**
     * يحفظ ملفاً جديداً ويحذف المسار القديم إن وُجد.
     *
     * @return string المسار النسبي داخل القرص
     */
    public static function store(UploadedFile $file, ?string $oldPath): string
    {
        $disk = self::resolvedDisk();

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
        $dir = trim(self::DIRECTORY, '/');

        if ($disk === 'public') {
            Storage::disk('public')->makeDirectory($dir);
            $stored = $file->storeAs($dir, $name, 'public');
        } else {
            $stored = Storage::disk($disk)->putFileAs($dir, $file, $name, 'public');
        }

        if (! is_string($stored) || $stored === '') {
            throw new \RuntimeException('فشل حفظ صورة الخدمة.');
        }

        if (is_string($oldPath) && $oldPath !== '' && $oldPath !== $stored) {
            self::delete($oldPath);
        }

        return $stored;
    }

    public static function delete(?string $path): void
    {
        if (! is_string($path) || $path === '') {
            return;
        }

        $path = str_replace('\\', '/', ltrim($path, '/'));
        foreach (array_unique([self::resolvedDisk(), 'public', 'r2', 's3']) as $d) {
            if (! in_array($d, ['public', 'r2', 's3'], true)) {
                continue;
            }
            try {
                if (Storage::disk($d)->exists($path)) {
                    Storage::disk($d)->delete($path);
                }
            } catch (\Throwable) {
                // تجاهل فشل حذف على قرص غير مهيأ
            }
        }
    }
}
