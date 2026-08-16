<?php

namespace App\Support;

use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

/**
 * نوافذ الجدول الأسبوعي للمعلم — تطبيع الوقت ومنع إسقاط المواعيد بصمت.
 */
class WeeklyScheduleTime
{
    public const TIME_REGEX = '/^\d{1,2}:\d{2}(?::\d{2})?$/';

    public const MAX_WINDOWS = 48;

    /**
     * @return array<string, mixed>
     */
    public static function slotTimeRules(int $maxDuration, bool $withAppliesTo = false): array
    {
        $rules = [
            'slots' => ['nullable', 'array', 'max:'.self::MAX_WINDOWS],
            'slots.*.day_of_week' => ['required', 'integer', 'between:1,7'],
            'slots.*.start_time' => ['required', 'string', 'regex:'.self::TIME_REGEX],
            'slots.*.end_time' => ['required', 'string', 'regex:'.self::TIME_REGEX],
            'slots.*.slot_duration_minutes' => ['nullable', 'integer', 'min:30', 'max:'.$maxDuration],
        ];

        if ($withAppliesTo) {
            $rules['slots.*.applies_to'] = ['nullable', 'in:individual,collective,both'];
            $rules['slots.*.note'] = ['nullable', 'string', 'max:255'];
        }

        return $rules;
    }

    public static function normalize(?string $value): ?string
    {
        $value = trim((string) $value);
        if ($value === '' || ! preg_match(self::TIME_REGEX, $value, $m)) {
            return null;
        }

        $parts = array_map('intval', explode(':', $value));
        $hour = $parts[0] ?? -1;
        $minute = $parts[1] ?? -1;
        if ($hour > 23 || $minute > 59) {
            return null;
        }

        return sprintf('%02d:%02d', $hour, $minute);
    }

    public static function toMinutes(string $hi, bool $midnightAsEndOfDay = false): int
    {
        [$hour, $minute] = array_map('intval', explode(':', $hi));
        $mins = ($hour * 60) + $minute;
        if ($midnightAsEndOfDay && $mins === 0) {
            return 24 * 60;
        }

        return $mins;
    }

    public static function slotCount(string $start, string $end, int $duration): int
    {
        $startMins = self::toMinutes($start);
        $endMins = self::toMinutes($end, $start !== '00:00');
        if ($endMins <= $startMins || $duration < 1) {
            return 0;
        }

        return (int) floor(($endMins - $startMins) / $duration);
    }

    /**
     * نهاية النافذة بتوقيت UTC. 00:00 بعد بداية لاحقة = منتصف ليل اليوم التالي.
     */
    public static function windowEndUtc(string $dateYmd, string $startHi, string $endHi, ?string $timezone = null): Carbon
    {
        $startHi = self::normalize($startHi) ?? $startHi;
        $endHi = self::normalize($endHi) ?? $endHi;
        $end = AppTimezone::wallClockToUtc($dateYmd, $endHi, $timezone);
        if ($endHi === '00:00' && $startHi !== '00:00') {
            return $end->addDay();
        }

        return $end;
    }

    /**
     * @param  array<int, array<string, mixed>>  $rows
     * @return array<int, array<string, mixed>>
     */
    public static function normalizeRows(array $rows, int $maxDuration, int $defaultDuration = 50): array
    {
        $clean = [];
        $errors = [];

        foreach (array_values($rows) as $i => $row) {
            $start = self::normalize((string) ($row['start_time'] ?? ''));
            $end = self::normalize((string) ($row['end_time'] ?? ''));
            $day = (int) ($row['day_of_week'] ?? 0);
            $duration = (int) ($row['slot_duration_minutes'] ?? $defaultDuration);
            $duration = max(30, min($maxDuration, $duration > 0 ? $duration : $defaultDuration));

            if ($day < 1 || $day > 7) {
                $errors["slots.$i.day_of_week"] = 'اختر يوماً صحيحاً.';
            }
            if ($start === null) {
                $errors["slots.$i.start_time"] = 'وقت البداية غير صالح.';
            }
            if ($end === null) {
                $errors["slots.$i.end_time"] = 'وقت النهاية غير صالح.';
            }
            if ($start !== null && $end !== null) {
                $yield = self::slotCount($start, $end, $duration);
                if ($yield < 1) {
                    $errors["slots.$i.end_time"] = 'هذه النافذة لا تتسع لموعد كامل. مدّد وقت «إلى» ليكون بعد البداية وبما يغطي مدة الحصة (حتى منتصف الليل استخدم 00:00).';
                }
            }

            if (isset($errors["slots.$i.day_of_week"]) || isset($errors["slots.$i.start_time"]) || isset($errors["slots.$i.end_time"])) {
                continue;
            }

            $clean[] = [
                'day_of_week' => $day,
                'start_time' => $start,
                'end_time' => $end,
                'slot_duration_minutes' => $duration,
                'applies_to' => $row['applies_to'] ?? 'both',
                'note' => $row['note'] ?? null,
            ];
        }

        if ($errors !== []) {
            throw ValidationException::withMessages($errors);
        }

        return $clean;
    }
}
