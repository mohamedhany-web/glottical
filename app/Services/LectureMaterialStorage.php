<?php

namespace App\Services;

use App\Models\LectureMaterial;
use App\Support\FamilyLibraryThemes;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
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

        return CloudflareR2::resolveDisk($d);
    }

    public static function supportsDirectUpload(): bool
    {
        return CloudflareR2::isReady();
    }

    public static function maxBytes(): int
    {
        return 50 * 1024 * 1024;
    }

    /**
     * @return list<string>
     */
    public static function allowedExtensions(): array
    {
        return array_values(array_filter(array_map(
            static fn ($ext) => strtolower(trim((string) $ext)),
            explode(',', FamilyLibraryThemes::materialMimes())
        )));
    }

    public static function uploadFailureHint(\Throwable $e): string
    {
        return CurriculumLibraryStorage::uploadFailureHint($e);
    }

    /**
     * تجهيز PUT موقّت: المتصفح يرفع مباشرة إلى R2 دون المرور بحدود PHP/Cloudflare على الطلب.
     *
     * @return array{ok:true, upload_url:string, upload_token:string, content_type:string, headers:array}|array{ok:false, message:string, status:int, direct_upload?:bool}
     */
    public static function presignDirectUpload(int $userId, string $filename, ?string $contentType, int $fileSize): array
    {
        if (! self::supportsDirectUpload()) {
            return [
                'ok' => false,
                'direct_upload' => false,
                'status' => 422,
                'message' => CurriculumLibraryStorage::adminStatusMessage()
                    ?: 'الرفع المباشر إلى Cloudflare غير متاح. استخدم الرفع عبر الخادم.',
            ];
        }

        if ($fileSize < 1 || $fileSize > self::maxBytes()) {
            return [
                'ok' => false,
                'status' => 422,
                'message' => 'حجم الملف يتجاوز الحد المسموح (50 ميجابايت).',
            ];
        }

        $originalName = basename(str_replace(["\0", '\\'], '', $filename));
        if ($originalName === '' || $originalName === '.' || $originalName === '..') {
            return ['ok' => false, 'status' => 422, 'message' => 'اسم الملف غير صالح.'];
        }

        $ext = strtolower((string) pathinfo($originalName, PATHINFO_EXTENSION));
        if ($ext === '' || ! in_array($ext, self::allowedExtensions(), true)) {
            return ['ok' => false, 'status' => 422, 'message' => 'امتداد الملف غير مسموح.'];
        }

        $mime = self::normalizeMime((string) ($contentType ?? ''), $ext);
        CloudflareR2::ensureBrowserUploadCors();
        $path = trim(self::DIRECTORY, '/').'/direct/'.now()->format('Ym').'/'.Str::uuid()->toString().'.'.$ext;
        $token = Str::random(64);

        Cache::put(
            'lecture_material_presign:'.$token,
            [
                'path' => $path,
                'user_id' => $userId,
                'mime' => $mime,
                'disk' => 'r2',
                'original_name' => $originalName,
                'max_bytes' => self::maxBytes(),
            ],
            now()->addMinutes(75)
        );

        try {
            $signed = Storage::disk('r2')->temporaryUploadUrl(
                $path,
                now()->addMinutes(70),
                ['ContentType' => $mime]
            );
        } catch (\Throwable $e) {
            Cache::forget('lecture_material_presign:'.$token);
            Log::error('Lecture material presign failed', ['error' => $e->getMessage()]);

            return [
                'ok' => false,
                'direct_upload' => false,
                'status' => 503,
                'message' => 'تعذّر تجهيز الرفع المباشر إلى Cloudflare R2. راجع CORS على الـ bucket (PUT من نطاق الموقع) ثم أعد المحاولة.',
            ];
        }

        return [
            'ok' => true,
            'upload_url' => $signed['url'],
            'upload_token' => $token,
            'content_type' => $mime,
            'headers' => CurriculumLibraryR2MultipartService::filterPresignedUploadHeadersForBrowser($signed['headers'] ?? []),
        ];
    }

    /**
     * بعد PUT الناجح: التأكد أن الملف ظاهر على R2 دون استهلاك التوكن (الاستهلاك عند الحفظ).
     *
     * @return array{ok:true, file_path:string, storage_disk:string, original_name:string, file_size:int}|array{ok:false, message:string, status:int}
     */
    public static function confirmDirectUpload(int $userId, string $uploadToken): array
    {
        $payload = Cache::get('lecture_material_presign:'.$uploadToken);
        if (! is_array($payload) || (int) ($payload['user_id'] ?? 0) !== $userId) {
            return ['ok' => false, 'status' => 422, 'message' => 'انتهت صلاحية رابط الرفع أو أنه غير صالح.'];
        }

        $path = (string) ($payload['path'] ?? '');
        $diskName = (string) ($payload['disk'] ?? 'r2');
        if ($path === '' || str_contains($path, '..') || $diskName !== 'r2') {
            return ['ok' => false, 'status' => 422, 'message' => 'مسار التخزين غير صالح.'];
        }

        try {
            $disk = Storage::disk($diskName);
            if (! $disk->exists($path)) {
                return [
                    'ok' => false,
                    'status' => 422,
                    'message' => 'الملف غير ظاهر على Cloudflare بعد. انتظر لحظة ثم أكّد مرة أخرى.',
                ];
            }
            $size = (int) $disk->size($path);
        } catch (\Throwable $e) {
            Log::error('Lecture material confirm failed', ['error' => $e->getMessage(), 'path' => $path]);

            return [
                'ok' => false,
                'status' => 503,
                'message' => self::uploadFailureHint($e),
            ];
        }

        if ($size <= 0) {
            return ['ok' => false, 'status' => 422, 'message' => 'الملف المرفوع فارغ.'];
        }

        return [
            'ok' => true,
            'file_path' => $path,
            'storage_disk' => $diskName,
            'original_name' => (string) ($payload['original_name'] ?? basename($path)),
            'file_size' => $size,
        ];
    }

    /**
     * احتياطي عند فشل CORS: المتصفح يرسل الملف لنفس أصل الموقع ثم الخادم يكتبه على R2.
     *
     * @return array{ok:true, file_path:string, storage_disk:string, original_name:string, file_size:int}
     */
    public static function proxyDirectUpload(int $userId, string $uploadToken, UploadedFile $file): array
    {
        $payload = Cache::get('lecture_material_presign:'.$uploadToken);
        if (! is_array($payload) || (int) ($payload['user_id'] ?? 0) !== $userId) {
            throw new \RuntimeException('انتهت صلاحية رابط الرفع أو أنه غير صالح.');
        }

        $path = (string) ($payload['path'] ?? '');
        $diskName = (string) ($payload['disk'] ?? 'r2');
        $originalName = (string) ($payload['original_name'] ?? $file->getClientOriginalName());
        $maxBytes = (int) ($payload['max_bytes'] ?? self::maxBytes());
        if ($path === '' || str_contains($path, '..') || $diskName !== 'r2' || ! str_starts_with($path, trim(self::DIRECTORY, '/').'/')) {
            throw new \RuntimeException('مسار التخزين غير صالح.');
        }
        if ($file->getSize() > $maxBytes) {
            throw new \RuntimeException('حجم الملف يتجاوز الحد المسموح.');
        }

        $stream = fopen($file->getRealPath(), 'rb');
        if ($stream === false) {
            throw new \RuntimeException('تعذّر قراءة الملف للرفع عبر الخادم.');
        }

        try {
            Storage::disk($diskName)->writeStream($path, $stream);
        } finally {
            if (is_resource($stream)) {
                fclose($stream);
            }
        }

        if (! Storage::disk($diskName)->exists($path)) {
            throw new \RuntimeException('تعذّر حفظ الملف على Cloudflare R2 عبر الخادم.');
        }

        return [
            'ok' => true,
            'file_path' => $path,
            'storage_disk' => $diskName,
            'original_name' => $originalName !== '' ? $originalName : basename($path),
            'file_size' => (int) Storage::disk($diskName)->size($path),
        ];
    }

    /**
     * استهلاك توكن الرفع المباشر عند حفظ السجل.
     *
     * @return array{path:string, disk:string, original_name:string}
     */
    public static function claimDirectUpload(int $userId, string $uploadToken): array
    {
        $payload = Cache::pull('lecture_material_presign:'.$uploadToken);
        if (! is_array($payload) || (int) ($payload['user_id'] ?? 0) !== $userId) {
            throw new \RuntimeException('انتهت صلاحية الرفع أو أنه غير صالح. أعد اختيار الملف.');
        }

        $path = (string) ($payload['path'] ?? '');
        $diskName = (string) ($payload['disk'] ?? 'r2');
        $originalName = (string) ($payload['original_name'] ?? '');
        if ($path === '' || str_contains($path, '..') || $diskName !== 'r2' || ! str_starts_with($path, trim(self::DIRECTORY, '/').'/')) {
            throw new \RuntimeException('مسار التخزين غير صالح.');
        }

        if (! Storage::disk($diskName)->exists($path)) {
            throw new \RuntimeException('الملف غير موجود على Cloudflare R2. أعد الرفع.');
        }

        return [
            'path' => $path,
            'disk' => $diskName,
            'original_name' => $originalName !== '' ? $originalName : basename($path),
        ];
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
        return self::storeInDirectory($file, trim(self::DIRECTORY, '/').'/'.$lectureId);
    }

    /**
     * رفع ملف داخل فولدر معلم×سنة (بدون محاضرة).
     */
    public static function storeForFolder(UploadedFile $file, int $folderId): string
    {
        return self::storeInDirectory($file, trim(self::DIRECTORY, '/').'/folders/'.$folderId);
    }

    /**
     * رفع تسجيل محاضرة من جهاز المعلم.
     */
    public static function storeLectureRecording(UploadedFile $file, int $lectureId): string
    {
        return self::storeInDirectory($file, 'lecture-recordings/'.$lectureId);
    }

    private static function storeInDirectory(UploadedFile $file, string $dir): string
    {
        $disk = self::resolvedDisk();
        $ext = strtolower($file->getClientOriginalExtension() ?: $file->guessExtension() ?: 'bin');
        $ext = preg_replace('/[^a-z0-9]/', '', $ext) ?: 'bin';
        $name = Str::uuid()->toString().'.'.$ext;

        if ($disk === 'public') {
            Storage::disk('public')->makeDirectory($dir);
            $stored = $file->storeAs($dir, $name, 'public');
        } else {
            // لا نرسل ACL public-read — Cloudflare R2 يرفضها افتراضياً.
            $stored = Storage::disk($disk)->putFileAs($dir, $file, $name);
        }

        if (! is_string($stored) || $stored === '') {
            throw new \RuntimeException('فشل حفظ الملف على التخزين.');
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

    /**
     * قراءة محتوى ملف نصي/HTML للعرض داخل المنصة.
     */
    public static function getContents(LectureMaterial $material): ?string
    {
        $path = str_replace('\\', '/', ltrim((string) $material->file_path, '/'));
        if ($path === '') {
            return null;
        }

        $diskName = self::resolveExistingDisk($path, self::diskFor($material));
        if ($diskName === null) {
            return null;
        }

        try {
            $raw = Storage::disk($diskName)->get($path);
        } catch (\Throwable) {
            return null;
        }

        return is_string($raw) ? $raw : null;
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
            Storage::disk($target)->writeStream($path, $stream);
            if (is_resource($stream)) {
                fclose($stream);
            }

            return Storage::disk($target)->exists($path);
        } catch (\Throwable $e) {
            Log::warning('فشل ترحيل ماتريال إلى Cloudflare R2: '.$e->getMessage(), ['path' => $path]);

            return false;
        }
    }

    private static function normalizeMime(string $contentType, string $ext): string
    {
        $mime = strtolower(trim(explode(';', $contentType)[0]));
        $fromExt = match ($ext) {
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docm', 'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'ppt' => 'application/vnd.ms-powerpoint',
            'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'zip' => 'application/zip',
            'rar' => 'application/vnd.rar',
            'txt' => 'text/plain',
            'html', 'htm' => 'text/html',
            'png' => 'image/png',
            'jpg', 'jpeg' => 'image/jpeg',
            'webp' => 'image/webp',
            'mp3' => 'audio/mpeg',
            'mp4' => 'video/mp4',
            default => 'application/octet-stream',
        };

        if ($mime === '' || $mime === 'application/octet-stream') {
            return $fromExt;
        }

        return $mime;
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
