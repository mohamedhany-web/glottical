<?php

namespace App\Services;

use App\Models\ConsultationRequest;
use App\Models\OneToOneSession;
use App\Models\OneToOneWeeklyAvailability;
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

            $duration = max(30, min(180, (int) ($row['slot_duration_minutes'] ?? 60)));
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
     * @return Collection<int, array{starts_at: Carbon, ends_at: Carbon, label: string}>
     */
    public static function availableSlots(
        int $instructorId,
        Carbon $from,
        Carbon $to,
        int $durationMinutes = 60,
        ?int $excludeSessionId = null
    ): Collection {
        $rules = self::rulesForInstructor($instructorId);
        if ($rules->isEmpty()) {
            return collect();
        }

        $slots = collect();
        $cursor = $from->copy()->startOfDay();
        $endDay = $to->copy()->endOfDay();

        while ($cursor->lte($endDay)) {
            $dayRules = $rules->where('day_of_week', $cursor->isoWeekday());
            foreach ($dayRules as $rule) {
                $startStr = is_string($rule->start_time) ? substr($rule->start_time, 0, 5) : $rule->start_time->format('H:i');
                $endStr = is_string($rule->end_time) ? substr($rule->end_time, 0, 5) : $rule->end_time->format('H:i');
                $slotDuration = (int) ($rule->slot_duration_minutes ?: $durationMinutes);

                $windowStart = $cursor->copy()->setTimeFromTimeString($startStr);
                $windowEnd = $cursor->copy()->setTimeFromTimeString($endStr);
                $slotStart = $windowStart->copy();

                while ($slotStart->copy()->addMinutes($slotDuration)->lte($windowEnd)) {
                    $slotEnd = $slotStart->copy()->addMinutes($slotDuration);
                    if ($slotStart->gte($from) && $slotStart->gt(now()) && $slotEnd->lte($to)) {
                        if (! self::hasConflict($instructorId, $slotStart, $slotEnd, $excludeSessionId)) {
                            $slots->push([
                                'starts_at' => $slotStart->copy(),
                                'ends_at' => $slotEnd->copy(),
                                'label' => $slotStart->format('Y-m-d H:i'),
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

        $dayRules = $rules->where('day_of_week', $startsAt->isoWeekday());
        foreach ($dayRules as $rule) {
            $startStr = is_string($rule->start_time) ? substr($rule->start_time, 0, 5) : $rule->start_time->format('H:i');
            $endStr = is_string($rule->end_time) ? substr($rule->end_time, 0, 5) : $rule->end_time->format('H:i');
            $windowStart = $startsAt->copy()->setTimeFromTimeString($startStr);
            $windowEnd = $startsAt->copy()->setTimeFromTimeString($endStr);

            if ($startsAt->gte($windowStart) && $endsAt->lte($windowEnd)) {
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
