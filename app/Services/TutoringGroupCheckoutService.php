<?php

namespace App\Services;

use App\Models\Order;
use App\Models\StudentTutoringSubscription;
use App\Models\TutoringGroup;
use App\Models\TutoringGroupBooking;
use App\Models\TutoringGroupCohort;
use App\Models\TutoringGroupPackage;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class TutoringGroupCheckoutService
{
    public static function createPackageOrder(
        User $user,
        TutoringGroup $group,
        TutoringGroupPackage $package,
        string $paymentMethod = 'online',
        ?int $walletId = null
    ): Order {
        if (! $group->isIndividual()) {
            throw new InvalidArgumentException('الباقات متاحة للمجموعات الفردية فقط.');
        }

        if (! $package->is_active || (int) $package->tutoring_group_id !== (int) $group->id) {
            throw new InvalidArgumentException('الباقة غير متاحة.');
        }

        return Order::create([
            'user_id' => $user->id,
            'tutoring_group_id' => $group->id,
            'tutoring_group_package_id' => $package->id,
            'order_type' => Order::TYPE_TUTORING_PACKAGE,
            'original_amount' => $package->original_price ?? $package->price,
            'discount_amount' => max(0, (float) ($package->original_price ?? $package->price) - (float) $package->price),
            'amount' => $package->price,
            'payment_method' => $paymentMethod,
            'wallet_id' => $walletId,
            'status' => Order::STATUS_PENDING,
            'notes' => 'اشتراك باقة tutoring: '.$package->name,
        ]);
    }

    public static function createCohortOrder(
        User $user,
        TutoringGroup $group,
        TutoringGroupCohort $cohort,
        string $paymentMethod = 'online',
        ?int $walletId = null,
        ?Carbon $startsAt = null
    ): Order {
        if (! $group->isCollective()) {
            throw new InvalidArgumentException('الدفعات متاحة للمجموعات الجماعية فقط.');
        }

        if ((int) $cohort->tutoring_group_id !== (int) $group->id) {
            throw new InvalidArgumentException('الدفعة لا تنتمي لهذه المجموعة.');
        }

        if (! TutoringCohortService::isEnrollmentOpen($cohort)) {
            throw new InvalidArgumentException('هذه الدفعة غير متاحة للاشتراك.');
        }

        $amount = (float) ($group->price ?? 0);

        return Order::create([
            'user_id' => $user->id,
            'tutoring_group_id' => $group->id,
            'tutoring_group_cohort_id' => $cohort->id,
            'order_type' => Order::TYPE_TUTORING_COHORT,
            'original_amount' => $amount,
            'discount_amount' => 0,
            'amount' => $amount,
            'payment_method' => $paymentMethod,
            'wallet_id' => $walletId,
            'status' => Order::STATUS_PENDING,
            'notes' => 'اشتراك دفعة: '.$cohort->title.($startsAt ? ' | starts_at='.$startsAt->toIso8601String() : ''),
        ]);
    }

    /**
     * Fulfill approved tutoring order: subscription + optional first booking + live confirm.
     */
    public static function fulfillApprovedOrder(Order $order, ?Carbon $preferredStartsAt = null): ?StudentTutoringSubscription
    {
        if (! $order->isTutoringOrder()) {
            return null;
        }

        return DB::transaction(function () use ($order, $preferredStartsAt) {
            $order->loadMissing(['tutoringGroup', 'tutoringPackage', 'tutoringCohort', 'user']);
            $group = $order->tutoringGroup;
            if (! $group) {
                return null;
            }

            $subscription = null;

            if ($order->order_type === Order::TYPE_TUTORING_PACKAGE && $order->tutoringPackage) {
                $package = $order->tutoringPackage;
                $subscription = StudentTutoringSubscription::create([
                    'user_id' => $order->user_id,
                    'tutoring_group_id' => $group->id,
                    'tutoring_group_package_id' => $package->id,
                    'sessions_total' => (int) $package->sessions_count,
                    'sessions_used' => 0,
                    'starts_at' => now(),
                    'expires_at' => now()->addMonths(max(1, (int) $package->duration_months)),
                    'status' => StudentTutoringSubscription::STATUS_ACTIVE,
                    'order_id' => $order->id,
                ]);
            }

            $startsAt = $preferredStartsAt;
            if (! $startsAt && $order->notes && preg_match('/starts_at=([^\s|]+)/', $order->notes, $m)) {
                try {
                    $startsAt = Carbon::parse($m[1]);
                } catch (\Throwable) {
                    $startsAt = null;
                }
            }

            if ($order->order_type === Order::TYPE_TUTORING_COHORT && $order->tutoringCohort) {
                $cohort = $order->tutoringCohort;
                $startsAt = $startsAt ?: ($cohort->starts_at ?: now()->addDay()->setTime(18, 0));
                $duration = max(30, (int) ($group->duration_minutes ?? 60));

                $booking = TutoringGroupBooking::create([
                    'tutoring_group_id' => $group->id,
                    'cohort_id' => $cohort->id,
                    'order_id' => $order->id,
                    'payment_status' => TutoringGroupBooking::PAYMENT_PAID,
                    'instructor_id' => $group->instructor_id,
                    'user_id' => $order->user_id,
                    'starts_at' => $startsAt,
                    'ends_at' => $startsAt->copy()->addMinutes($duration),
                    'status' => TutoringGroupBooking::STATUS_PENDING,
                ]);

                TutoringCohortService::enroll($cohort);
                TutoringCrmHookService::onBookingCreated($booking->fresh(['tutoringGroup', 'user']));
                TutoringGroupOrchestrationService::confirmBooking($booking);
            }

            if ($subscription && $startsAt) {
                $duration = max(30, (int) ($group->duration_minutes ?? 60));
                $booking = TutoringGroupBooking::create([
                    'tutoring_group_id' => $group->id,
                    'tutoring_group_package_id' => $subscription->tutoring_group_package_id,
                    'student_tutoring_subscription_id' => $subscription->id,
                    'order_id' => $order->id,
                    'payment_status' => TutoringGroupBooking::PAYMENT_PAID,
                    'instructor_id' => $group->instructor_id,
                    'user_id' => $order->user_id,
                    'starts_at' => $startsAt,
                    'ends_at' => $startsAt->copy()->addMinutes($duration),
                    'status' => TutoringGroupBooking::STATUS_PENDING,
                ]);
                TutoringCrmHookService::onBookingCreated($booking->fresh(['tutoringGroup', 'user']));
                TutoringGroupOrchestrationService::confirmBooking($booking);
            }

            return $subscription;
        });
    }

    /**
     * Book a session against an active individual subscription (no new payment).
     */
    public static function bookFromSubscription(
        StudentTutoringSubscription $subscription,
        Carbon $startsAt
    ): TutoringGroupBooking {
        if (! $subscription->hasSessionsLeft()) {
            throw new InvalidArgumentException('لا توجد حصص متبقية في باقتك.');
        }

        $group = $subscription->tutoringGroup;
        if (! $group || ! $group->isIndividual()) {
            throw new InvalidArgumentException('الاشتراك غير صالح.');
        }

        $duration = max(30, (int) ($group->duration_minutes ?? 60));
        $endsAt = $startsAt->copy()->addMinutes($duration);

        $allowed = TutoringGroupAvailabilityService::availableSlots(
            $group,
            $startsAt->copy()->startOfDay(),
            $startsAt->copy()->endOfDay()
        )->first(fn ($s) => Carbon::parse($s['starts_at'])->equalTo($startsAt));

        if (! $allowed) {
            throw new InvalidArgumentException('هذا الموعد غير متاح.');
        }

        $booking = TutoringGroupBooking::create([
            'tutoring_group_id' => $group->id,
            'tutoring_group_package_id' => $subscription->tutoring_group_package_id,
            'student_tutoring_subscription_id' => $subscription->id,
            'payment_status' => TutoringGroupBooking::PAYMENT_PAID,
            'instructor_id' => $group->instructor_id,
            'user_id' => $subscription->user_id,
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'status' => TutoringGroupBooking::STATUS_PENDING,
        ]);

        TutoringCrmHookService::onBookingCreated($booking->fresh(['tutoringGroup', 'user']));

        return TutoringGroupOrchestrationService::confirmBooking($booking);
    }
}
