<?php

namespace App\Support;

/**
 * تصنيفات المكتبة الآمنة — كتب، عروض، HTML، ألعاب، أطفال، إسلامي.
 */
class FamilyLibraryThemes
{
    public const GENERAL = 'general';

    public const BOOKS = 'books';

    public const PRESENTATIONS = 'presentations';

    public const HTML = 'html';

    public const GAMES = 'games';

    public const KIDS = 'kids';

    public const ISLAMIC = 'islamic';

    public const MODE_DOWNLOAD = 'download';

    public const MODE_VIEW = 'view';

    public const MODE_PLAY = 'play';

    /**
     * @return array<string, array{ar:string,en:string,icon:string,tone:string,hint_ar:string,hint_en:string}>
     */
    public static function all(): array
    {
        return [
            self::BOOKS => [
                'ar' => 'كتب PDF',
                'en' => 'PDF books',
                'icon' => 'fas fa-book',
                'tone' => 'blue',
                'hint_ar' => 'كتب تعليمية يقرأها الطفل داخل المنصة بأمان.',
                'hint_en' => 'Educational books for safe in-platform reading.',
            ],
            self::PRESENTATIONS => [
                'ar' => 'عروض بوربوينت',
                'en' => 'PowerPoint',
                'icon' => 'fas fa-file-powerpoint',
                'tone' => 'orange',
                'hint_ar' => 'شرائح تفاعلية للشرح والمراجعة.',
                'hint_en' => 'Interactive slides for learning and review.',
            ],
            self::HTML => [
                'ar' => 'محتوى HTML تفاعلي',
                'en' => 'Interactive HTML',
                'icon' => 'fas fa-code',
                'tone' => 'purple',
                'hint_ar' => 'صفحات وأنشطة تفتح داخل المنصة دون الخروج ليوتيوب.',
                'hint_en' => 'Interactive pages that stay inside the platform.',
            ],
            self::GAMES => [
                'ar' => 'ألعاب تعليمية',
                'en' => 'Educational games',
                'icon' => 'fas fa-gamepad',
                'tone' => 'green',
                'hint_ar' => 'ألعاب ممتعة تحت إشراف الأكاديمية.',
                'hint_en' => 'Fun games under academy supervision.',
            ],
            self::KIDS => [
                'ar' => 'فيديوهات أطفال تعليمية',
                'en' => 'Kids learning videos',
                'icon' => 'fas fa-child',
                'tone' => 'pink',
                'hint_ar' => 'فيديوهات تفاعلية آمنة بدل التصفح الحر على يوتيوب.',
                'hint_en' => 'Safe interactive videos instead of open YouTube.',
            ],
            self::ISLAMIC => [
                'ar' => 'مسلسلات إسلامية',
                'en' => 'Islamic series',
                'icon' => 'fas fa-moon',
                'tone' => 'purple',
                'hint_ar' => 'محتوى قيمي وترفيهي هادف داخل المنصة.',
                'hint_en' => 'Values-based series inside the platform.',
            ],
            self::GENERAL => [
                'ar' => 'مكتبة عامة',
                'en' => 'General library',
                'icon' => 'fas fa-layer-group',
                'tone' => 'blue',
                'hint_ar' => 'محتوى أكاديمي متنوع داخل المدرسة التفاعلية.',
                'hint_en' => 'Mixed academy content inside the interactive school.',
            ],
        ];
    }

    public static function labels(string $locale = 'ar'): array
    {
        return collect(self::all())
            ->mapWithKeys(fn ($meta, $key) => [$key => $locale === 'en' ? $meta['en'] : $meta['ar']])
            ->all();
    }

    public static function label(string $theme, string $locale = 'ar'): string
    {
        $all = self::all();
        $meta = $all[$theme] ?? $all[self::GENERAL];

        return $locale === 'en' ? $meta['en'] : $meta['ar'];
    }

    public static function meta(string $theme): array
    {
        return self::all()[$theme] ?? self::all()[self::GENERAL];
    }

    public static function keys(): array
    {
        return array_keys(self::all());
    }

    public static function materialMimes(): string
    {
        return 'pdf,doc,docm,docx,ppt,pptx,xls,xlsx,zip,rar,txt,html,htm,png,jpg,jpeg,webp,mp3,mp4';
    }

    public static function materialAcceptAttr(): string
    {
        return '.pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.zip,.rar,.txt,.html,.htm,.png,.jpg,.jpeg,.webp,.mp3,.mp4';
    }

    public static function detectThemeFromFilename(?string $filename, ?string $fallback = self::GENERAL): string
    {
        $ext = strtolower(pathinfo((string) $filename, PATHINFO_EXTENSION));

        return match ($ext) {
            'pdf' => self::BOOKS,
            'ppt', 'pptx' => self::PRESENTATIONS,
            'html', 'htm' => self::HTML,
            'zip', 'rar' => self::GAMES,
            default => $fallback ?: self::GENERAL,
        };
    }

    public static function detectExperienceMode(?string $filename, ?string $theme = null): string
    {
        $ext = strtolower(pathinfo((string) $filename, PATHINFO_EXTENSION));
        $theme = $theme ?: self::detectThemeFromFilename($filename);

        if ($theme === self::GAMES && in_array($ext, ['html', 'htm'], true)) {
            return self::MODE_PLAY;
        }
        if ($theme === self::GAMES && in_array($ext, ['zip', 'rar'], true)) {
            return self::MODE_DOWNLOAD;
        }
        if (in_array($ext, ['html', 'htm'], true) || $theme === self::HTML) {
            return self::MODE_VIEW;
        }

        return self::MODE_DOWNLOAD;
    }

    public static function isPlayableInPlatform(?string $filename, ?string $mode = null): bool
    {
        $ext = strtolower(pathinfo((string) $filename, PATHINFO_EXTENSION));
        if (in_array($ext, ['html', 'htm'], true)) {
            return true;
        }

        return in_array($mode, [self::MODE_VIEW, self::MODE_PLAY], true) && in_array($ext, ['html', 'htm'], true);
    }
}
