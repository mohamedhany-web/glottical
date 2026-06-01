<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class PublicFooterSettings
{
    public const CACHE_KEY = 'public_footer_payload_v1';

    /** @return array<string, string> */
    public static function defaults(): array
    {
        return [
            'footer_brand_tagline' => 'تعلّم لغة. احصل على وظيفة. ابدأ مستقبلك.',
            'footer_blurb' => 'منصة متخصصة في تعليم الألمانية والإنجليزية المرتبطة بسوق العمل — كورسات عملية، جلسات خاصة، واستشارات مهنية للكول سنتر، الدعم الفني، السياحة، وألمانيا.',
            'footer_email' => 'info@glottical.com',
            'footer_phone' => '01044610507',
            'footer_whatsapp_url' => 'https://wa.me/201044610507',
            'footer_bottom_tagline' => 'اللغة والمهارة المهنية — طريقك إلى سوق العمل',
            'social_facebook_url' => '',
            'social_x_url' => '',
            'social_instagram_url' => '',
            'social_youtube_url' => '',
            'social_linkedin_url' => '',
            'social_tiktok_url' => '',
            'social_telegram_url' => '',
            'social_snapchat_url' => '',
        ];
    }

    /** @return list<string> */
    public static function editableKeys(): array
    {
        return array_keys(self::defaults());
    }

    public static function forgetCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * بيانات الفوتر للواجهة العامة (مدمجة مع القيم الافتراضية).
     *
     * @return array{
     *   brand_tagline: string,
     *   blurb: string,
     *   email: string,
     *   phone: string,
     *   whatsapp_url: string,
     *   bottom_tagline: string,
     *   socials: list<array{url: string, icon: string, label: string}>
     * }
     */
    public static function payload(): array
    {
        return Cache::remember(self::CACHE_KEY, 3600, function () {
            $defaults = self::defaults();
            $stored = Setting::query()
                ->whereIn('key', array_keys($defaults))
                ->pluck('value', 'key')
                ->all();

            $merged = [];
            foreach ($defaults as $key => $default) {
                if (array_key_exists($key, $stored) && $stored[$key] !== null && $stored[$key] !== '') {
                    $merged[$key] = (string) $stored[$key];
                } else {
                    $merged[$key] = $default;
                }
            }

            $socialMap = [
                ['key' => 'social_facebook_url', 'icon' => 'fab fa-facebook-f', 'label' => 'Facebook'],
                ['key' => 'social_x_url', 'icon' => 'fab fa-x-twitter', 'label' => 'X / Twitter'],
                ['key' => 'social_instagram_url', 'icon' => 'fab fa-instagram', 'label' => 'Instagram'],
                ['key' => 'social_youtube_url', 'icon' => 'fab fa-youtube', 'label' => 'YouTube'],
                ['key' => 'social_linkedin_url', 'icon' => 'fab fa-linkedin-in', 'label' => 'LinkedIn'],
                ['key' => 'social_tiktok_url', 'icon' => 'fab fa-tiktok', 'label' => 'TikTok'],
                ['key' => 'social_telegram_url', 'icon' => 'fab fa-telegram-plane', 'label' => 'Telegram'],
                ['key' => 'social_snapchat_url', 'icon' => 'fab fa-snapchat-ghost', 'label' => 'Snapchat'],
            ];

            $socials = [];
            foreach ($socialMap as $meta) {
                $url = trim((string) ($merged[$meta['key']] ?? ''));
                if ($url !== '') {
                    $socials[] = [
                        'url' => $url,
                        'icon' => $meta['icon'],
                        'label' => $meta['label'],
                    ];
                }
            }

            return [
                'brand_tagline' => (string) $merged['footer_brand_tagline'],
                'blurb' => (string) $merged['footer_blurb'],
                'email' => (string) $merged['footer_email'],
                'phone' => (string) $merged['footer_phone'],
                'whatsapp_url' => (string) $merged['footer_whatsapp_url'],
                'bottom_tagline' => (string) $merged['footer_bottom_tagline'],
                'socials' => $socials,
            ];
        });
    }
}
