<?php

namespace App\Services;

use App\Models\Order;
use App\Models\ServicePackage;
use App\Models\ServicePackagePricingRule;
use App\Models\StudentServiceEntitlement;
use App\Models\StudentTutoringSubscription;
use App\Models\TutoringGroup;
use App\Models\TutoringGroupBooking;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class StudentEntitlementService
{
    public static function createOrder(User $user, ServicePackage $package, string $paymentMethod = 'online', ?int $walletId = null): Order
    {
        if (! $package->is_active) {
            throw new InvalidArgumentException('الباقة غير متاحة حالياً.');
        }

        $allowed = ['bank_transfer', 'cash', 'other', 'online', 'wallet'];
        if (! in_array($paymentMethod, $allowed, true)) {
            $paymentMethod = 'bank_transfer';
        }

        return Order::create([
            'user_id' => $user->id,
            'service_package_id' => $package->id,
            'tutoring_group_id' => $package->tutoring_group_id,
            'order_type' => Order::TYPE_SERVICE_PACKAGE,
            'original_amount' => $package->original_price ?? $package->price,
            'discount_amount' => max(0, (float) ($package->original_price ?? $package->price) - (float) $package->price),
            'amount' => $package->price,
            'payment_method' => $paymentMethod,
            'wallet_id' => $walletId,
            'status' => Order::STATUS_PENDING,
            'notes' => 'باقة خدمات: '.$package->name.' ('.$package->units_count.' حصة)',
        ]);
    }

    public static function createCustomOrder(
        User $user,
        ServicePackagePricingRule $rule,
        int $sessions,
        string $paymentMethod = 'online',
        ?int $walletId = null,
    ): Order {
        $allowed = ['bank_transfer', 'cash', 'other', 'online', 'wallet'];
        if (! in_array($paymentMethod, $allowed, true)) {
            $paymentMethod = 'bank_transfer';
        }

        $quote = CustomServicePackagePricingService::calculate($rule, $sessions);

        return Order::create([
            'user_id' => $user->id,
            'service_package_id' => null,
            'tutoring_group_id' => null,
            'custom_package_data' => $quote,
            'order_type' => Order::TYPE_CUSTOM_SERVICE_PACKAGE,
            'original_amount' => $quote['original_amount'],
            'discount_amount' => $quote['discount_amount'],
            'amount' => $quote['amount'],
            'payment_method' => $paymentMethod,
            'wallet_id' => $walletId,
            'status' => Order::STATUS_PENDING,
            'notes' => $quote['name'].' ('.$quote['sessions'].' حصة × '.$quote['session_minutes'].' دقيقة) — USD',
        ]);
    }

    public static function grantFromOrder(Order $order): ?StudentServiceEntitlement
    {
        if ($order->order_type === Order::TYPE_CUSTOM_SERVICE_PACKAGE && $order->custom_package_data) {
            $existing = StudentServiceEntitlement::query()->where('order_id', $order->id)->first();
            if ($existing) {
                return $existing;
            }

            $data = $order->custom_package_data;
            $starts = now();
            $durationDays = max(1, (int) ($data['duration_days'] ?? 90));

            return StudentServiceEntitlement::create([
                'user_id' => $order->user_id,
                'service_package_id' => null,
                'order_id' => $order->id,
                'scope' => $data['scope'] ?? ServicePackage::SCOPE_GLOBAL,
                'tutoring_group_id' => null,
                'units_total' => max(1, (int) ($data['sessions'] ?? 1)),
                'units_used' => 0,
                'starts_at' => $starts,
                'expires_at' => $starts->copy()->addDays($durationDays),
                'status' => StudentServiceEntitlement::STATUS_ACTIVE,
                'notes' => 'custom_package:'.json_encode($data, JSON_UNESCAPED_UNICODE),
            ]);
        }

        if ($order->order_type === Order::TYPE_SERVICE_PACKAGE && $order->service_package_id) {
            $existing = StudentServiceEntitlement::query()->where('order_id', $order->id)->first();
            if ($existing) {
                return $existing;
            }

            $package = $order->servicePackage ?: ServicePackage::find($order->service_package_id);
            if (! $package) {
                return null;
            }

            return self::grant(
                userId: (int) $order->user_id,
                package: $package,
                orderId: (int) $order->id,
            );
        }

        return null;
    }

    public static function grant(
        int $userId,
        ServicePackage $package,
        ?int $orderId = null,
        ?int $unitsOverride = null,
        ?string $notes = null,
    ): StudentServiceEntitlement {
        if ($orderId) {
            $existing = StudentServiceEntitlement::query()->where('order_id', $orderId)->first();
            if ($existing) {
                return $existing;
            }
        }

        $units = max(1, (int) ($unitsOverride ?? $package->units_count));
        $starts = now();
        $expires = $package->duration_days
            ? $starts->copy()->addDays((int) $package->duration_days)
            : null;

        return StudentServiceEntitlement::create([
            'user_id' => $userId,
            'service_package_id' => $package->id,
            'order_id' => $orderId,
            'scope' => $package->scope ?: ServicePackage::SCOPE_GLOBAL,
            'tutoring_group_id' => $package->tutoring_group_id,
            'units_total' => $units,
            'units_used' => 0,
            'starts_at' => $starts,
            'expires_at' => $expires,
            'status' => StudentServiceEntitlement::STATUS_ACTIVE,
            'notes' => $notes,
        ]);
    }

    public static function grantManual(
        int $userId,
        string $scope,
        int $units,
        ?int $tutoringGroupId = null,
        ?int $durationDays = null,
        ?string $notes = null,
    ): StudentServiceEntitlement {
        $starts = now();

        return StudentServiceEntitlement::create([
            'user_id' => $userId,
            'service_package_id' => null,
            'order_id' => null,
            'scope' => $scope,
            'tutoring_group_id' => $tutoringGroupId,
            'units_total' => max(1, $units),
            'units_used' => 0,
            'starts_at' => $starts,
            'expires_at' => $durationDays ? $starts->copy()->addDays($durationDays) : null,
            'status' => StudentServiceEntitlement::STATUS_ACTIVE,
            'notes' => $notes ?: 'منح يدوي من الإدارة',
        ]);
    }

    /**
     * Prefer group-specific, then matching scope, then global.
     */
    public static function availableFor(
        int $userId,
        string $scope,
        ?int $tutoringGroupId = null,
    ): ?StudentServiceEntitlement {
        self::expireStaleForUser($userId);

        $base = StudentServiceEntitlement::query()
            ->forUser($userId)
            ->active()
            ->whereColumn('units_used', '<', 'units_total')
            ->orderBy('expires_at')
            ->orderBy('id');

        $candidates = collect();
        if ($tutoringGroupId) {
            $candidates = $candidates->merge(
                (clone $base)
                    ->where('tutoring_group_id', $tutoringGroupId)
                    ->whereIn('scope', [$scope, ServicePackage::SCOPE_GLOBAL])
                    ->get()
            );
        }
        $candidates = $candidates->merge(
            (clone $base)
                ->whereNull('tutoring_group_id')
                ->where('scope', $scope)
                ->get()
        );
        $candidates = $candidates->merge(
            (clone $base)
                ->whereNull('tutoring_group_id')
                ->where('scope', ServicePackage::SCOPE_GLOBAL)
                ->get()
        );

        foreach ($candidates->unique('id') as $entitlement) {
            if (self::bookableUnitsLeft($entitlement) > 0) {
                return $entitlement;
            }
        }

        return null;
    }

    /**
     * Units not yet used and not reserved by open (pending/confirmed) bookings.
     */
    public static function bookableUnitsLeft(StudentServiceEntitlement $entitlement): int
    {
        $reserved = TutoringGroupBooking::query()
            ->where('student_service_entitlement_id', $entitlement->id)
            ->whereIn('status', [
                TutoringGroupBooking::STATUS_PENDING,
                TutoringGroupBooking::STATUS_CONFIRMED,
            ])
            ->count();

        // One-to-one sessions scheduled against this entitlement also reserve a unit
        $otoReserved = 0;
        if (class_exists(\App\Models\OneToOneSession::class)
            && \Illuminate\Support\Facades\Schema::hasColumn('one_to_one_sessions', 'student_service_entitlement_id')) {
            $otoReserved = \App\Models\OneToOneSession::query()
                ->where('student_service_entitlement_id', $entitlement->id)
                ->whereIn('status', [
                    \App\Models\OneToOneSession::STATUS_PENDING,
                    \App\Models\OneToOneSession::STATUS_SCHEDULED,
                ])
                ->count();
        }

        return max(0, $entitlement->unitsLeft() - $reserved - $otoReserved);
    }

    public static function unitsLeft(int $userId, string $scope, ?int $tutoringGroupId = null): int
    {
        self::expireStaleForUser($userId);

        return (int) StudentServiceEntitlement::query()
            ->forUser($userId)
            ->active()
            ->where(function ($q) use ($scope, $tutoringGroupId) {
                $q->where(function ($inner) use ($scope, $tutoringGroupId) {
                    if ($tutoringGroupId) {
                        $inner->where('tutoring_group_id', $tutoringGroupId)
                            ->whereIn('scope', [$scope, ServicePackage::SCOPE_GLOBAL]);
                    } else {
                        $inner->whereRaw('1 = 0');
                    }
                })->orWhere(function ($inner) use ($scope) {
                    $inner->whereNull('tutoring_group_id')
                        ->whereIn('scope', [$scope, ServicePackage::SCOPE_GLOBAL]);
                });
            })
            ->get()
            ->sum(fn (StudentServiceEntitlement $e) => self::bookableUnitsLeft($e));
    }

    public static function assertCanBook(int $userId, string $scope, ?int $tutoringGroupId = null): StudentServiceEntitlement
    {
        $entitlement = self::availableFor($userId, $scope, $tutoringGroupId);
        if (! $entitlement) {
            throw new InvalidArgumentException('لا يوجد رصيد حصص متاح. اشترِ باقة أو اشحن رصيدك.');
        }

        return $entitlement;
    }

    public static function consume(StudentServiceEntitlement $entitlement, int $units = 1): StudentServiceEntitlement
    {
        return DB::transaction(function () use ($entitlement, $units) {
            $locked = StudentServiceEntitlement::query()->lockForUpdate()->findOrFail($entitlement->id);

            if (! $locked->isActive()) {
                throw new InvalidArgumentException('الرصيد غير نشط أو منتهٍ.');
            }
            if ($locked->unitsLeft() < $units) {
                throw new InvalidArgumentException('رصيد الحصص غير كافٍ.');
            }

            $locked->units_used = (int) $locked->units_used + $units;
            if ($locked->units_used >= (int) $locked->units_total) {
                $locked->status = StudentServiceEntitlement::STATUS_EXPIRED;
            }
            $locked->save();

            // Keep legacy tutoring subscription in sync when linked
            foreach (StudentTutoringSubscription::query()->where('student_service_entitlement_id', $locked->id)->get() as $sub) {
                $sub->sessions_used = min((int) $sub->sessions_total, (int) $locked->units_used);
                if ($sub->sessions_used >= (int) $sub->sessions_total || $locked->status === StudentServiceEntitlement::STATUS_EXPIRED) {
                    $sub->status = StudentTutoringSubscription::STATUS_EXPIRED;
                }
                $sub->save();
            }

            return $locked->fresh();
        });
    }

    public static function expireEmpty(StudentServiceEntitlement $entitlement): void
    {
        if ($entitlement->unitsLeft() <= 0 && $entitlement->status === StudentServiceEntitlement::STATUS_ACTIVE) {
            $entitlement->update(['status' => StudentServiceEntitlement::STATUS_EXPIRED]);
        }
    }

    public static function expireStaleForUser(int $userId): void
    {
        StudentServiceEntitlement::query()
            ->forUser($userId)
            ->where('status', StudentServiceEntitlement::STATUS_ACTIVE)
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', now())
            ->update(['status' => StudentServiceEntitlement::STATUS_EXPIRED]);
    }

    public static function scopeForTutoringGroup(TutoringGroup $group): string
    {
        return $group->isCollective()
            ? ServicePackage::SCOPE_TUTORING_COLLECTIVE
            : ServicePackage::SCOPE_TUTORING_INDIVIDUAL;
    }

    public static function syncLegacySubscription(StudentTutoringSubscription $subscription, StudentServiceEntitlement $entitlement): void
    {
        $subscription->update([
            'student_service_entitlement_id' => $entitlement->id,
            'sessions_total' => $entitlement->units_total,
            'sessions_used' => $entitlement->units_used,
            'status' => $entitlement->status === StudentServiceEntitlement::STATUS_ACTIVE
                ? StudentTutoringSubscription::STATUS_ACTIVE
                : StudentTutoringSubscription::STATUS_EXPIRED,
            'starts_at' => $entitlement->starts_at,
            'expires_at' => $entitlement->expires_at,
        ]);
    }

    public static function findOrCreatePackageForTutoringGroupPackage($tutoringPackage): ServicePackage
    {
        $existing = ServicePackage::query()
            ->where('scope', ServicePackage::SCOPE_TUTORING_INDIVIDUAL)
            ->where('tutoring_group_id', $tutoringPackage->tutoring_group_id)
            ->where('units_count', (int) $tutoringPackage->sessions_count)
            ->where('price', $tutoringPackage->price)
            ->first();

        if ($existing) {
            return $existing;
        }

        return ServicePackage::create([
            'name' => $tutoringPackage->name,
            'slug' => ServicePackage::uniqueSlug('tgp-'.$tutoringPackage->id.'-'.$tutoringPackage->name),
            'description' => 'باقة مجموعة فردية',
            'badge' => $tutoringPackage->is_featured ? 'مميزة' : null,
            'scope' => ServicePackage::SCOPE_TUTORING_INDIVIDUAL,
            'tutoring_group_id' => $tutoringPackage->tutoring_group_id,
            'units_count' => max(1, (int) $tutoringPackage->sessions_count),
            'duration_days' => max(1, (int) $tutoringPackage->duration_months) * 30,
            'price' => $tutoringPackage->price,
            'original_price' => $tutoringPackage->original_price,
            'currency' => $tutoringPackage->currency ?: 'EGP',
            'is_active' => (bool) $tutoringPackage->is_active,
            'is_featured' => (bool) $tutoringPackage->is_featured,
            'sort_order' => (int) ($tutoringPackage->sort_order ?? 0),
        ]);
    }
}
