<?php

namespace App\Services;

use App\Models\ConsultationRequest;
use App\Models\OneToOneSession;
use App\Models\OneToOneWeeklyAvailability;
use App\Support\AppTimezone;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class OneToOneAvailabilityService
{
    public static function dayLabels(): array
    {
        return [
            1 => 'الإثنين',
            2 => 'الثلاثاء',
            3 => 'الأربعاء',
            4 => 'الخميس',
            5 => 'الجمعة',
            6 => 'السبت',
            7 => 'الأحد',
        ];
    }

    public static function rulesForInstructor(int $instructorId): Collection
    {
        if (! Schema::hasTable('one_to_one_weekly_availability')) {
            return collect();
        }

        return OneToOneWeeklyAvailability::query()
            ->where('instructor_id', $instructorId)
            ->where('is_active', true)
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();
    }

    /**
     * @param  array<int, array{day_of_week:int,start_time:string,end_time:string,slot_duration_minutes?:int}>  $rows
     */
    public static function syncRules(int $instructorId, array $rows): void
    {
        OneToOneWeeklyAvailability::query()->where('instructor_id', $instructorId)->delete();

        foreach ($rows as $row) {
            $day = (int) ($row['day_of_week'] ?? 0);
            $start = trim((string) ($row['start_time'] ?? ''));
            $end = trim((string) ($row['end_time'] ?? ''));
            if ($day < 1 || $day > 7 || $start === '' || $end === '') {
                continue;
            }

            $default = (int) config('private_lessons.lesson_duration_minutes', 50);
            $duration = max(30, min(180, (int) ($row['slot_duration_minutes'] ?? $default)));
            if ($start >= $end) {
                continue;
            }

            OneToOneWeeklyAvailability::create([
                'instructor_id' => $instructorId,
                'day_of_week' => $day,
                'start_time' => strlen($start) === 5 ? $start.':00' : $start,
                'end_time' => strlen($end) === 5 ? $end.':00' : $end,
                'slot_duration_minutes' => $duration,
                'is_active' => true,
            ]);
        }
    }

    /**
     * Weekly H:i rules are interpreted in academy timezone (Egypt), returned as UTC instants.
     *
     * @return Collection<int, array{starts_at: Carbon, ends_at: Carbon, label: string}>
     */
    public static function availableSlots(
        int $instructorId,
        Carbon $from,
        Carbon $to,
        ?int $durationMinutes = null,
        ?int $excludeSessionId = null
    ): Collection {
        $durationMinutes = $durationMinutes ?? (int) config('private_lessons.lesson_duration_minutes', 50);
        $rules = self::rulesForInstructor($instructorId);
        if ($rules->isEmpty()) {
            return collect();
        }

        $academy = AppTimezone::academy();
        $slots = collect();
        $cursor = $from->copy()->timezone($academy)->startOfDay();
        $endDay = $to->copy()->timezone($academy)->endOfDay();

        while ($cursor->lte($endDay)) {
            $dayRules = $rules->where('day_of_week', $cursor->isoWeekday());
            foreach ($dayRules as $rule) {
                $startStr = is_string($rule->start_time) ? substr($rule->start_time, 0, 5) : $rule->start_time->format('H:i');
                $endStr = is_string($rule->end_time) ? substr($rule->end_time, 0, 5) : $rule->end_time->format('H:i');
                $slotDuration = (int) ($rule->slot_duration_minutes ?: $durationMinutes);

                $windowStart = AppTimezone::wallClockToUtc($cursor->toDateString(), $startStr, $academy);
                $windowEnd = AppTimezone::wallClockToUtc($cursor->toDateString(), $endStr, $academy);
                $slotStart = $windowStart->copy();

                while ($slotStart->copy()->addMinutes($slotDuration)->lte($windowEnd)) {
                    $slotEnd = $slotStart->copy()->addMinutes($slotDuration);
                    if ($slotStart->gte($from) && $slotStart->gt(now()) && $slotEnd->lte($to)) {
                        if (! self::hasConflict($instructorId, $slotStart, $slotEnd, $excludeSessionId)) {
                            $slots->push([
                                'starts_at' => $slotStart->copy()->utc(),
                                'ends_at' => $slotEnd->copy()->utc(),
                                'label' => $slotStart->copy()->timezone($academy)->format('Y-m-d H:i'),
                            ]);
                        }
                    }
                    $slotStart->addMinutes($slotDuration);
                }
            }
            $cursor->addDay();
        }

        return $slots->sortBy(fn ($s) => $s['starts_at']->timestamp)->values();
    }

    public static function isSlotAvailable(
        int $instructorId,
        Carbon $startsAt,
        int $durationMinutes,
        ?int $excludeSessionId = null
    ): bool {
        $endsAt = $startsAt->copy()->addMinutes($durationMinutes);

        if ($startsAt->lte(now())) {
            return false;
        }

        if (self::hasConflict($instructorId, $startsAt, $endsAt, $excludeSessionId)) {
            return false;
        }

        $rules = self::rulesForInstructor($instructorId);
        if ($rules->isEmpty()) {
            return false;
        }

        $academy = AppTimezone::academy();
        $localStart = $startsAt->copy()->timezone($academy);
        $localEnd = $endsAt->copy()->timezone($academy);

        $dayRules = $rules->where('day_of_week', $localStart->isoWeekday());
        foreach ($dayRules as $rule) {
            $startStr = is_string($rule->start_time) ? substr($rule->start_time, 0, 5) : $rule->start_time->format('H:i');
            $endStr = is_string($rule->end_time) ? substr($rule->end_time, 0, 5) : $rule->end_time->format('H:i');
            $windowStart = AppTimezone::wallClockToUtc($localStart->toDateString(), $startStr, $academy);
            $windowEnd = AppTimezone::wallClockToUtc($localStart->toDateString(), $endStr, $academy);

            if ($startsAt->copy()->utc()->gte($windowStart) && $endsAt->copy()->utc()->lte($windowEnd)) {
                return true;
            }
        }

        return false;
    }

    public static function hasConflict(
        int $instructorId,
        Carbon $startsAt,
        Carbon $endsAt,
        ?int $excludeSessionId = null
    ): bool {
        $sessionQuery = OneToOneSession::query()
            ->where('instructor_id', $instructorId)
            ->where('status', OneToOneSession::STATUS_SCHEDULED)
            ->whereNotNull('scheduled_at');

        if ($excludeSessionId) {
            $sessionQuery->where('id', '!=', $excludeSessionId);
        }

        foreach ($sessionQuery->get() as $session) {
            $existingStart = $session->scheduled_at;
            $existingEnd = $existingStart->copy()->addMinutes((int) ($session->duration_minutes ?? 60));
            if ($startsAt->lt($existingEnd) && $endsAt->gt($existingStart)) {
                return true;
            }
        }

        if (Schema::hasTable('consultation_requests')) {
            $consultations = ConsultationRequest::query()
                ->where('instructor_id', $instructorId)
                ->where('status', ConsultationRequest::STATUS_SCHEDULED)
                ->whereNotNull('scheduled_at')
                ->get();

            foreach ($consultations as $consultation) {
                $existingStart = $consultation->scheduled_at;
                $existingEnd = $existingStart->copy()->addMinutes((int) ($consultation->duration_minutes ?? 60));
                if ($startsAt->lt($existingEnd) && $endsAt->gt($existingStart)) {
                    return true;
                }
            }
        }

        return false;
    }
}
