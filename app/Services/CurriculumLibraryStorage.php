<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

/**
 * تخزين مواد المناهج التفاعلية: Cloudflare R2 عند اكتمال المفاتيح.
 *
 * .env: AWS_ACCESS_KEY_ID, AWS_SECRET_ACCESS_KEY, AWS_BUCKET, AWS_ENDPOINT
 * اختياري: CURRICULUM_LIBRARY_DISK=r2|public
 */
class CurriculumLibraryStorage
{
    /**
     * @return list<string>
     */
    public static function missingR2Fields(): array
    {
        return CloudflareR2::missingFields();
    }

    public static function isR2Ready(): bool
    {
        return CloudflareR2::isReady();
    }

    /**
     * القرص الفعلي للرفع الجديد.
     */
    public static function resolvedDisk(): string
    {
        $preferred = (string) config('filesystems.curriculum_library_disk', 'r2');

        return CloudflareR2::resolveDisk($preferred);
    }

    /**
     * الرفع المباشر (presign/multipart) يعتمد على اكتمال مفاتيح R2 فقط.
     * لا نحمّل قرص التخزين هنا حتى لا يعطّل استثناء التهيئة مسار Cloudflare رغم صحة الإعدادات.
     */
    public static function supportsDirectUpload(): bool
    {
        return self::isR2Ready();
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
        if (stripos($msg, 'AccessControlListNotSupported') !== false || stripos($msg, 'The bucket does not allow ACLs') !== false) {
            return 'تم رفض صلاحيات ACL على Cloudflare R2. الرفع يجب أن يتم بدون public-read — راجع إعداد قرص r2.';
        }

        Log::error('Curriculum library upload failed', ['message' => $msg]);

        return 'تعذّر رفع الملف إلى Cloudflare R2. تحقّق من الإعدادات ثم أعد المحاولة. ('.$msg.')';
    }
}
