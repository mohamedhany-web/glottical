<?php

namespace App\Services;

use App\Models\FreeTrialBooking;
use App\Models\FreeTrialWeeklyAvailability;
use App\Support\AppTimezone;
use App\Support\WeeklyScheduleTime;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use InvalidArgumentException;

class FreeTrialBookingService
{
    public const DURATION_MINUTES = 30;

    /**
     * نوافذ التوفر تُفسَّر بتوقيت الأكاديمية (مصر)، وتُعرض للزائر بتوقيته.
     *
     * @return Collection<int, array{
     *     starts_at: Carbon,
     *     ends_at: Carbon,
     *     date: string,
     *     time: string,
     *     time_academy: string,
     *     quality: string,
     *     quality_label: string,
     *     label: string,
     *     duration: int,
     *     viewer_timezone: string,
     *     academy_timezone: string
     * }>
     */
    public static function availableSlots(
        ?Carbon $from = null,
        ?Carbon $to = null,
        ?string $viewerTimezone = null,
    ): Collection {
        if (! Schema::hasTable('free_trial_weekly_availability')) {
            return collect();
        }

        $from = ($from ?? now())->copy();
        $to = ($to ?? now()->addDays(14))->copy()->endOfDay();
        $clockTz = AppTimezone::academy();
        $viewerTz = AppTimezone::normalize($viewerTimezone) ?? $clockTz;

        $rules = FreeTrialWeeklyAvailability::query()
            ->where('is_active', true)
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        if ($rules->isEmpty()) {
            return collect();
        }

        $bookedStarts = FreeTrialBooking::query()
            ->where('status', FreeTrialBooking::STATUS_CONFIRMED)
            ->whereBetween('starts_at', [$from->copy()->utc(), $to->copy()->utc()])
            ->pluck('starts_at')
            ->map(fn ($d) => Carbon::parse($d)->utc()->format('Y-m-d H:i'))
            ->flip();

        $slots = collect();
        $cursor = $from->copy()->timezone($clockTz)->startOfDay();
        $endDay = $to->copy()->timezone($clockTz)->endOfDay();
        $locale = app()->getLocale();

        while ($cursor->lte($endDay)) {
            $dayRules = $rules->where('day_of_week', $cursor->isoWeekday());
            foreach ($dayRules as $rule) {
                $startStr = is_string($rule->start_time) ? substr($rule->start_time, 0, 5) : $rule->start_time->format('H:i');
                $endStr = is_string($rule->end_time) ? substr($rule->end_time, 0, 5) : $rule->end_time->format('H:i');
                $duration = (int) ($rule->slot_duration_minutes ?: self::DURATION_MINUTES);

                $windowStart = AppTimezone::wallClockToUtc($cursor->toDateString(), $startStr, $clockTz);
                $windowEnd = WeeklyScheduleTime::windowEndUtc($cursor->toDateString(), $startStr, $endStr, $clockTz);
                $slotStart = $windowStart->copy();

                while ($slotStart->copy()->addMinutes($duration)->lte($windowEnd)) {
                    $slotEnd = $slotStart->copy()->addMinutes($duration);
                    $key = $slotStart->copy()->utc()->format('Y-m-d H:i');

                    if (
                        $slotStart->gte($from)
                        && $slotStart->gt(now()->addMinutes(15))
                        && $slotEnd->lte($to)
                        && ! $bookedStarts->has($key)
                    ) {
                        $viewerLocal = $slotStart->copy()->timezone($viewerTz);
                        $academyLocal = $slotStart->copy()->timezone($clockTz);
                        $quality = AppTimezone::slotQuality($slotStart, $viewerTz);
                        $qualityLabel = AppTimezone::qualityLabels($quality)[$locale === 'ar' ? 'ar' : 'en'];

                        $slots->push([
                            'starts_at' => $slotStart->copy()->utc(),
                            'ends_at' => $slotEnd->copy()->utc(),
                            'date' => $viewerLocal->toDateString(),
                            'time' => $viewerLocal->format('H:i'),
                            'time_academy' => $academyLocal->format('H:i'),
                            'quality' => $quality,
                            'quality_label' => $qualityLabel,
                            'label' => $viewerLocal->locale($locale)->translatedFormat('D d M — H:i'),
                            'duration' => $duration,
                            'viewer_timezone' => $viewerTz,
                            'academy_timezone' => $clockTz,
                        ]);
                    }
                    $slotStart->addMinutes($duration);
                }
            }
            $cursor->addDay();
        }

        return $slots->values();
    }

    /**
     * @param  array{name:string,email?:string,phone?:string,country_code?:string,goal?:string,starts_at:string,notes?:string,timezone?:string,us_state?:string}  $data
     */
    public static function book(array $data, ?int $userId = null): FreeTrialBooking
    {
        $viewerTz = AppTimezone::normalize($data['timezone'] ?? null)
            ?? AppTimezone::timezoneForUsState($data['us_state'] ?? null)
            ?? AppTimezone::academy();

        $starts = AppTimezone::parseAppointmentInput((string) $data['starts_at'], $viewerTz);
        if (! $starts) {
            throw new InvalidArgumentException('موعد غير صالح.');
        }
        $starts = $starts->utc();

        $duration = self::DURATION_MINUTES;
        $ends = $starts->copy()->addMinutes($duration);

        $allowed = self::availableSlots(
            $starts->copy()->subHour(),
            $starts->copy()->addHour(),
            $viewerTz
        )->contains(fn (array $s) => $s['starts_at']->equalTo($starts));

        if (! $allowed) {
            throw new InvalidArgumentException('هذا الموعد لم يعد متاحاً. اختر موعداً آخر.');
        }

        [$countryCode, $fullPhone] = self::normalizePhone(
            $data['phone'] ?? null,
            $data['country_code'] ?? null
        );

        if (empty($data['email']) && empty($fullPhone)) {
            throw new InvalidArgumentException('أدخل البريد أو رقم الواتساب للمتابعة.');
        }

        $goal = isset($data['goal']) ? trim((string) $data['goal']) : null;
        if ($goal === '') {
            $goal = null;
        }
        if ($goal !== null && ! array_key_exists($goal, FreeTrialBooking::goalOptions())) {
            throw new InvalidArgumentException('الغرض من التعلم غير صالح.');
        }

        $payload = [
            'name' => $data['name'],
            'email' => $data['email'] ?? null,
            'phone' => $fullPhone,
            'country_code' => $countryCode,
            'goal' => $goal,
            'user_id' => $userId,
            'starts_at' => $starts,
            'ends_at' => $ends,
            'duration_minutes' => $duration,
            'status' => FreeTrialBooking::STATUS_CONFIRMED,
            'notes' => $data['notes'] ?? null,
        ];

        if (Schema::hasColumn('free_trial_bookings', 'timezone')) {
            $payload['timezone'] = $viewerTz;
        }
        if (Schema::hasColumn('free_trial_bookings', 'us_state') && ! empty($data['us_state'])) {
            $payload['us_state'] = trim((string) $data['us_state']);
        }

        return FreeTrialBooking::create($payload);
    }

    /**
     * @return array{0:?string,1:?string} [country_code, full_phone]
     */
    private static function normalizePhone(?string $phone, ?string $countryCode): array
    {
        $rawPhone = trim((string) $phone);
        $dial = trim((string) $countryCode);

        if ($rawPhone === '') {
            return [null, null];
        }

        $countries = config('phone_countries.countries', []);
        $country = collect($countries)->firstWhere('dial_code', $dial);

        if ($dial === '' || ! $country) {
            throw new InvalidArgumentException('اختر كود الدولة لرقم الواتساب.');
        }

        $national = preg_replace('/\D+/', '', $rawPhone) ?? '';
        $national = ltrim($national, '0');
        $regex = $country['validation']['regex'] ?? '/^\d{6,15}$/';

        if ($national === '' || ! preg_match($regex, $national)) {
            $example = $country['example'] ?? $country['placeholder'] ?? '';
            throw new InvalidArgumentException(
                $example !== ''
                    ? ('رقم الواتساب غير صحيح لهذه الدولة. مثال: '.$example)
                    : 'رقم الواتساب غير صحيح لهذه الدولة.'
            );
        }

        return [$dial, $dial.$national];
    }
}
