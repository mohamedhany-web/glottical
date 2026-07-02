<?php

namespace App\Services\Crm;

use App\Models\CrmGroup;
use App\Models\CrmGroupMember;
use App\Models\SalesLead;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class CrmAccessService
{
    public static function crmRole(User $user): string
    {
        if ($user->role === 'super_admin' || $user->hasPermission('manage.leads')) {
            return 'super_admin';
        }

        $code = $user->employeeJobCode();

        return match ($code) {
            config('crm.employee_job_codes.team_leader') => 'team_leader',
            config('crm.employee_job_codes.marketing') => 'marketing',
            config('crm.employee_job_codes.sales') => 'sales',
            config('crm.employee_job_codes.finance'), 'accountant' => 'finance',
            default => 'none',
        };
    }

    public static function canAccessCrm(User $user): bool
    {
        return self::crmRole($user) !== 'none' || $user->role === 'super_admin';
    }

    public static function leadsQueryFor(User $user): Builder
    {
        $role = self::crmRole($user);
        $query = SalesLead::query();

        return match ($role) {
            'super_admin' => $query,
            'marketing' => $query->where('marketing_owner_id', $user->id),
            'sales' => $query->where('assigned_to', $user->id),
            'team_leader' => self::teamLeaderLeadsQuery($user, $query),
            'finance' => $query->whereIn('status', [
                SalesLead::STATUS_PAYMENT_PENDING,
                SalesLead::STATUS_PAYMENT_CONFIRMED,
                SalesLead::STATUS_ENROLLED,
                SalesLead::STATUS_COURSE_ACTIVE,
                SalesLead::STATUS_CLOSED_WON,
            ]),
            default => $query->whereRaw('1 = 0'),
        };
    }

    private static function teamLeaderLeadsQuery(User $user, Builder $query): Builder
    {
        $groupIds = CrmGroup::query()->where('team_leader_id', $user->id)->pluck('id');
        $memberIds = CrmGroupMember::query()
            ->whereIn('crm_group_id', $groupIds)
            ->where('is_active', true)
            ->pluck('user_id');

        return $query->where(function ($q) use ($groupIds, $memberIds) {
            $q->whereIn('crm_group_id', $groupIds)
                ->orWhereIn('marketing_owner_id', $memberIds)
                ->orWhereIn('assigned_to', $memberIds);
        });
    }

    public static function canViewLead(User $user, SalesLead $lead): bool
    {
        if (self::crmRole($user) === 'super_admin') {
            return true;
        }

        return self::leadsQueryFor($user)->where('sales_leads.id', $lead->id)->exists();
    }

    public static function canEditLead(User $user, SalesLead $lead): bool
    {
        if ($lead->isClosed()) {
            return false;
        }

        $role = self::crmRole($user);

        if ($role === 'super_admin') {
            return true;
        }

        if ($role === 'marketing') {
            return $lead->marketing_owner_id === $user->id
                && $lead->status === SalesLead::STATUS_NEW;
        }

        if ($role === 'sales') {
            return $lead->assigned_to === $user->id
                && ! in_array($lead->status, [SalesLead::STATUS_NEW], true);
        }

        return false;
    }

    public static function canAssignLead(User $user): bool
    {
        return in_array(self::crmRole($user), ['super_admin'], true);
    }

    public static function canTransitionStatus(User $user, SalesLead $lead, string $toStatus): bool
    {
        if (! $lead->canTransitionTo($toStatus)) {
            return false;
        }

        $role = self::crmRole($user);

        if ($role === 'super_admin') {
            return true;
        }

        if ($toStatus === SalesLead::STATUS_ASSIGNED) {
            return false;
        }

        if ($role === 'marketing') {
            return false;
        }

        if ($role === 'sales') {
            if ($lead->assigned_to !== $user->id) {
                return false;
            }
            $salesStatuses = [
                SalesLead::STATUS_CONTACTED,
                SalesLead::STATUS_INTERESTED,
                SalesLead::STATUS_PLACEMENT_TEST,
                SalesLead::STATUS_OFFER_SENT,
                SalesLead::STATUS_PAYMENT_PENDING,
                SalesLead::STATUS_CLOSED_LOST,
            ];

            return in_array($toStatus, $salesStatuses, true);
        }

        if ($role === 'finance') {
            return in_array($toStatus, [
                SalesLead::STATUS_PAYMENT_CONFIRMED,
            ], true);
        }

        return false;
    }

    public static function canSeePaymentData(User $user): bool
    {
        $role = self::crmRole($user);

        return in_array($role, ['super_admin', 'finance', 'team_leader'], true);
    }
}
