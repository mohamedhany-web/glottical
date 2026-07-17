<?php

namespace App\Services;

use App\Models\FreeTrialBooking;
use App\Models\FreeTrialWeeklyAvailability;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use InvalidArgumentException;

class FreeTrialBookingService
{
    public const DURATION_MINUTES = 30;

    public static function availableSlots(?Carbon $from = null, ?Carbon $to = null): Collection
    {
        if (! Schema::hasTable('free_trial_weekly_availability')) {
            return collect();
        }

        $from = ($from ?? now())->copy()->startOfDay();
        $to = ($to ?? now()->addDays(14))->copy()->endOfDay();
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
            ->whereBetween('starts_at', [$from, $to])
            ->pluck('starts_at')
            ->map(fn ($d) => Carbon::parse($d)->format('Y-m-d H:i'))
            ->flip();

        $slots = collect();
        $cursor = $from->copy()->startOfDay();

        while ($cursor->lte($to)) {
            $dayRules = $rules->where('day_of_week', $cursor->isoWeekday());
            foreach ($dayRules as $rule) {
                $startStr = is_string($rule->start_time) ? substr($rule->start_time, 0, 5) : $rule->start_time->format('H:i');
                $endStr = is_string($rule->end_time) ? substr($rule->end_time, 0, 5) : $rule->end_time->format('H:i');
                $duration = (int) ($rule->slot_duration_minutes ?: self::DURATION_MINUTES);

                $windowStart = $cursor->copy()->setTimeFromTimeString($startStr);
                $windowEnd = $cursor->copy()->setTimeFromTimeString($endStr);
                $slotStart = $windowStart->copy();

                while ($slotStart->copy()->addMinutes($duration)->lte($windowEnd)) {
                    $slotEnd = $slotStart->copy()->addMinutes($duration);
                    $key = $slotStart->format('Y-m-d H:i');
                    if ($slotStart->gt(now()->addMinutes(15)) && ! $bookedStarts->has($key)) {
                        $slots->push([
                            'starts_at' => $slotStart->toIso8601String(),
                            'ends_at' => $slotEnd->toIso8601String(),
                            'date' => $slotStart->toDateString(),
                            'time' => $slotStart->format('H:i'),
                            'label' => $slotStart->locale(app()->getLocale())->translatedFormat('D d M — H:i'),
                            'duration' => $duration,
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
     * @param  array{name:string,email?:string,phone?:string,goal?:string,starts_at:string}  $data
     */
    public static function book(array $data, ?int $userId = null): FreeTrialBooking
    {
        $starts = Carbon::parse($data['starts_at']);
        $duration = self::DURATION_MINUTES;
        $ends = $starts->copy()->addMinutes($duration);

        $allowed = self::availableSlots($starts->copy()->startOfDay(), $starts->copy()->endOfDay())
            ->contains(fn ($s) => Carbon::parse($s['starts_at'])->equalTo($starts));

        if (! $allowed) {
            throw new InvalidArgumentException('هذا الموعد لم يعد متاحاً. اختر موعداً آخر.');
        }

        if (empty($data['email']) && empty($data['phone'])) {
            throw new InvalidArgumentException('أدخل البريد أو رقم الهاتف للمتابعة.');
        }

        return FreeTrialBooking::create([
            'name' => $data['name'],
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'goal' => $data['goal'] ?? null,
            'user_id' => $userId,
            'starts_at' => $starts,
            'ends_at' => $ends,
            'duration_minutes' => $duration,
            'status' => FreeTrialBooking::STATUS_CONFIRMED,
            'notes' => $data['notes'] ?? null,
        ]);
    }
}
