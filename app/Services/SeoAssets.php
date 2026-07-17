<?php

namespace App\Services;

/**
 * أصول وصور SEO المشتركة (OG / JSON-LD).
 */
class SeoAssets
{
    public static function ogImageUrl(): string
    {
        $configured = config('app.og_image_url');
        if (is_string($configured) && $configured !== '') {
            return $configured;
        }

        $local = public_path('images/og-image.jpg');
        if (is_file($local)) {
            return asset('images/og-image.jpg');
        }

        $logo = AdminPanelBranding::logoPublicUrl();
        if (is_string($logo) && $logo !== '') {
            return $logo;
        }

        return asset('images/og-image.jpg');
    }

    /**
     * تصغير روابط Unsplash لتقليل حجم LCP.
     */
    public static function optimizedRemoteImage(?string $url, int $width = 1600, int $quality = 72): ?string
    {
        if ($url === null || $url === '') {
            return $url;
        }

        if (! str_contains($url, 'images.unsplash.com')) {
            return $url;
        }

        $parts = parse_url($url);
        if ($parts === false || empty($parts['scheme']) || empty($parts['host'])) {
            return $url;
        }

        parse_str($parts['query'] ?? '', $query);
        $query['auto'] = $query['auto'] ?? 'format';
        $query['fit'] = $query['fit'] ?? 'crop';
        $query['w'] = (string) $width;
        $query['q'] = (string) $quality;

        $base = $parts['scheme'].'://'.$parts['host'].($parts['path'] ?? '');

        return $base.'?'.http_build_query($query);
    }
}
