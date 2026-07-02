<?php

namespace App\Services;

use App\Models\AdvancedCourse;
use App\Models\ClassroomMeeting;
use App\Models\Notification;
use App\Models\OneToOneSession;
use App\Models\StudentCourseEnrollment;
use App\Models\User;
use App\Services\OneToOneAvailabilityService;
use Carbon\Carbon;
use Illuminate\Support\Str;

class OneToOneSessionService
{
    public const SESSIONS_PER_MONTH = 4;

    /**
     * إنشاء حصص أسبوعية (4) عند تفعيل اشتراك كورس فردي 1:1.
     */
    public static function provisionSessionsForEnrollment(StudentCourseEnrollment $enrollment, AdvancedCourse $course): void
    {
        if (! $course->isOneToOne() || ! $course->instructor_id) {
            return;
        }

        if (! CourseSubscriptionService::enrollmentGrantsAccess($enrollment)) {
            return;
        }

        $activeCount = OneToOneSession::query()
            ->where('student_course_enrollment_id', $enrollment->id)
            ->whereIn('status', [OneToOneSession::STATUS_PENDING, OneToOneSession::STATUS_SCHEDULED])
            ->count();

        $toCreate = max(0, self::SESSIONS_PER_MONTH - $activeCount);
        if ($toCreate === 0) {
            return;
        }

        $maxNumber = (int) OneToOneSession::query()
            ->where('student_course_enrollment_id', $enrollment->id)
            ->max('session_number');

        for ($i = 1; $i <= $toCreate; $i++) {
            OneToOneSession::create([
                'student_course_enrollment_id' => $enrollment->id,
                'advanced_course_id' => $course->id,
                'instructor_id' => $course->instructor_id,
                'student_id' => $enrollment->user_id,
                'session_number' => $maxNumber + $i,
                'duration_minutes' => 60,
                'status' => OneToOneSession::STATUS_PENDING,
            ]);
        }

        Notification::create([
            'user_id' => $enrollment->user_id,
            'sender_id' => null,
            'title' => 'تم تفعيل حصصك الفردية',
            'message' => 'تم إنشاء '.self::SESSIONS_PER_MONTH.' حصص شهرية مع المعلم. اختر موعداً من جدول المعلم المتاح.',
            'type' => 'general',
            'priority' => 'normal',
            'audience' => 'student',
            'action_url' => route('student.one-to-one-sessions.index'),
            'action_text' => 'عرض الحصص',
        ]);

        Notification::create([
            'user_id' => $course->instructor_id,
            'sender_id' => null,
            'title' => 'طالب جديد في كورس فردي 1:1',
            'message' => 'طالب مشترك في «'.$course->title.'» — يمكنه الحجز من جدولك أو جدولة الحصص يدوياً.',
            'type' => 'general',
            'priority' => 'high',
            'audience' => 'instructor',
            'action_url' => route('instructor.one-to-one-sessions.index'),
            'action_text' => 'جدولة الحصص',
        ]);
    }

    public static function scheduleSession(
        OneToOneSession $session,
        Carbon $scheduledAt,
        int $durationMinutes,
        ?User $scheduledBy = null,
        bool $requireAvailability = true
    ): void {
        if (! in_array($session->status, [OneToOneSession::STATUS_PENDING, OneToOneSession::STATUS_SCHEDULED], true)) {
            throw new \InvalidArgumentException('لا يمكن جدولة هذه الحصة في حالتها الحالية.');
        }

        if ($requireAvailability && ! OneToOneAvailabilityService::isSlotAvailable(
            (int) $session->instructor_id,
            $scheduledAt,
            $durationMinutes,
            $session->status === OneToOneSession::STATUS_SCHEDULED ? $session->id : null
        )) {
            throw new \InvalidArgumentException('هذا الموعد غير متاح — ربما حُجز أو خارج جدول المعلم.');
        }

        $studentName = $session->student->name ?? 'طالب';
        $courseTitle = $session->course->title ?? 'كورس فردي';

        $meeting = ClassroomMeeting::create([
            'user_id' => $session->instructor_id,
            'one_to_one_session_id' => $session->id,
            'code' => ClassroomMeeting::generateCode(),
            'room_name' => 'one-to-one-'.$session->id.'-'.Str::lower(Str::random(6)),
            'title' => 'حصة 1:1: '.$courseTitle.' — '.$studentName,
            'scheduled_for' => $scheduledAt,
            'planned_duration_minutes' => $durationMinutes,
            'max_participants' => 4,
        ]);

        $session->update([
            'status' => OneToOneSession::STATUS_SCHEDULED,
            'scheduled_at' => $scheduledAt,
            'duration_minutes' => $durationMinutes,
            'classroom_meeting_id' => $meeting->id,
            'booked_by_user_id' => $scheduledBy?->id,
        ]);

        $joinUrl = url('classroom/join/'.$meeting->code);
        $when = $scheduledAt->format('Y-m-d H:i');

        Notification::create([
            'user_id' => $session->student_id,
            'sender_id' => $scheduledBy?->id,
            'title' => 'تم جدولة حصتك الفردية',
            'message' => 'موعد الحصة: '.$when.' — رابط الدخول: '.$joinUrl,
            'type' => 'reminder',
            'priority' => 'high',
            'audience' => 'student',
            'action_url' => route('student.one-to-one-sessions.show', $session),
            'action_text' => 'تفاصيل الحصة',
        ]);

        Notification::create([
            'user_id' => $session->instructor_id,
            'sender_id' => $scheduledBy?->id,
            'title' => 'حصة 1:1 مجدولة',
            'message' => 'الطالب: '.$studentName.' — الموعد: '.$when,
            'type' => 'reminder',
            'priority' => 'normal',
            'audience' => 'instructor',
            'action_url' => route('instructor.one-to-one-sessions.show', $session),
            'action_text' => 'تفاصيل الحصة',
        ]);
    }

    public static function markCompleted(OneToOneSession $session): void
    {
        $session->update(['status' => OneToOneSession::STATUS_COMPLETED]);
    }
}
