<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

/**
 * رفع صورة غلاف الكورس وحفظ المسار أو الرابط في قاعدة البيانات.
 */
class CourseThumbnailStorage
{
    public const DIRECTORY = 'courses';

    /**
     * من طلب الإدارة: ملف مرفوع أو رابط خارجي (يُحفظ في عمود thumbnail).
     */
    public static function resolveFromRequest(Request $request, ?string $existingPath = null): ?string
    {
        if ($request->hasFile('thumbnail')) {
            return PublicMediaStorage::store(
                $request->file('thumbnail'),
                self::DIRECTORY,
                self::isExternalUrl($existingPath) ? null : $existingPath
            );
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
                PublicMediaStorage::delete($existingPath);
            }

            return null;
        }

        return $existingPath;
    }

    public static function storeUploadedFile(UploadedFile $file): string
    {
        return PublicMediaStorage::storeUploaded($file, self::DIRECTORY);
    }

    public static function publicUrl(?string $thumbnail): ?string
    {
        return PublicMediaStorage::publicUrl($thumbnail);
    }

    public static function isExternalUrl(?string $value): bool
    {
        return PublicMediaStorage::isExternalUrl($value);
    }

    public static function deleteIfStored(?string $path): void
    {
        PublicMediaStorage::delete($path);
    }
}
