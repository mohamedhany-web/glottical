<?php

namespace App\Services\Crm;

use App\Models\CrmCommission;
use App\Models\CrmGroup;
use App\Models\CrmGroupMember;
use App\Models\SalesLead;
use App\Models\User;
use Illuminate\Support\Collection;
use InvalidArgumentException;

class CrmTeamService
{
    public static function addMember(CrmGroup $group, int $userId, string $role, User $actor): CrmGroupMember
    {
        if (! CrmAccessService::canManageTeam($actor, $group)) {
            throw new InvalidArgumentException('غير مصرح بإدارة هذا الفريق.');
        }

        if (! in_array($role, ['marketing', 'sales'], true)) {
            throw new InvalidArgumentException('دور العضو غير صالح.');
        }

        $member = CrmGroupMember::updateOrCreate(
            ['crm_group_id' => $group->id, 'user_id' => $userId, 'role' => $role],
            ['is_active' => true]
        );

        CrmAuditService::log('team_member_added', null, $actor, null, [
            'crm_group_id' => $group->id,
            'user_id' => $userId,
            'role' => $role,
        ]);

        return $member;
    }

    public static function removeMember(CrmGroup $group, CrmGroupMember $member, User $actor): void
    {
        if (! CrmAccessService::canManageTeam($actor, $group)) {
            throw new InvalidArgumentException('غير مصرح بإدارة هذا الفريق.');
        }

        abort_unless($member->crm_group_id === $group->id, 404);

        $member->update(['is_active' => false]);

        CrmAuditService::log('team_member_removed', null, $actor, null, [
            'crm_group_id' => $group->id,
            'user_id' => $member->user_id,
            'role' => $member->role,
        ]);
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    public static function memberPerformanceStats(User $teamLeader): Collection
    {
        $groups = CrmAccessService::teamGroupsFor($teamLeader)
            ->with(['activeMembers.user:id,name'])
            ->get();

        $stats = collect();

        foreach ($groups as $group) {
            foreach ($group->activeMembers as $member) {
                $user = $member->user;
                if (! $user) {
                    continue;
                }

                $leadQuery = SalesLead::query();
                if ($member->role === 'marketing') {
                    $leadQuery->where('marketing_owner_id', $user->id);
                } else {
                    $leadQuery->where('assigned_to', $user->id);
                }

                $leadIds = (clone $leadQuery)->pluck('id');

                $stats->push([
                    'group_id' => $group->id,
                    'group_name' => $group->name,
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'role' => $member->role,
                    'role_label' => $member->role === 'marketing' ? 'تسويق' : 'مبيعات',
                    'total_leads' => (clone $leadQuery)->count(),
                    'open_leads' => (clone $leadQuery)->open()->count(),
                    'closed_won' => (clone $leadQuery)->where('status', SalesLead::STATUS_CLOSED_WON)->count(),
                    'payment_pending' => (clone $leadQuery)->where('status', SalesLead::STATUS_PAYMENT_PENDING)->count(),
                    'revenue' => (float) (clone $leadQuery)
                        ->whereNotNull('order_id')
                        ->join('orders', 'orders.id', '=', 'sales_leads.order_id')
                        ->sum('orders.amount'),
                    'commissions' => (float) CrmCommission::where('user_id', $user->id)
                        ->whereIn('sales_lead_id', $leadIds)
                        ->sum('commission_amount_egp'),
                ]);
            }
        }

        return $stats->sortBy('user_name')->values();
    }
}
