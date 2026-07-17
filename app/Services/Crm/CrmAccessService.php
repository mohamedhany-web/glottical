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



        $role = match ($code) {

            config('crm.employee_job_codes.team_leader') => 'team_leader',

            config('crm.employee_job_codes.marketing') => 'marketing',

            config('crm.employee_job_codes.sales') => 'sales',

            config('crm.employee_job_codes.finance'), 'accountant' => 'finance',

            default => 'none',

        };

        // معيَّن كقائد مجموعة CRM فعّالة ⇒ دور قائد فريق حتى لو الوظيفة ليست crm_team_leader
        if ($role !== 'finance' && self::leadsActiveCrmGroupsAsLeader($user)) {
            return 'team_leader';
        }

        return $role;

    }

    /**
     * هل المستخدم قائد لمجموعة CRM نشطة؟
     */
    public static function leadsActiveCrmGroupsAsLeader(User $user): bool
    {
        return CrmGroup::query()
            ->where('team_leader_id', $user->id)
            ->where('is_active', true)
            ->exists();
    }



    public static function canAccessCrm(User $user): bool

    {

        if (self::crmRole($user) !== 'none' || $user->role === 'super_admin') {

            return true;

        }



        return self::hasCrmPermission($user, 'crm_desk');

    }



    public static function hasCrmPermission(User $user, string $permission): bool

    {

        if ($user->role === 'super_admin' || $user->hasPermission('manage.leads')) {

            return true;

        }



        if (! $user->is_employee) {

            return false;

        }



        if ($user->employeeCan($permission)) {

            return true;

        }



        if ($user->usesCustomEmployeePermissions()) {

            // قائد مجموعة مفعّلة يحتفظ بصلاحيات قيادة الفريق حتى لو سقطت من الصلاحيات المخصّصة بالخطأ
            if (in_array($permission, ['crm_desk', 'crm_assign_leads', 'crm_manage_team', 'crm_view_team_performance', 'crm_add_notes'], true)
                && self::leadsActiveCrmGroupsAsLeader($user)) {
                return true;
            }

            return false;

        }



        $role = self::crmRole($user);

        $defaults = config('crm.role_default_permissions.'.$role, []);



        if (in_array('*', $defaults, true)) {

            return true;

        }



        return in_array($permission, $defaults, true);

    }



    public static function leadsQueryFor(User $user): Builder

    {

        $role = self::crmRole($user);

        $query = SalesLead::query();



        return match ($role) {

            'super_admin' => $query,

            'marketing' => $query->where('marketing_owner_id', $user->id),

            'sales' => $query->where(function ($q) use ($user) {
                $q->where('assigned_to', $user->id)
                    ->orWhere(function ($inbox) {
                        $inbox->whereNotNull('submitted_to_sales_at')
                            ->whereNull('assigned_to')
                            ->whereNotIn('status', [
                                SalesLead::STATUS_CLOSED_WON,
                                SalesLead::STATUS_CLOSED_LOST,
                            ]);
                    });
            }),

            'team_leader' => self::teamLeaderLeadsQuery($user, $query),

            'finance' => self::hasCrmPermission($user, 'crm_view_all_leads')

                ? $query

                : $query->whereIn('status', [

                    SalesLead::STATUS_PAYMENT_PENDING,

                    SalesLead::STATUS_PAYMENT_CONFIRMED,

                    SalesLead::STATUS_ENROLLED,

                    SalesLead::STATUS_COURSE_ACTIVE,

                    SalesLead::STATUS_CLOSED_WON,

                ]),

            default => $query->whereRaw('1 = 0'),

        };

    }



    public static function teamGroupsFor(User $user): Builder

    {

        if (self::crmRole($user) === 'super_admin') {

            return CrmGroup::query()->where('is_active', true);

        }



        return CrmGroup::query()

            ->where('team_leader_id', $user->id)

            ->where('is_active', true);

    }



    public static function canManageTeam(User $user, ?CrmGroup $group = null): bool

    {

        if (! self::hasCrmPermission($user, 'crm_manage_team')) {

            return false;

        }



        if (self::crmRole($user) === 'super_admin') {

            return true;

        }



        if (! $group) {

            return self::teamGroupsFor($user)->exists();

        }



        return (int) $group->team_leader_id === (int) $user->id;

    }



    public static function canViewTeamPerformance(User $user): bool

    {

        if (self::hasCrmPermission($user, 'crm_view_team_performance')) {

            return in_array(self::crmRole($user), ['team_leader', 'finance', 'super_admin'], true);

        }



        return false;

    }



    public static function canViewSalesFinancialReports(User $user): bool

    {

        return self::hasCrmPermission($user, 'crm_view_sales_financial_reports');

    }



    public static function canViewSubmittedReports(User $user): bool

    {

        return self::hasCrmPermission($user, 'crm_view_submitted_reports')

            || self::hasCrmPermission($user, 'crm_view_sales_financial_reports');

    }



    public static function teamsVisibleTo(User $user): Builder

    {

        if (self::crmRole($user) === 'super_admin') {

            return CrmGroup::query()->where('is_active', true);

        }



        if (self::crmRole($user) === 'finance' && self::hasCrmPermission($user, 'crm_view_all_leads')) {

            return CrmGroup::query()->where('is_active', true);

        }



        return self::teamGroupsFor($user);

    }



    private static function teamLeaderLeadsQuery(User $user, Builder $query): Builder

    {

        $groupIds = self::teamGroupsFor($user)->pluck('id');

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



    public static function canTeamLeaderManageLead(User $user, SalesLead $lead): bool

    {

        if (self::crmRole($user) === 'super_admin') {

            return true;

        }



        $groupIds = self::teamGroupsFor($user)->pluck('id');



        if ($groupIds->isEmpty()) {

            return false;

        }



        if ($lead->crm_group_id && $groupIds->contains($lead->crm_group_id)) {

            return true;

        }



        $memberIds = CrmGroupMember::query()

            ->whereIn('crm_group_id', $groupIds)

            ->where('is_active', true)

            ->pluck('user_id');



        return $memberIds->contains($lead->marketing_owner_id)

            || $memberIds->contains($lead->assigned_to);

    }



    public static function canAssignLead(User $user, ?SalesLead $lead = null): bool

    {

        if (! self::hasCrmPermission($user, 'crm_assign_leads')) {

            return false;

        }



        $role = self::crmRole($user);



        if ($role === 'super_admin') {

            return true;

        }



        // دور قائد فريق أو معيَّن كقائد مجموعة (team_leader_id)
        if ($role === 'team_leader' || self::leadsActiveCrmGroupsAsLeader($user)) {

            if (! $lead) {

                return self::teamGroupsFor($user)->exists();

            }



            return self::canTeamLeaderManageLead($user, $lead);

        }



        return false;

    }



    public static function canSubmitLeadToSales(User $user, SalesLead $lead): bool
    {
        if (! self::hasCrmPermission($user, 'crm_submit_to_sales') && ! self::hasCrmPermission($user, 'crm_create_leads')) {
            return false;
        }

        if (self::crmRole($user) !== 'marketing' && self::crmRole($user) !== 'super_admin') {
            return false;
        }

        if (self::crmRole($user) === 'marketing' && (int) $lead->marketing_owner_id !== (int) $user->id) {
            return false;
        }

        return ! $lead->isClosed() && ! $lead->assigned_to && ! $lead->submitted_to_sales_at;
    }

    public static function canClaimMarketingLead(User $user, SalesLead $lead): bool
    {
        if (self::crmRole($user) !== 'sales' && self::crmRole($user) !== 'super_admin') {
            return false;
        }

        if (! self::hasCrmPermission($user, 'crm_desk') && self::crmRole($user) !== 'super_admin') {
            return false;
        }

        if ($lead->isClosed()) {
            return false;
        }

        if (! $lead->submitted_to_sales_at && self::crmRole($user) !== 'super_admin') {
            return false;
        }

        if ($lead->assigned_to && (int) $lead->assigned_to !== (int) $user->id) {
            return false;
        }

        return true;
    }

    /**
     * صندوق بيانات المسوقين بالعمولة لمندوبي المبيعات.
     */
    public static function marketingInboxQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return SalesLead::query()
            ->whereNotNull('submitted_to_sales_at')
            ->whereNull('assigned_to')
            ->open();
    }

    /**
     * ليدز جلبها المسوقون وأُسندت للمندوب الحالي (للمتابعة).
     */
    public static function marketerSourcedAssignedQuery(User $salesUser): \Illuminate\Database\Eloquent\Builder
    {
        return SalesLead::query()
            ->where('assigned_to', $salesUser->id)
            ->whereNotNull('marketing_owner_id')
            ->whereColumn('marketing_owner_id', '!=', 'assigned_to');
    }



    public static function canAssignLeadTo(User $actor, SalesLead $lead, User $salesUser): bool

    {

        if (! self::canAssignLead($actor, $lead)) {

            return false;

        }



        if (self::crmRole($actor) === 'super_admin') {

            return true;

        }



        $groupIds = self::teamGroupsFor($actor)->pluck('id');



        return CrmGroupMember::query()

            ->whereIn('crm_group_id', $groupIds)

            ->where('user_id', $salesUser->id)

            ->where('role', 'sales')

            ->where('is_active', true)

            ->exists();

    }



    public static function salesUsersForTeamLeader(User $user): \Illuminate\Support\Collection

    {

        $groupIds = self::teamGroupsFor($user)->pluck('id');



        $userIds = CrmGroupMember::query()

            ->whereIn('crm_group_id', $groupIds)

            ->where('role', 'sales')

            ->where('is_active', true)

            ->pluck('user_id');



        return User::query()->whereIn('id', $userIds)->orderBy('name')->get(['id', 'name']);

    }



    public static function assignableSalesUsers(User $user): \Illuminate\Support\Collection

    {

        if (self::crmRole($user) === 'super_admin') {

            return User::employees()

                ->whereHas('employeeJob', fn ($q) => $q->whereIn('code', ['sales', config('crm.employee_job_codes.sales')]))

                ->orderBy('name')

                ->get(['id', 'name']);

        }



        return self::salesUsersForTeamLeader($user);

    }



    public static function canEditLead(User $user, SalesLead $lead): bool

    {

        if ($lead->isClosed()) {

            return false;

        }



        if (! self::hasCrmPermission($user, 'crm_edit_leads')) {

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



    public static function canTransitionStatus(User $user, SalesLead $lead, string $toStatus): bool

    {

        if (! $lead->canTransitionTo($toStatus)) {

            return false;

        }



        $role = self::crmRole($user);



        if ($role === 'super_admin') {

            return true;

        }



        if ($role === 'finance' && self::hasCrmPermission($user, 'crm_approve_payments')) {

            return in_array($toStatus, [

                SalesLead::STATUS_PAYMENT_CONFIRMED,

            ], true);

        }



        if (! self::hasCrmPermission($user, 'crm_transition_leads')) {

            return false;

        }



        if ($toStatus === SalesLead::STATUS_ASSIGNED) {

            return false;

        }



        if ($role === 'marketing') {

            return false;

        }



        $salesStatuses = SalesLead::salesMovableStatuses();



        if ($role === 'sales') {

            if ($lead->assigned_to !== $user->id) {

                return false;

            }



            return in_array($toStatus, $salesStatuses, true);

        }



        if ($role === 'team_leader') {

            if (! self::canTeamLeaderManageLead($user, $lead)) {

                return false;

            }



            return in_array($toStatus, $salesStatuses, true);

        }



        return false;

    }



    /**
     * حالات يمكن للمستخدم اختيارها فعلياً لتحديث هذا العميل.
     *
     * @return list<string>
     */
    public static function selectableStatusesFor(User $user, SalesLead $lead): array
    {
        if ($lead->isClosed() && ! self::canForceLeadStatus($user)) {
            return [];
        }

        $candidates = array_keys(SalesLead::statusLabels());

        return array_values(array_filter(
            $candidates,
            fn (string $status) => self::canTransitionStatus($user, $lead, $status)
        ));
    }

    public static function canForceLeadStatus(User $user): bool
    {
        return $user->role === 'super_admin' || $user->hasPermission('manage.leads');
    }



    public static function canSeePaymentData(User $user): bool

    {

        $role = self::crmRole($user);



        return in_array($role, ['super_admin', 'finance', 'team_leader'], true)

            || self::hasCrmPermission($user, 'crm_approve_payments');

    }



    public static function canViewAllOrders(User $user): bool

    {

        return self::hasCrmPermission($user, 'crm_view_all_orders')

            || self::crmRole($user) === 'super_admin';

    }



    public static function canApproveCommissions(User $user): bool

    {

        return self::hasCrmPermission($user, 'crm_approve_commissions')

            || self::crmRole($user) === 'super_admin';

    }



    public static function canSubmitReports(User $user): bool

    {

        return self::hasCrmPermission($user, 'crm_submit_reports');

    }



    public static function canUseMessages(User $user, string $action = 'view'): bool

    {

        $key = $action === 'send' ? 'crm_send_messages' : 'crm_view_messages';



        return self::hasCrmPermission($user, $key);

    }



    public static function crmContactsFor(User $user): \Illuminate\Support\Collection

    {

        $role = self::crmRole($user);

        $codes = array_values(config('crm.employee_job_codes'));



        $jobCodes = array_values(array_unique(array_merge(['sales'], $codes)));



        $query = User::employees()

            ->where('id', '!=', $user->id)

            ->whereHas('employeeJob', fn ($j) => $j->whereIn('code', $jobCodes));



        if ($role === 'team_leader') {

            $groupIds = self::teamGroupsFor($user)->pluck('id');

            $memberIds = CrmGroupMember::query()

                ->whereIn('crm_group_id', $groupIds)

                ->where('is_active', true)

                ->pluck('user_id');



            $query->whereIn('id', $memberIds->push($user->id)->unique());

        }



        return $query->orderBy('name')->get(['id', 'name']);

    }



    public static function crmRoleLabel(string $role): string

    {

        return match ($role) {

            'super_admin' => 'إدارة',

            'marketing' => 'تسويق',

            'sales' => 'مبيعات',

            'team_leader' => 'قائد فريق',

            'finance' => 'مالية',

            default => $role,

        };

    }



    public static function canAddNotes(User $user, SalesLead $lead): bool

    {

        if ($lead->isClosed()) {

            return false;

        }



        if (! self::hasCrmPermission($user, 'crm_add_notes')) {

            return false;

        }



        return self::canViewLead($user, $lead);

    }

}

