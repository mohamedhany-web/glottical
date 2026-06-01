<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AdminPanelBranding
{
    public const SETTING_KEY = 'admin_panel_logo_path';

    private const LOGO_URL_CACHE_KEY = 'branding.admin_panel_logo_url_v2';

    /**
     * قرص التخزين: public محلي، أو r2 لـ Cloudflare R2.
     */
    public static function resolvedDisk(): string
    {
        $d = (string) config('filesystems.admin_branding_disk', 'public');

        if ($d === 'r2') {
            $bucket = config('filesystems.disks.r2.bucket');
            $endpoint = config('filesystems.disks.r2.endpoint');
            if (empty($bucket) || empty($endpoint)) {
                Log::warning('ADMIN_BRANDING_DISK=r2 لكن إعدادات R2 غير مكتملة؛ يُستخدم القرص public.');

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

    public static function forgetLogoUrlCache(): void
    {
        Cache::forget(self::LOGO_URL_CACHE_KEY);
    }

    /**
     * رابط عرض الشعار (مُخزَّن مؤقتاً لتسريع كل صفحة).
     */
    public static function logoPublicUrl(): ?string
    {
        return Cache::remember(self::LOGO_URL_CACHE_KEY, 3600, function () {
            $path = Setting::getValue(self::SETTING_KEY);
            if (is_string($path) && $path !== '') {
                $url = self::urlForStoredPath($path);
                if ($url !== null) {
                    return $url;
                }
            }

            $defaultPath = \App\Providers\AppServiceProvider::SITE_LOGO_STORAGE_PATH;
            $defaultUrl = self::urlForStoredPath($defaultPath);
            if ($defaultUrl !== null) {
                return $defaultUrl;
            }

            return self::inlineFallbackDataUri();
        });
    }

    /**
     * شعار احتياطي مضمّن (لا يعتمد على ملف في public/).
     */
    public static function inlineFallbackDataUri(): string
    {
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64">'
            .'<circle cx="32" cy="32" r="32" fill="#F6C945"/>'
            .'<text x="32" y="42" text-anchor="middle" font-family="Arial,sans-serif" font-size="28" font-weight="700" fill="#082B63">G</text>'
            .'</svg>';

        return 'data:image/svg+xml;base64,'.base64_encode($svg);
    }

    /**
     * رابط عرض صالح للمتصفح (R2 عام أو موقّع أو /storage/ بروكسي).
     */
    private static function urlForStoredPath(string $path): ?string
    {
        $path = str_replace('\\', '/', ltrim($path, '/'));

        return PublicStorageUrl::fromPath($path, self::resolvedDisk());
    }

    public static function removeLogo(): void
    {
        $path = Setting::getValue(self::SETTING_KEY);
        if (is_string($path) && $path !== '') {
            self::deletePhysicalFile($path);
        }
        Setting::setValue(self::SETTING_KEY, null);
        self::forgetLogoUrlCache();
    }

    public static function storeLogo(UploadedFile $file): void
    {
        $oldPath = Setting::getValue(self::SETTING_KEY);
        $stored = PublicMediaStorage::store($file, 'site', is_string($oldPath) ? $oldPath : null);

        Setting::setValue(self::SETTING_KEY, $stored);

        if (is_string($oldPath) && $oldPath !== '' && $oldPath !== $stored) {
            self::deletePhysicalFile($oldPath);
        }

        self::forgetLogoUrlCache();
    }

    private static function deletePhysicalFile(string $path): void
    {
        PublicMediaStorage::delete($path);
    }
}
