<?php

namespace App\Services;

use App\Models\AdvancedCourse;
use App\Models\Notification;
use App\Models\Order;
use App\Models\StudentCourseEnrollment;
use Illuminate\Support\Facades\Log;

/**
 * تجديد تلقائي للاشتراكات الشهرية (إنشاء طلب تجديد + إشعار — الدفع عبر نفس مسار checkout).
 */
class CourseAutoRenewalService
{
    public static function processDueRenewals(): int
    {
        $processed = 0;

        $enrollments = StudentCourseEnrollment::query()
            ->where('auto_renew', true)
            ->where('status', 'active')
            ->whereNotNull('expires_at')
            ->whereBetween('expires_at', [now(), now()->addDays(3)])
            ->with(['course', 'student'])
            ->get();

        foreach ($enrollments as $enrollment) {
            if (! $enrollment->course || ! $enrollment->course->isMonthlyBilling()) {
                continue;
            }

            try {
                if (self::createRenewalOrderIfNeeded($enrollment)) {
                    $processed++;
                }
            } catch (\Throwable $e) {
                Log::error('Course auto-renewal failed', [
                    'enrollment_id' => $enrollment->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $processed;
    }

    public static function sendExpiryReminders(): int
    {
        $count = 0;

        $enrollments = StudentCourseEnrollment::query()
            ->where('status', 'active')
            ->whereNotNull('expires_at')
            ->whereBetween('expires_at', [now()->addDays(6)->startOfDay(), now()->addDays(7)->endOfDay()])
            ->where(function ($q) {
                $q->where('access_type', 'subscription')
                    ->orWhere('enrollment_type', 'subscription');
            })
            ->with(['course', 'student'])
            ->get();

        foreach ($enrollments as $enrollment) {
            $course = $enrollment->course;
            if (! $course) {
                continue;
            }

            $checkoutUrl = route('public.course.checkout', $course->id);
            Notification::create([
                'user_id' => $enrollment->user_id,
                'sender_id' => null,
                'title' => 'اشتراكك ينتهي خلال أسبوع',
                'message' => 'ينتهي اشتراكك في «'.$course->title.'» بتاريخ '.$enrollment->expires_at->format('Y-m-d').'. جدّد الآن للاستمرار.',
                'type' => 'reminder',
                'priority' => 'high',
                'audience' => 'student',
                'action_url' => $checkoutUrl,
                'action_text' => 'تجديد الاشتراك',
            ]);
            $count++;
        }

        return $count;
    }

    private static function createRenewalOrderIfNeeded(StudentCourseEnrollment $enrollment): bool
    {
        $course = $enrollment->course;
        $user = $enrollment->student;
        if (! $course || ! $user) {
            return false;
        }

        $existingPending = Order::query()
            ->where('user_id', $user->id)
            ->where('advanced_course_id', $course->id)
            ->where('status', Order::STATUS_PENDING)
            ->exists();

        if ($existingPending) {
            return false;
        }

        $amount = $course->effectiveCheckoutPrice();
        if ($amount <= 0) {
            return false;
        }

        $order = Order::create([
            'user_id' => $user->id,
            'advanced_course_id' => $course->id,
            'original_amount' => $amount,
            'discount_amount' => 0,
            'wallet_credit_amount' => 0,
            'amount' => $amount,
            'billing_mode' => CourseSubscriptionService::BILLING_MONTHLY,
            'auto_renew' => true,
            'payment_method' => 'online',
            'notes' => 'تجديد تلقائي للاشتراك الشهري',
            'status' => Order::STATUS_PENDING,
        ]);

        Notification::create([
            'user_id' => $user->id,
            'sender_id' => null,
            'title' => 'تجديد اشتراكك الشهري',
            'message' => 'اشتراكك في «'.$course->title.'» على وشك الانتهاء. أكمل الدفع لتجديد الوصول تلقائياً.',
            'type' => 'reminder',
            'priority' => 'high',
            'audience' => 'student',
            'action_url' => route('public.course.checkout', $course->id),
            'action_text' => 'إتمام التجديد',
        ]);

        return true;
    }
}
