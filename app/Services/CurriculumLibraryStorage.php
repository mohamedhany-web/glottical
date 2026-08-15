<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * تخزين مواد المناهج التفاعلية: يفضّل Cloudflare R2، ويسقط إلى public إن لم تكتمل المفاتيح.
 *
 * .env: AWS_ACCESS_KEY_ID, AWS_SECRET_ACCESS_KEY, AWS_BUCKET, AWS_ENDPOINT
 * اختياري: CURRICULUM_LIBRARY_DISK=r2|public
 */
class CurriculumLibraryStorage
{
    public static function missingR2Fields(): array
    {
        $cfg = config('filesystems.disks.r2', []);
        $missing = [];
        foreach (['key' => 'AWS_ACCESS_KEY_ID', 'secret' => 'AWS_SECRET_ACCESS_KEY', 'bucket' => 'AWS_BUCKET', 'endpoint' => 'AWS_ENDPOINT'] as $field => $env) {
            if (empty($cfg[$field])) {
                $missing[] = $env;
            }
        }

        return $missing;
    }

    public static function isR2Ready(): bool
    {
        return self::missingR2Fields() === [];
    }

    /**
     * القرص الفعلي للرفع الجديد.
     */
    public static function resolvedDisk(): string
    {
        $preferred = strtolower(trim((string) env('CURRICULUM_LIBRARY_DISK', 'r2')));
        if ($preferred === '' || $preferred === '0') {
            $preferred = 'r2';
        }

        if ($preferred === 'r2') {
            if (self::isR2Ready()) {
                return 'r2';
            }

            Log::warning('Curriculum library: R2 not configured; falling back to public disk.', [
                'missing' => self::missingR2Fields(),
            ]);

            return 'public';
        }

        if (in_array($preferred, ['public', 'local'], true)) {
            return $preferred === 'local' ? 'local' : 'public';
        }

        return self::isR2Ready() ? 'r2' : 'public';
    }

    /**
     * الرفع المباشر (presign/multipart) يعمل فقط مع R2 جاهز.
     */
    public static function supportsDirectUpload(): bool
    {
        if (! self::isR2Ready()) {
            return false;
        }

        try {
            $disk = Storage::disk('r2');

            return $disk instanceof \Illuminate\Filesystem\AwsS3V3Adapter;
        } catch (\Throwable) {
            return false;
        }
    }

    public static function adminStatusMessage(): ?string
    {
        if (self::isR2Ready()) {
            return null;
        }

        $missing = implode('، ', self::missingR2Fields());

        return 'إعدادات Cloudflare R2 غير مكتملة (ناقص: '.$missing.'). الرفع يتم حالياً على التخزين المحلي للخادم. أضف المفاتيح في .env ثم نفّذ php artisan config:clear وphp artisan curriculum:r2-smoke.';
    }

    public static function uploadFailureHint(\Throwable $e): string
    {
        if (! self::isR2Ready()) {
            return 'تعذّر الرفع لأن Cloudflare R2 غير مضبوط. أضف AWS_ACCESS_KEY_ID وAWS_SECRET_ACCESS_KEY وAWS_BUCKET وAWS_ENDPOINT في .env على السيرفر ثم config:clear.';
        }

        $msg = $e->getMessage();
        if (stripos($msg, 'Could not resolve host') !== false || stripos($msg, 'cURL error') !== false) {
            return 'تعذّر الاتصال بـ Cloudflare R2. راجع AWS_ENDPOINT والاتصال بالإنترنت من السيرفر.';
        }
        if (stripos($msg, 'Access Denied') !== false || stripos($msg, 'InvalidAccessKeyId') !== false || stripos($msg, 'SignatureDoesNotMatch') !== false) {
            return 'مفاتيح Cloudflare R2 غير صحيحة. راجع AWS_ACCESS_KEY_ID وAWS_SECRET_ACCESS_KEY.';
        }

        return 'تعذّر رفع الملف إلى التخزين. تحقّق من الإعدادات ثم أعد المحاولة. ('.$msg.')';
    }
}
