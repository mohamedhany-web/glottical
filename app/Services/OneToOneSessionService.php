<?php

namespace App\Services;

use App\Models\AdvancedCourse;
use App\Models\ClassroomMeeting;
use App\Models\Notification;
use App\Models\OneToOneSession;
use App\Models\ServicePackage;
use App\Models\StudentCourseEnrollment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OneToOneSessionService
{
    public const SESSIONS_PER_MONTH = 4;

    /**
     * إنشاء حصص بانتظار الجدولة حسب رصيد الباقة (أو حد أقصى للعرض).
     */
    public static function provisionSessionsForEnrollment(StudentCourseEnrollment $enrollment, AdvancedCourse $course): void
    {
        if (! $course->isOneToOne() || ! $course->instructor_id) {
            return;
        }

        if (! CourseSubscriptionService::enrollmentGrantsAccess($enrollment)) {
            return;
        }

        $unitsLeft = StudentEntitlementService::unitsLeft(
            (int) $enrollment->user_id,
            ServicePackage::SCOPE_PRIVATE_LESSONS
        );

        // Fallback for legacy enrollments without packages: keep previous 4-session behavior
        $target = $unitsLeft > 0
            ? min($unitsLeft, self::SESSIONS_PER_MONTH)
            : self::SESSIONS_PER_MONTH;

        $activeCount = OneToOneSession::query()
            ->where('student_course_enrollment_id', $enrollment->id)
            ->whereIn('status', [OneToOneSession::STATUS_PENDING, OneToOneSession::STATUS_SCHEDULED])
            ->count();

        $toCreate = max(0, $target - $activeCount);
        if ($toCreate === 0) {
            return;
        }

        $maxNumber = (int) OneToOneSession::query()
            ->where('student_course_enrollment_id', $enrollment->id)
            ->max('session_number');

        $entitlement = StudentEntitlementService::availableFor(
            (int) $enrollment->user_id,
            ServicePackage::SCOPE_PRIVATE_LESSONS
        );

        for ($i = 1; $i <= $toCreate; $i++) {
            OneToOneSession::create([
                'student_course_enrollment_id' => $enrollment->id,
                'student_service_entitlement_id' => $entitlement?->id,
                'advanced_course_id' => $course->id,
                'instructor_id' => $course->instructor_id,
                'student_id' => $enrollment->user_id,
                'session_number' => $maxNumber + $i,
                'duration_minutes' => OneToOneSession::defaultDurationMinutes(),
                'status' => OneToOneSession::STATUS_PENDING,
            ]);
        }

        Notification::create([
            'user_id' => $enrollment->user_id,
            'sender_id' => null,
            'title' => 'تم تفعيل حصصك الفردية',
            'message' => $unitsLeft > 0
                ? ('تم تجهيز '.$toCreate.' حصص من رصيد باقتك. اختر موعداً من جدول المعلم.')
                : ('تم إنشاء '.$toCreate.' حصص. اشحن باقتك لاحقاً لإضافة المزيد.'),
            'type' => 'general',
            'priority' => 'normal',
            'audience' => 'student',
            'action_url' => route('student.one-to-one-sessions.index'),
            'action_text' => 'عرض الحصص',
        ]);

        Notification::create([
            'user_id' => $course->instructor_id,
            'sender_id' => null,
            'title' => '🎉 New Student Assigned',
            'message' => ($enrollment->student->name ?? 'Student').' has been assigned to you for private lessons — «'.$course->title.'».',
            'type' => 'general',
            'priority' => 'high',
            'audience' => 'instructor',
            'action_url' => route('instructor.one-to-one-sessions.index'),
            'action_text' => 'View schedule',
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

        // Require private-lessons (or global) credit unless session already linked to entitlement
        $entitlement = $session->entitlement;
        if (! $entitlement || ! $entitlement->hasUnitsLeft()) {
            $entitlement = StudentEntitlementService::availableFor(
                (int) $session->student_id,
                ServicePackage::SCOPE_PRIVATE_LESSONS
            );
        }
        // Soft-gate: if student has any entitlement system usage, enforce it
        $hasAnyPrivatePackage = \App\Models\StudentServiceEntitlement::query()
            ->forUser((int) $session->student_id)
            ->whereIn('scope', [ServicePackage::SCOPE_PRIVATE_LESSONS, ServicePackage::SCOPE_GLOBAL])
            ->exists();
        if ($hasAnyPrivatePackage && (! $entitlement || ! $entitlement->hasUnitsLeft())) {
            throw new \InvalidArgumentException('لا يوجد رصيد حصص خاصة. اشترِ باقة أو اشحن رصيدك.');
        }
        if ($entitlement && ! $session->student_service_entitlement_id) {
            $session->student_service_entitlement_id = $entitlement->id;
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
            'student_service_entitlement_id' => $session->student_service_entitlement_id,
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
        DB::transaction(function () use ($session) {
            $session = OneToOneSession::query()
                ->with(['entitlement', 'classroomMeeting'])
                ->lockForUpdate()
                ->findOrFail($session->id);

            if ($session->status === OneToOneSession::STATUS_COMPLETED) {
                return;
            }
            if ($session->status !== OneToOneSession::STATUS_SCHEDULED) {
                throw new \InvalidArgumentException('لا يمكن إكمال حصة غير مجدولة.');
            }

            $entitlement = $session->entitlement ?: StudentEntitlementService::availableFor(
                (int) $session->student_id,
                ServicePackage::SCOPE_PRIVATE_LESSONS
            );

            if ($entitlement) {
                StudentEntitlementService::consume($entitlement, 1);
                if (! $session->student_service_entitlement_id) {
                    $session->student_service_entitlement_id = $entitlement->id;
                }
            }

            $session->status = OneToOneSession::STATUS_COMPLETED;
            $session->save();
            if ($session->classroomMeeting && ! $session->classroomMeeting->ended_at) {
                $session->classroomMeeting->update(['ended_at' => now()]);
            }
        });
    }
}
