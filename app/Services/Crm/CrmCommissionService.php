<?php

namespace App\Services\Crm;

use App\Models\CrmCommission;
use App\Models\CrmGroup;
use App\Models\Order;
use App\Models\SalesLead;
use App\Models\StudentCourseEnrollment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class CrmCommissionService
{
    /**
     * يُستدعى عند اعتماد الطلب (تأكيد الدفع) — IF IT IS NOT IN CRM, IT DID NOT HAPPEN.
     */
    public static function handleOrderApproved(Order $order, ?User $actor = null): void
    {
        if (! Schema::hasTable('crm_commissions')) {
            return;
        }

        $lead = self::resolveLeadForOrder($order);
        if (! $lead) {
            return;
        }

        DB::transaction(function () use ($order, $lead, $actor) {
        $lead->update([
            'order_id' => $order->id,
            'converted_order_id' => $order->id,
            'linked_user_id' => $order->user_id,
            'status' => SalesLead::STATUS_PAYMENT_CONFIRMED,
        ]);

        if (Schema::hasColumn('orders', 'sales_lead_id')) {
            $order->update(['sales_lead_id' => $lead->id]);
        }

            CrmAuditService::log('payment_confirmed', $lead, $actor, null, [
                'order_id' => $order->id,
                'amount' => $order->amount,
            ]);

            self::activateEnrollmentForLead($lead, $order, $actor);
            self::calculateCommissions($lead, $order, $actor);
        });
    }

    public static function resolveLeadForOrder(Order $order): ?SalesLead
    {
        if ($order->sales_lead_id) {
            return SalesLead::find($order->sales_lead_id);
        }

        $lead = SalesLead::query()
            ->where('order_id', $order->id)
            ->orWhere('converted_order_id', $order->id)
            ->first();

        if ($lead) {
            return $lead;
        }

        if ($order->user_id) {
            return SalesLead::query()
                ->open()
                ->where(function ($q) use ($order) {
                    $q->where('linked_user_id', $order->user_id);
                    if ($order->user?->email) {
                        $q->orWhere('email', $order->user->email);
                    }
                })
                ->when($order->advanced_course_id, fn ($q) => $q->where('interested_advanced_course_id', $order->advanced_course_id))
                ->latest()
                ->first();
        }

        return null;
    }

    private static function activateEnrollmentForLead(SalesLead $lead, Order $order, ?User $actor): void
    {
        if ($order->advanced_course_id && $order->user_id) {
            $enrollment = StudentCourseEnrollment::query()
                ->where('user_id', $order->user_id)
                ->where('advanced_course_id', $order->advanced_course_id)
                ->where('status', 'active')
                ->latest()
                ->first();

            if ($enrollment) {
                $lead->update([
                    'enrollment_id' => $enrollment->id,
                    'status' => SalesLead::STATUS_COURSE_ACTIVE,
                ]);

                CrmAuditService::log('course_activated', $lead, $actor, null, [
                    'enrollment_id' => $enrollment->id,
                    'order_id' => $order->id,
                ]);
            } else {
                $lead->update(['status' => SalesLead::STATUS_ENROLLED]);
                CrmAuditService::log('course_activated', $lead, $actor, null, [
                    'status' => SalesLead::STATUS_ENROLLED,
                    'order_id' => $order->id,
                ]);
            }
        }
    }

    public static function calculateCommissions(SalesLead $lead, Order $order, ?User $actor): void
    {
        $base = (float) $order->amount;
        $rates = config('crm.commission_rates', []);

        $recipients = [];

        if ($lead->marketing_owner_id) {
            $recipients[CrmCommission::TYPE_MARKETING] = $lead->marketing_owner_id;
        }
        if ($lead->assigned_to) {
            $recipients[CrmCommission::TYPE_SALES] = $lead->assigned_to;
        }

        if ($lead->crm_group_id) {
            $teamLeaderId = CrmGroup::query()->where('id', $lead->crm_group_id)->value('team_leader_id');
            if ($teamLeaderId) {
                $recipients[CrmCommission::TYPE_TEAM_LEADER] = (int) $teamLeaderId;
            }
        }

        foreach ($recipients as $type => $userId) {
            $percent = (float) ($rates[$type] ?? 0);
            if ($percent <= 0) {
                continue;
            }

            $amount = round($base * $percent / 100, 2);

            $commission = CrmCommission::firstOrCreate(
                [
                    'order_id' => $order->id,
                    'user_id' => $userId,
                    'type' => $type,
                ],
                [
                    'sales_lead_id' => $lead->id,
                    'base_amount_egp' => $base,
                    'commission_percent' => $percent,
                    'commission_amount_egp' => $amount,
                    'status' => CrmCommission::STATUS_PENDING,
                ]
            );

            CrmAuditService::log('commission_calculated', $lead, $actor, null, [
                'commission_id' => $commission->id,
                'type' => $type,
                'user_id' => $userId,
                'amount' => $amount,
            ]);
        }
    }

    public static function approveCommission(CrmCommission $commission, User $actor): CrmCommission
    {
        if ($commission->status !== CrmCommission::STATUS_PENDING) {
            return $commission;
        }

        $commission->update([
            'status' => CrmCommission::STATUS_APPROVED,
            'approved_at' => now(),
            'approved_by' => $actor->id,
        ]);

        CrmAuditService::log('commission_approved', $commission->lead, $actor, null, [
            'commission_id' => $commission->id,
            'amount' => $commission->commission_amount_egp,
        ]);

        return $commission->fresh();
    }
}
