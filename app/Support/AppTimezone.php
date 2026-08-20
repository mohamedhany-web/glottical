<?php

namespace App\Support;

use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use DateTimeZone;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Throwable;

/**
 * توقيت الأكاديمية (مصر) مقابل توقيت المشاهد — التخزين دائماً UTC.
 */
class AppTimezone
{
    public const ACADEMY_DEFAULT = 'Africa/Cairo';

    public const QUALITY_GOOD = 'good';

    public const QUALITY_CAUTION = 'caution';

    public const QUALITY_POOR = 'poor';

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
            'America/New_York' => 'أمريكا — شرقي / نيويورك (America/New_York)',
            'America/Chicago' => 'أمريكا — وسطي / شيكاغو (America/Chicago)',
            'America/Denver' => 'أمريكا — جبلي / دنفر (America/Denver)',
            'America/Los_Angeles' => 'أمريكا — هادي / لوس أنجلوس (America/Los_Angeles)',
            'America/Phoenix' => 'أمريكا — فينيكس (America/Phoenix)',
            'America/Anchorage' => 'أمريكا — ألاسكا (America/Anchorage)',
            'Pacific/Honolulu' => 'أمريكا — هاواي (Pacific/Honolulu)',
            'America/Toronto' => 'كندا — تورونتو (America/Toronto)',
            'Australia/Sydney' => 'أستراليا — سيدني (Australia/Sydney)',
            'UTC' => 'UTC',
        ];
    }

    /**
     * المناطق الأربع الرئيسية في أمريكا (برج التنسيق الزمني).
     *
     * @return array<int, array{key: string, tz: string, label: string}>
     */
    public static function usMainlandZones(): array
    {
        return [
            ['key' => 'ET', 'tz' => 'America/New_York', 'label' => 'شرقي — نيويورك'],
            ['key' => 'CT', 'tz' => 'America/Chicago', 'label' => 'وسطي — شيكاغو'],
            ['key' => 'MT', 'tz' => 'America/Denver', 'label' => 'جبلي — دنفر'],
            ['key' => 'PT', 'tz' => 'America/Los_Angeles', 'label' => 'الهادي — لوس أنجلوس'],
        ];
    }

    /**
     * ولاية أمريكية → IANA timezone (من أداة التنسيق).
     *
     * @return array<string, string>
     */
    public static function usStates(): array
    {
        return [
            'ألاباما' => 'America/Chicago',
            'ألاسكا' => 'America/Anchorage',
            'أريزونا' => 'America/Phoenix',
            'أركنساس' => 'America/Chicago',
            'كاليفورنيا' => 'America/Los_Angeles',
            'كولورادو' => 'America/Denver',
            'كونيتيكت' => 'America/New_York',
            'ديلاوير' => 'America/New_York',
            'العاصمة واشنطن' => 'America/New_York',
            'فلوريدا' => 'America/New_York',
            'جورجيا' => 'America/New_York',
            'هاواي' => 'Pacific/Honolulu',
            'آيداهو' => 'America/Denver',
            'إلينوي' => 'America/Chicago',
            'إنديانا' => 'America/New_York',
            'آيوا' => 'America/Chicago',
            'كنساس' => 'America/Chicago',
            'كنتاكي' => 'America/New_York',
            'لويزيانا' => 'America/Chicago',
            'مين' => 'America/New_York',
            'ميريلاند' => 'America/New_York',
            'ماساتشوستس' => 'America/New_York',
            'ميشيغان' => 'America/New_York',
            'مينيسوتا' => 'America/Chicago',
            'ميسيسيبي' => 'America/Chicago',
            'ميزوري' => 'America/Chicago',
            'مونتانا' => 'America/Denver',
            'نبراسكا' => 'America/Chicago',
            'نيفادا' => 'America/Los_Angeles',
            'نيوهامبشير' => 'America/New_York',
            'نيوجيرسي' => 'America/New_York',
            'نيومكسيكو' => 'America/Denver',
            'نيويورك' => 'America/New_York',
            'كارولاينا الشمالية' => 'America/New_York',
            'داكوتا الشمالية' => 'America/Chicago',
            'أوهايو' => 'America/New_York',
            'أوكلاهوما' => 'America/Chicago',
            'أوريغون' => 'America/Los_Angeles',
            'بنسلفانيا' => 'America/New_York',
            'رود آيلاند' => 'America/New_York',
            'كارولاينا الجنوبية' => 'America/New_York',
            'داكوتا الجنوبية' => 'America/Chicago',
            'تينيسي' => 'America/Chicago',
            'تكساس' => 'America/Chicago',
            'يوتا' => 'America/Denver',
            'فيرمونت' => 'America/New_York',
            'فيرجينيا' => 'America/New_York',
            'واشنطن' => 'America/Los_Angeles',
            'فيرجينيا الغربية' => 'America/New_York',
            'ويسكونسن' => 'America/Chicago',
            'وايومنغ' => 'America/Denver',
        ];
    }

    public static function timezoneForUsState(?string $state): ?string
    {
        $state = trim((string) $state);
        if ($state === '') {
            return null;
        }

        $map = self::usStates();
        if (isset($map[$state])) {
            return $map[$state];
        }

        foreach ($map as $name => $tz) {
            if (strcasecmp($name, $state) === 0) {
                return $tz;
            }
        }

        return null;
    }

    /**
     * جودة الموعد للطالب حسب ساعته المحلية (من برج التنسيق):
     * good 07–20 · caution 06–07 و 20–22 · poor باقي اليوم.
     */
    public static function slotQualityForHour(int $hour24): string
    {
        $h = (($hour24 % 24) + 24) % 24;
        if ($h >= 7 && $h < 20) {
            return self::QUALITY_GOOD;
        }
        if (($h >= 6 && $h < 7) || ($h >= 20 && $h < 22)) {
            return self::QUALITY_CAUTION;
        }

        return self::QUALITY_POOR;
    }

    public static function slotQuality(?CarbonInterface $datetime, ?string $viewerTimezone = null): string
    {
        if (! $datetime) {
            return self::QUALITY_POOR;
        }

        $tz = self::normalize($viewerTimezone) ?? self::academy();
        $hour = (int) $datetime->copy()->timezone($tz)->format('G');

        return self::slotQualityForHour($hour);
    }

    /**
     * @return array{ar: string, en: string}
     */
    public static function qualityLabels(string $quality): array
    {
        return match ($quality) {
            self::QUALITY_GOOD => ['ar' => 'مناسب', 'en' => 'Good'],
            self::QUALITY_CAUTION => ['ar' => 'حدّي', 'en' => 'Borderline'],
            default => ['ar' => 'غير مناسب', 'en' => 'Poor'],
        };
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

    /**
     * منطقة ساعة المعلم لحجز النوافذ الأسبوعية (fallback: الأكاديمية).
     */
    public static function forInstructorId(int $instructorId): string
    {
        $user = User::query()->find($instructorId);

        return self::forUser($user);
    }

    /**
     * @return array<int, string|\Closure>
     */
    public static function inputRules(bool $required = false): array
    {
        return [
            $required ? 'required' : 'nullable',
            'string',
            'max:64',
            function (string $attribute, mixed $value, \Closure $fail): void {
                if ($value === null || $value === '') {
                    return;
                }
                if (! self::isValid((string) $value)) {
                    $fail('منطقة زمنية غير صالحة.');
                }
            },
        ];
    }

    public static function resolveInput(?string $requested, ?User $user = null): string
    {
        return self::normalize($requested) ?? self::forUser($user);
    }

    public static function persistForUser(User $user, ?string $timezone): string
    {
        $tz = self::resolveInput($timezone, $user);
        if ((string) $user->timezone !== $tz) {
            $user->forceFill(['timezone' => $tz])->save();
        }

        return $tz;
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
     * تسمية مقروءة للمنطقة (مصر — القاهرة) مع الإبقاء على المعرّف إن لم تُعرف.
     */
    public static function label(?string $timezone): string
    {
        $tz = self::normalize($timezone) ?? self::academy();
        $zones = self::commonZones();

        return $zones[$tz] ?? $tz;
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

        return Carbon::parse(str_replace('T', ' ', trim($datetime)), $tz)->utc();
    }

    /**
     * datetime-local (بدون منطقة) أو لحظة مطلقة (ISO مع Z/offset).
     */
    public static function parseAppointmentInput(?string $value, ?string $timezone = null): ?Carbon
    {
        $value = trim((string) $value);
        if ($value === '') {
            return null;
        }

        if (preg_match('/(?:[zZ]|[+-]\d{2}:?\d{2})$/', $value)) {
            return Carbon::parse($value)->utc();
        }

        // قيم القوائم القديمة: "Y-m-d H:i:s" مخزّنة أصلاً كـ UTC (بدون T).
        if (preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $value)) {
            return Carbon::parse($value, 'UTC')->utc();
        }

        return self::parseLocalToUtc($value, $timezone);
    }

    /**
     * قيمة حقل datetime-local بتوقيت المنطقة المختارة.
     */
    public static function datetimeLocalValue(?CarbonInterface $datetime, ?string $timezone = null): string
    {
        if (! $datetime) {
            return '';
        }

        $tz = self::normalize($timezone) ?? self::academy();

        return $datetime->copy()->timezone($tz)->format('Y-m-d\TH:i');
    }

    /**
     * تفسير موعد من النموذج حسب المنطقة المختارة (أو منطقة المستخدم).
     *
     * @param  array<string, mixed>  $validated
     * @return array<string, mixed>
     */
    public static function shiftRequestDateTime(
        Request $request,
        array $validated,
        string $field = 'scheduled_at',
        bool $mustBeFuture = false,
        ?User $fallbackUser = null,
    ): array {
        if (empty($validated[$field])) {
            return $validated;
        }

        $tz = self::resolveInput(
            is_string($request->input('timezone')) ? $request->input('timezone') : null,
            $fallbackUser ?? $request->user()
        );
        $parsed = self::parseAppointmentInput((string) $validated[$field], $tz);
        if (! $parsed) {
            return $validated;
        }

        if ($mustBeFuture && $parsed->lte(now())) {
            throw ValidationException::withMessages([
                $field => 'الموعد يجب أن يكون في المستقبل حسب المنطقة الزمنية المختارة.',
            ]);
        }

        $validated[$field] = $parsed;
        unset($validated['timezone']);

        return $validated;
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
            $secondary = 'بتوقيت '.self::label($viewer).' · '.$academyTime.' بتوقيت مصر';
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
