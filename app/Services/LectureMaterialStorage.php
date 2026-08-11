<?php

namespace App\Services;

use App\Models\LectureMaterial;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * ملفات مكتبة الماتريال (محاضرات) — تُرفع افتراضياً على Cloudflare R2.
 *
 * .env: LECTURE_MATERIALS_DISK=r2 (أو فارغ ليتبع PUBLIC_MEDIA_DISK)
 */
class LectureMaterialStorage
{
    public const DIRECTORY = 'lecture-materials';

    public static function resolvedDisk(): string
    {
        $d = (string) config('filesystems.lecture_materials_disk', '');
        if ($d === '' || $d === '0') {
            $d = (string) config('filesystems.public_media_disk', 'r2');
        }

        if ($d === 'r2') {
            $bucket = config('filesystems.disks.r2.bucket');
            $endpoint = config('filesystems.disks.r2.endpoint');
            if (empty($bucket) || empty($endpoint)) {
                Log::warning('LECTURE_MATERIALS_DISK=r2 لكن إعدادات Cloudflare R2 غير مكتملة؛ يُستخدم القرص public.');

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

    public static function diskFor(?LectureMaterial $material): string
    {
        $hint = $material?->storage_disk;
        if (is_string($hint) && in_array($hint, ['public', 'r2', 's3'], true)) {
            if ($hint === 'r2') {
                $bucket = config('filesystems.disks.r2.bucket');
                $endpoint = config('filesystems.disks.r2.endpoint');
                if (! empty($bucket) && ! empty($endpoint)) {
                    return 'r2';
                }
            } elseif ($hint === 's3' && ! empty(config('filesystems.disks.s3.bucket'))) {
                return 's3';
            } elseif ($hint === 'public') {
                return 'public';
            }
        }

        return self::resolvedDisk();
    }

    /**
     * @return string المسار النسبي داخل القرص
     */
    public static function store(UploadedFile $file, int $lectureId): string
    {
        $disk = self::resolvedDisk();
        $dir = trim(self::DIRECTORY, '/').'/'.$lectureId;
        $ext = strtolower($file->getClientOriginalExtension() ?: $file->guessExtension() ?: 'bin');
        $ext = preg_replace('/[^a-z0-9]/', '', $ext) ?: 'bin';
        $name = Str::uuid()->toString().'.'.$ext;

        if ($disk === 'public') {
            Storage::disk('public')->makeDirectory($dir);
            $stored = $file->storeAs($dir, $name, 'public');
        } else {
            $stored = Storage::disk($disk)->putFileAs($dir, $file, $name, ['visibility' => 'public']);
        }

        if (! is_string($stored) || $stored === '') {
            throw new \RuntimeException('فشل حفظ ملف الماتريال على التخزين السحابي.');
        }

        return $stored;
    }

    public static function delete(?string $path, ?string $hintDisk = null): void
    {
        if (! is_string($path) || $path === '') {
            return;
        }

        $path = str_replace('\\', '/', ltrim($path, '/'));
        $disks = array_unique(array_filter([
            $hintDisk,
            self::resolvedDisk(),
            'public',
            'r2',
            's3',
        ]));

        foreach ($disks as $d) {
            if (! in_array($d, ['public', 'r2', 's3'], true)) {
                continue;
            }
            try {
                if (Storage::disk($d)->exists($path)) {
                    Storage::disk($d)->delete($path);
                }
            } catch (\Throwable) {
            }
        }
    }

    public static function exists(?string $path, ?string $hintDisk = null): bool
    {
        if (! is_string($path) || $path === '') {
            return false;
        }

        $path = str_replace('\\', '/', ltrim($path, '/'));
        foreach (array_unique(array_filter([$hintDisk, self::resolvedDisk(), 'public', 'r2', 's3'])) as $d) {
            if (! in_array($d, ['public', 'r2', 's3'], true)) {
                continue;
            }
            try {
                if (Storage::disk($d)->exists($path)) {
                    return true;
                }
            } catch (\Throwable) {
                continue;
            }
        }

        return false;
    }

    public static function download(LectureMaterial $material): StreamedResponse
    {
        $path = str_replace('\\', '/', ltrim((string) $material->file_path, '/'));
        abort_if($path === '', 404, 'الملف غير موجود');

        $diskName = self::resolveExistingDisk($path, self::diskFor($material));
        abort_if($diskName === null, 404, 'الملف غير موجود');

        $downloadName = $material->file_name ?: basename($path);

        return Storage::disk($diskName)->download($path, $downloadName);
    }

    public static function publicUrl(?LectureMaterial $material): ?string
    {
        if (! $material || ! is_string($material->file_path) || $material->file_path === '') {
            return null;
        }

        $path = str_replace('\\', '/', ltrim($material->file_path, '/'));

        return PublicStorageUrl::fromPath($path, self::diskFor($material));
    }

    /**
     * انسخ ملفاً محلياً قديماً إلى R2 إن كان القرص الحالي r2.
     */
    public static function mirrorLocalToCloud(string $path): bool
    {
        $path = str_replace('\\', '/', ltrim($path, '/'));
        $target = self::resolvedDisk();
        if ($target === 'public' || ! in_array($target, ['r2', 's3'], true)) {
            return false;
        }

        try {
            if (! Storage::disk('public')->exists($path)) {
                return false;
            }
            if (Storage::disk($target)->exists($path)) {
                return true;
            }
            $stream = Storage::disk('public')->readStream($path);
            if ($stream === false) {
                return false;
            }
            Storage::disk($target)->writeStream($path, $stream, ['visibility' => 'public']);
            if (is_resource($stream)) {
                fclose($stream);
            }

            return Storage::disk($target)->exists($path);
        } catch (\Throwable $e) {
            Log::warning('فشل ترحيل ماتريال إلى Cloudflare R2: '.$e->getMessage(), ['path' => $path]);

            return false;
        }
    }

    private static function resolveExistingDisk(string $path, ?string $preferred = null): ?string
    {
        foreach (array_unique(array_filter([$preferred, self::resolvedDisk(), 'r2', 's3', 'public'])) as $d) {
            if (! in_array($d, ['public', 'r2', 's3'], true)) {
                continue;
            }
            try {
                if (Storage::disk($d)->exists($path)) {
                    return $d;
                }
            } catch (\Throwable) {
                continue;
            }
        }

        return null;
    }
}
