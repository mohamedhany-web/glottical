<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * مستندات تقديم المعلمين — صور، PDF، فيديو — على PUBLIC_MEDIA_DISK (Cloudflare R2 افتراضياً).
 */
class TutorApplicationStorage
{
    public const DIR_PHOTOS = 'tutor-applications/photos';

    public const DIR_IDS = 'tutor-applications/ids';

    public const DIR_CERTIFICATES = 'tutor-applications/certificates';

    public const DIR_VIDEOS = 'tutor-applications/videos';

    public static function resolvedDisk(): string
    {
        return PublicMediaStorage::resolvedDisk();
    }

    public static function publicUrl(?string $path): ?string
    {
        if (! is_string($path) || trim($path) === '') {
            return null;
        }

        $path = trim($path);
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return PublicStorageUrl::fromPathStable(
            PublicMediaStorage::normalizePath($path),
            self::resolvedDisk()
        );
    }

    public static function storePhoto(UploadedFile $file, ?string $oldPath = null): string
    {
        return self::store($file, self::DIR_PHOTOS, ['jpg', 'jpeg', 'png', 'webp', 'gif'], $oldPath);
    }

    public static function storeIdDocument(UploadedFile $file, ?string $oldPath = null): string
    {
        return self::store($file, self::DIR_IDS, ['jpg', 'jpeg', 'png', 'webp', 'pdf'], $oldPath);
    }

    public static function storeCertificate(UploadedFile $file, ?string $oldPath = null): string
    {
        return self::store($file, self::DIR_CERTIFICATES, ['jpg', 'jpeg', 'png', 'webp', 'pdf'], $oldPath);
    }

    public static function storeVideo(UploadedFile $file, ?string $oldPath = null): string
    {
        return self::store($file, self::DIR_VIDEOS, ['mp4', 'webm', 'mov', 'm4v', 'ogg'], $oldPath);
    }

    /**
     * @param  list<string>  $allowedExt
     */
    public static function store(UploadedFile $file, string $directory, array $allowedExt, ?string $oldPath = null): string
    {
        $disk = self::resolvedDisk();
        $directory = trim(str_replace('\\', '/', $directory), '/');

        $ext = strtolower((string) ($file->getClientOriginalExtension() ?: $file->guessExtension() ?: ''));
        $ext = preg_replace('/[^a-z0-9]/', '', $ext) ?: '';

        if ($ext === '' || ! in_array($ext, $allowedExt, true)) {
            $mime = (string) $file->getMimeType();
            $ext = match (true) {
                str_contains($mime, 'jpeg') => 'jpg',
                str_contains($mime, 'png') => 'png',
                str_contains($mime, 'webp') => 'webp',
                str_contains($mime, 'pdf') => 'pdf',
                str_contains($mime, 'webm') => 'webm',
                str_contains($mime, 'quicktime') => 'mov',
                str_contains($mime, 'mp4') => 'mp4',
                default => $allowedExt[0] ?? 'bin',
            };
            if (! in_array($ext, $allowedExt, true)) {
                $ext = $allowedExt[0] ?? 'bin';
            }
        }

        $name = Str::uuid()->toString().'.'.$ext;

        if ($disk === 'public') {
            Storage::disk('public')->makeDirectory($directory);
            $stored = $file->storeAs($directory, $name, 'public');
        } else {
            $stored = Storage::disk($disk)->putFileAs($directory, $file, $name, ['visibility' => 'public']);
        }

        if (! is_string($stored) || $stored === '') {
            throw new \RuntimeException('فشل رفع الملف على Cloudflare R2 / التخزين.');
        }

        $stored = str_replace('\\', '/', $stored);

        if (is_string($oldPath) && $oldPath !== '' && $oldPath !== $stored) {
            PublicMediaStorage::delete($oldPath);
        }

        return $stored;
    }

    public static function delete(?string $path): void
    {
        PublicMediaStorage::delete($path);
    }

    public static function isPdf(?string $path): bool
    {
        if (! is_string($path) || $path === '') {
            return false;
        }

        return str_ends_with(strtolower(parse_url($path, PHP_URL_PATH) ?: $path), '.pdf');
    }
}
