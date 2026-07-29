<?php

namespace App\Services;

use App\Models\ClassroomMeeting;
use App\Models\Notification;
use App\Models\StudentTutoringSubscription;
use App\Models\TutoringGroupBooking;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use InvalidArgumentException;

class TutoringGroupOrchestrationService
{
    public static function confirmBooking(TutoringGroupBooking $booking): TutoringGroupBooking
    {
        return DB::transaction(function () use ($booking) {
            $booking = TutoringGroupBooking::query()
                ->with(['tutoringGroup', 'user', 'instructor', 'classroomMeeting', 'cohort', 'subscription'])
                ->lockForUpdate()
                ->findOrFail($booking->id);

            if ($booking->status === TutoringGroupBooking::STATUS_CANCELLED) {
                throw new InvalidArgumentException('لا يمكن تأكيد حجز ملغي.');
            }

            if ($booking->status === TutoringGroupBooking::STATUS_COMPLETED) {
                throw new InvalidArgumentException('هذا الحجز مكتمل مسبقاً.');
            }

            if ($booking->cohort_id && $booking->status === TutoringGroupBooking::STATUS_PENDING) {
                TutoringCohortService::enroll($booking->cohort);
            }

            $meeting = $booking->classroomMeeting;
            if (! $meeting) {
                $meeting = self::createMeetingForBooking($booking);
            }

            $booking->update([
                'status' => TutoringGroupBooking::STATUS_CONFIRMED,
                'classroom_meeting_id' => $meeting->id,
            ]);

            $meeting->update(['tutoring_group_booking_id' => $booking->id]);

            self::notifyConfirmed($booking->fresh(['tutoringGroup', 'user', 'instructor', 'classroomMeeting']));

            return $booking->fresh(['tutoringGroup', 'user', 'instructor', 'classroomMeeting', 'cohort']);
        });
    }

    public static function cancelBooking(TutoringGroupBooking $booking, ?string $reason = null): TutoringGroupBooking
    {
        return DB::transaction(function () use ($booking, $reason) {
            $booking = TutoringGroupBooking::query()
                ->with(['cohort', 'classroomMeeting'])
                ->lockForUpdate()
                ->findOrFail($booking->id);

            $wasConfirmed = $booking->status === TutoringGroupBooking::STATUS_CONFIRMED;

            if ($wasConfirmed && $booking->cohort_id) {
                TutoringCohortService::releaseSeat($booking->cohort);
            }

            if ($booking->classroomMeeting && ! $booking->classroomMeeting->ended_at) {
                $booking->classroomMeeting->update(['ended_at' => now()]);
            }

            $notes = $booking->admin_notes;
            if ($reason) {
                $notes = trim(($notes ? $notes."\n" : '').'إلغاء: '.$reason);
            }

            $booking->update([
                'status' => TutoringGroupBooking::STATUS_CANCELLED,
                'admin_notes' => $notes,
            ]);

            if ($booking->user_id) {
                Notification::create([
                    'user_id' => $booking->user_id,
                    'sender_id' => null,
                    'title' => 'تم إلغاء حجز المجموعة',
                    'message' => 'تم إلغاء حجزك بتاريخ '.$booking->starts_at?->format('Y-m-d H:i'),
                    'type' => 'general',
                    'priority' => 'normal',
                    'audience' => 'student',
                ]);
            }

            return $booking->fresh();
        });
    }

    public static function completeBooking(TutoringGroupBooking $booking): TutoringGroupBooking
    {
        return DB::transaction(function () use ($booking) {
            $booking = TutoringGroupBooking::query()
                ->with(['subscription'])
                ->lockForUpdate()
                ->findOrFail($booking->id);

            if ($booking->status !== TutoringGroupBooking::STATUS_CONFIRMED) {
                throw new InvalidArgumentException('لا يكتمل إلا الحجز المؤكد.');
            }

            $booking->update(['status' => TutoringGroupBooking::STATUS_COMPLETED]);

            if ($booking->student_tutoring_subscription_id) {
                $sub = StudentTutoringSubscription::query()->lockForUpdate()->find($booking->student_tutoring_subscription_id);
                if ($sub) {
                    $sub->sessions_used = min((int) $sub->sessions_total, (int) $sub->sessions_used + 1);
                    if ($sub->sessions_used >= (int) $sub->sessions_total) {
                        $sub->status = StudentTutoringSubscription::STATUS_EXPIRED;
                    }
                    $sub->save();
                }
            }

            return $booking->fresh(['subscription']);
        });
    }

    protected static function createMeetingForBooking(TutoringGroupBooking $booking): ClassroomMeeting
    {
        $group = $booking->tutoringGroup;
        $isCollective = $group?->isCollective() ?? false;
        $capacity = $isCollective
            ? max(2, (int) ($booking->cohort?->capacity ?? $group->capacity ?? 8))
            : 4;

        $title = ($group?->title ?? 'مجموعة').' — '.$booking->starts_at?->format('Y-m-d H:i');

        return ClassroomMeeting::create([
            'user_id' => $booking->instructor_id,
            'tutoring_group_booking_id' => $booking->id,
            'code' => ClassroomMeeting::generateCode(),
            'room_name' => 'tutoring-'.$booking->id.'-'.Str::lower(Str::random(6)),
            'title' => $title,
            'scheduled_for' => $booking->starts_at,
            'planned_duration_minutes' => max(30, (int) ($booking->starts_at && $booking->ends_at
                ? $booking->starts_at->diffInMinutes($booking->ends_at)
                : ($group->duration_minutes ?? 60))),
            'max_participants' => $capacity,
        ]);
    }

    protected static function notifyConfirmed(TutoringGroupBooking $booking): void
    {
        $joinUrl = $booking->joinUrl() ?: '';
        $when = $booking->starts_at?->format('Y-m-d H:i') ?? '';

        if ($booking->user_id) {
            Notification::create([
                'user_id' => $booking->user_id,
                'sender_id' => null,
                'title' => 'تم تأكيد حجز المجموعة',
                'message' => 'الموعد: '.$when.($joinUrl ? ' — رابط الدخول: '.$joinUrl : ''),
                'type' => 'reminder',
                'priority' => 'high',
                'audience' => 'student',
                'action_url' => Route::has('student.tutoring-bookings.show')
                    ? route('student.tutoring-bookings.show', $booking)
                    : $joinUrl,
                'action_text' => 'تفاصيل الحجز',
            ]);
        }

        if ($booking->instructor_id) {
            Notification::create([
                'user_id' => $booking->instructor_id,
                'sender_id' => null,
                'title' => 'حجز مجموعة مؤكد',
                'message' => ($booking->contactName()).' — '.$when,
                'type' => 'reminder',
                'priority' => 'normal',
                'audience' => 'instructor',
                'action_url' => Route::has('instructor.tutoring-bookings.show')
                    ? route('instructor.tutoring-bookings.show', $booking)
                    : $joinUrl,
                'action_text' => 'عرض الحجز',
            ]);
        }
    }
}
