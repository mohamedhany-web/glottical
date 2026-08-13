<?php

namespace App\Services;

use App\Models\TutorWorkSchedule;
use App\Models\TutoringGroup;
use App\Models\TutoringGroupBooking;
use App\Models\TutoringGroupCohort;
use App\Support\AppTimezone;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use InvalidArgumentException;

class TutoringGroupAvailabilityService
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
        if (! Schema::hasTable('tutor_work_schedules')) {
            return collect();
        }

        return TutorWorkSchedule::query()
            ->where('instructor_id', $instructorId)
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();
    }

    /**
     * @param  array<int, array{day_of_week:int,start_time:string,end_time:string,slot_duration_minutes?:int,applies_to?:string,note?:string|null}>  $slots
     */
    public static function syncRules(int $instructorId, array $slots): void
    {
        DB::transaction(function () use ($instructorId, $slots) {
            TutorWorkSchedule::query()->where('instructor_id', $instructorId)->delete();

            foreach ($slots as $slot) {
                $start = $slot['start_time'];
                $end = $slot['end_time'];
                if ($end <= $start) {
                    continue;
                }

                TutorWorkSchedule::create([
                    'instructor_id' => $instructorId,
                    'day_of_week' => (int) $slot['day_of_week'],
                    'start_time' => $start,
                    'end_time' => $end,
                    'slot_duration_minutes' => (int) ($slot['slot_duration_minutes'] ?? 60),
                    'applies_to' => $slot['applies_to'] ?? TutorWorkSchedule::APPLIES_BOTH,
                    'is_active' => true,
                    'note' => $slot['note'] ?? null,
                ]);
            }
        });
    }

    public static function availableSlots(TutoringGroup $group, ?Carbon $from = null, ?Carbon $to = null): Collection
    {
        if (! Schema::hasTable('tutor_work_schedules') || ! Schema::hasTable('tutoring_group_bookings')) {
            return collect();
        }

        $from = ($from ?? now())->copy();
        $to = ($to ?? now()->addDays(21))->copy();
        $duration = max(30, (int) $group->duration_minutes);
        $capacity = max(1, (int) $group->capacity);

        $rules = TutorWorkSchedule::query()
            ->where('instructor_id', $group->instructor_id)
            ->active()
            ->forType($group->type)
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        if ($rules->isEmpty()) {
            return collect();
        }

        $blocking = TutoringGroupBooking::query()
            ->blocking()
            ->where('instructor_id', $group->instructor_id)
            ->whereBetween('starts_at', [$from->copy()->utc(), $to->copy()->utc()])
            ->with('tutoringGroup:id,type')
            ->get(['id', 'tutoring_group_id', 'starts_at', 'ends_at']);

        $academy = AppTimezone::academy();
        $slots = collect();
        $cursor = $from->copy()->timezone($academy)->startOfDay();
        $endDay = $to->copy()->timezone($academy)->endOfDay();

        while ($cursor->lte($endDay)) {
            $dayRules = $rules->where('day_of_week', $cursor->isoWeekday());
            foreach ($dayRules as $rule) {
                $ruleDuration = (int) ($rule->slot_duration_minutes ?: $duration);
                $windowStart = AppTimezone::wallClockToUtc($cursor->toDateString(), $rule->startTimeString(), $academy);
                $windowEnd = AppTimezone::wallClockToUtc($cursor->toDateString(), $rule->endTimeString(), $academy);
                $slotStart = $windowStart->copy();

                while ($slotStart->copy()->addMinutes($ruleDuration)->lte($windowEnd)) {
                    $slotEnd = $slotStart->copy()->addMinutes($ruleDuration);
                    if ($slotStart->gt(now()->addMinutes(15))) {
                        $overlapping = $blocking->filter(function ($b) use ($slotStart, $slotEnd) {
                            $bStart = Carbon::parse($b->starts_at);
                            $bEnd = Carbon::parse($b->ends_at);

                            return $slotStart->lt($bEnd) && $slotEnd->gt($bStart);
                        });

                        $takenForGroup = $overlapping->where('tutoring_group_id', $group->id)->count();
                        $otherOverlaps = $overlapping->where('tutoring_group_id', '!=', $group->id);

                        if ($group->isIndividual()) {
                            $available = $overlapping->isEmpty();
                            $seatsLeft = $available ? 1 : 0;
                        } else {
                            $instructorBusyElsewhere = $otherOverlaps->isNotEmpty();
                            $available = ! $instructorBusyElsewhere && $takenForGroup < $capacity;
                            $seatsLeft = max(0, $capacity - $takenForGroup);
                        }

                        if ($available) {
                            $local = $slotStart->copy()->timezone($academy);
                            $slots->push([
                                'starts_at' => $slotStart->copy()->utc()->toIso8601String(),
                                'ends_at' => $slotEnd->copy()->utc()->toIso8601String(),
                                'date' => $local->toDateString(),
                                'time' => $local->format('H:i'),
                                'label' => $local->locale(app()->getLocale())->translatedFormat('D d M — H:i'),
                                'duration' => $ruleDuration,
                                'seats_left' => $seatsLeft,
                            ]);
                        }
                    }
                    $slotStart->addMinutes($ruleDuration);
                }
            }
            $cursor->addDay();
        }

        return $slots->values();
    }

    /**
     * @param  array{starts_at:string,guest_name?:string,guest_phone?:string,guest_email?:string,student_notes?:string}  $data
     */
    public static function book(TutoringGroup $group, array $data, ?int $userId = null): TutoringGroupBooking
    {
        if (! $group->is_active) {
            throw new InvalidArgumentException('هذه المجموعة غير متاحة للحجز حالياً.');
        }

        $starts = Carbon::parse($data['starts_at']);
        $allowed = self::availableSlots($group, $starts->copy()->startOfDay(), $starts->copy()->endOfDay())
            ->first(fn ($s) => Carbon::parse($s['starts_at'])->equalTo($starts));

        if (! $allowed) {
            throw new InvalidArgumentException('هذا الموعد لم يعد متاحاً. اختر موعداً آخر.');
        }

        $duration = (int) ($allowed['duration'] ?? $group->duration_minutes);
        $ends = $starts->copy()->addMinutes($duration);

        if (! $userId && empty($data['guest_name'])) {
            throw new InvalidArgumentException('أدخل الاسم للمتابعة.');
        }

        if (! $userId && empty($data['guest_email']) && empty($data['guest_phone'])) {
            throw new InvalidArgumentException('أدخل البريد أو رقم الهاتف للمتابعة.');
        }

        $cohortId = isset($data['cohort_id']) ? (int) $data['cohort_id'] : null;
        if ($cohortId) {
            $cohort = TutoringGroupCohort::query()->find($cohortId);
            if (! $cohort || (int) $cohort->tutoring_group_id !== (int) $group->id) {
                throw new InvalidArgumentException('الدفعة غير صالحة لهذه المجموعة.');
            }
            if (! TutoringCohortService::isEnrollmentOpen($cohort)) {
                throw new InvalidArgumentException('هذه الدفعة غير متاحة للاشتراك حالياً.');
            }
        }

        return DB::transaction(function () use ($group, $data, $userId, $starts, $ends, $cohortId) {
            $booking = TutoringGroupBooking::create([
                'tutoring_group_id' => $group->id,
                'cohort_id' => $cohortId,
                'instructor_id' => $group->instructor_id,
                'user_id' => $userId,
                'guest_name' => $data['guest_name'] ?? null,
                'guest_phone' => $data['guest_phone'] ?? null,
                'guest_email' => $data['guest_email'] ?? null,
                'starts_at' => $starts,
                'ends_at' => $ends,
                'status' => TutoringGroupBooking::STATUS_PENDING,
                'payment_status' => TutoringGroupBooking::PAYMENT_NONE,
                'student_notes' => $data['student_notes'] ?? null,
            ]);

            if ($cohortId) {
                TutoringCohortService::enroll(TutoringGroupCohort::query()->findOrFail($cohortId));
            }

            TutoringCrmHookService::onBookingCreated($booking->fresh(['tutoringGroup', 'user']));

            return $booking;
        });
    }
}
