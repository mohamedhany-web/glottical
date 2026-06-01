<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

/**
 * إعدادات وسائط المنصة — يمكن ضبط رابط R2 العام من .env أو لوحة التحكم (settings).
 */
class PlatformMediaSettings
{
    public const SETTING_R2_PUBLIC_URL = 'r2_public_url';

    private const CACHE_KEY = 'platform_media_settings_v1';

    public static function forgetCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * قاعدة URL عامة لـ R2 (r2.dev) — ليس cloudflarestorage.com.
     */
    public static function r2PublicBaseUrl(): ?string
    {
        return self::payload()['r2_public_base'];
    }

    /** @return array{r2_public_base: ?string} */
    private static function payload(): array
    {
        return Cache::remember(self::CACHE_KEY, 300, function () {
            $candidates = [
                Setting::getValue(self::SETTING_R2_PUBLIC_URL),
                config('filesystems.r2_public_url'),
                env('R2_PUBLIC_URL'),
                env('AWS_URL'),
            ];

            foreach ($candidates as $base) {
                if (! is_string($base) || trim($base) === '') {
                    continue;
                }

                $base = rtrim(trim($base), '/');

                if (str_contains($base, 'cloudflarestorage.com')) {
                    continue;
                }

                return ['r2_public_base' => $base];
            }

            return ['r2_public_base' => null];
        });
    }
}
