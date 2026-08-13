<?php

namespace App\Support;

use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use DateTimeZone;
use Throwable;

/**
 * توقيت الأكاديمية (مصر) مقابل توقيت المشاهد — التخزين دائماً UTC.
 */
class AppTimezone
{
    public const ACADEMY_DEFAULT = 'Africa/Cairo';

    /**
     * مناطق شائعة للاختيار في الواجهة.
     *
     * @return array<string, string>
     */
    public static function commonZones(): array
    {
        return [
            'Africa/Cairo' => 'مصر — القاهرة (Africa/Cairo)',
            'Asia/Riyadh' => 'السعودية — الرياض (Asia/Riyadh)',
            'Asia/Dubai' => 'الإمارات — دبي (Asia/Dubai)',
            'Asia/Kuwait' => 'الكويت (Asia/Kuwait)',
            'Asia/Qatar' => 'قطر (Asia/Qatar)',
            'Asia/Bahrain' => 'البحرين (Asia/Bahrain)',
            'Asia/Amman' => 'الأردن — عمّان (Asia/Amman)',
            'Asia/Beirut' => 'لبنان — بيروت (Asia/Beirut)',
            'Africa/Casablanca' => 'المغرب — الدار البيضاء (Africa/Casablanca)',
            'Europe/London' => 'بريطانيا — لندن (Europe/London)',
            'Europe/Paris' => 'فرنسا — باريس (Europe/Paris)',
            'Europe/Berlin' => 'ألمانيا — برلين (Europe/Berlin)',
            'America/New_York' => 'أمريكا — نيويورك (America/New_York)',
            'America/Chicago' => 'أمريكا — شيكاغو (America/Chicago)',
            'America/Denver' => 'أمريكا — دنفر (America/Denver)',
            'America/Los_Angeles' => 'أمريكا — لوس أنجلوس (America/Los_Angeles)',
            'America/Toronto' => 'كندا — تورونتو (America/Toronto)',
            'Australia/Sydney' => 'أستراليا — سيدني (Australia/Sydney)',
            'UTC' => 'UTC',
        ];
    }

    public static function academy(): string
    {
        $tz = (string) config('platform.academy_timezone', self::ACADEMY_DEFAULT);

        return self::normalize($tz) ?? self::ACADEMY_DEFAULT;
    }

    public static function forUser(?User $user = null): string
    {
        if ($user && filled($user->timezone)) {
            $normalized = self::normalize((string) $user->timezone);
            if ($normalized) {
                return $normalized;
            }
        }

        return self::academy();
    }

    public static function normalize(?string $timezone): ?string
    {
        $timezone = trim((string) $timezone);
        if ($timezone === '') {
            return null;
        }

        try {
            new DateTimeZone($timezone);

            return $timezone;
        } catch (Throwable) {
            return null;
        }
    }

    public static function isValid(?string $timezone): bool
    {
        return self::normalize($timezone) !== null;
    }

    /**
     * تفسير تاريخ/وقت محلي في منطقة معيّنة ثم إرجاعه كـ UTC.
     */
    public static function parseLocalToUtc(string|CarbonInterface $datetime, ?string $timezone = null): Carbon
    {
        $tz = self::normalize($timezone) ?? self::academy();

        if ($datetime instanceof CarbonInterface) {
            return $datetime->copy()->timezone($tz)->utc();
        }

        return Carbon::parse($datetime, $tz)->utc();
    }

    /**
     * بناء لحظة من تاريخ + وقت يوم في منطقة (مثل جدول أسبوعي).
     */
    public static function wallClockToUtc(string $dateYmd, string $timeHi, ?string $timezone = null): Carbon
    {
        $tz = self::normalize($timezone) ?? self::academy();
        $timeHi = strlen($timeHi) === 5 ? $timeHi.':00' : $timeHi;

        return Carbon::parse($dateYmd.' '.$timeHi, $tz)->utc();
    }

    public static function formatFor(
        ?CarbonInterface $datetime,
        ?string $timezone = null,
        string $pattern = 'Y-m-d H:i',
        ?string $locale = null,
    ): string {
        if (! $datetime) {
            return '—';
        }

        $tz = self::normalize($timezone) ?? self::academy();
        $dt = $datetime->copy()->timezone($tz);
        if ($locale) {
            $dt = $dt->locale($locale);
        }

        return $dt->translatedFormat($pattern);
    }

    /**
     * تسمية مزدوجة: توقيت المشاهد + توقيت مصر عند الاختلاف.
     *
     * @return array{primary: string, secondary: ?string, viewer_tz: string, academy_tz: string}
     */
    public static function dualLabel(
        ?CarbonInterface $datetime,
        ?string $viewerTimezone = null,
        ?string $locale = null,
        string $pattern = 'D j M · g:i A',
    ): array {
        if (! $datetime) {
            return [
                'primary' => '—',
                'secondary' => null,
                'viewer_tz' => self::academy(),
                'academy_tz' => self::academy(),
            ];
        }

        $viewer = self::normalize($viewerTimezone) ?? self::academy();
        $academy = self::academy();
        $locale = $locale ?: app()->getLocale();

        $primary = self::formatFor($datetime, $viewer, $pattern, $locale);
        $secondary = null;

        if ($viewer !== $academy) {
            $academyTime = self::formatFor($datetime, $academy, $pattern, $locale);
            $secondary = 'بتوقيتك ('.$viewer.') · '.$academyTime.' بتوقيت مصر';
        }

        return [
            'primary' => $primary,
            'secondary' => $secondary,
            'viewer_tz' => $viewer,
            'academy_tz' => $academy,
        ];
    }

    public static function labelHtml(?CarbonInterface $datetime, ?string $viewerTimezone = null, ?string $locale = null): string
    {
        $parts = self::dualLabel($datetime, $viewerTimezone, $locale);
        $html = e($parts['primary']);
        if ($parts['secondary']) {
            $html .= '<span class="block text-xs opacity-70 mt-0.5">'.e($parts['secondary']).'</span>';
        }

        return $html;
    }
}
