<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * رفع صورة غلاف الكورس وحفظ المسار أو الرابط في قاعدة البيانات.
 */
class CourseThumbnailStorage
{
    public const DISK = 'public';

    public const DIRECTORY = 'courses';

    /**
     * من طلب الإدارة: ملف مرفوع أو رابط خارجي (يُحفظ في عمود thumbnail).
     */
    public static function resolveFromRequest(Request $request, ?string $existingPath = null): ?string
    {
        if ($request->hasFile('thumbnail')) {
            $path = self::storeUploadedFile($request->file('thumbnail'));
            if ($existingPath && ! self::isExternalUrl($existingPath)) {
                self::deleteIfStored($existingPath);
            }

            return $path;
        }

        $external = trim((string) $request->input('thumbnail_link', ''));
        if ($external !== '') {
            if (! filter_var($external, FILTER_VALIDATE_URL)) {
                return $existingPath;
            }

            return $external;
        }

        if ($request->boolean('remove_thumbnail') && $existingPath) {
            if (! self::isExternalUrl($existingPath)) {
                self::deleteIfStored($existingPath);
            }

            return null;
        }

        return $existingPath;
    }

    public static function storeUploadedFile(UploadedFile $file): string
    {
        return $file->store(self::DIRECTORY, self::DISK);
    }

    public static function publicUrl(?string $thumbnail): ?string
    {
        return storage_public_url($thumbnail);
    }

    public static function isExternalUrl(?string $value): bool
    {
        if (! is_string($value) || $value === '') {
            return false;
        }

        return str_starts_with($value, 'http://') || str_starts_with($value, 'https://');
    }

    public static function deleteIfStored(?string $path): void
    {
        if (! is_string($path) || $path === '' || self::isExternalUrl($path)) {
            return;
        }

        $normalized = str_replace('\\', '/', ltrim($path, '/'));
        try {
            Storage::disk(self::DISK)->delete($normalized);
        } catch (\Throwable) {
        }
    }
}
