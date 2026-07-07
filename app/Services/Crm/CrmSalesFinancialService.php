<?php

namespace App\Services\Crm;

use App\Models\CrmCommission;
use App\Models\CrmGroup;
use App\Models\CrmGroupMember;
use App\Models\CrmReport;
use App\Models\EmployeeJob;
use App\Models\Order;
use App\Models\SalesLead;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class CrmSalesFinancialService
{
    /**
     * @return array<string, mixed>
     */
    public static function build(string $period = 'month'): array
    {
        [$start, $end] = self::periodBounds($period);

        $ordersQuery = Order::query()->whereBetween('created_at', [$start, $end]);
        $approvedOrders = (clone $ordersQuery)->where('status', Order::STATUS_APPROVED);
        $crmLinkedOrders = (clone $ordersQuery)->where(function ($q) {
            $q->whereNotNull('sales_lead_id')
                ->orWhereNotNull('sales_owner_id');
        });

        $commissionsQuery = CrmCommission::query()->whereBetween('created_at', [$start, $end]);

        $leadStatuses = SalesLead::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $revenueBySales = Order::query()
            ->join('users', 'users.id', '=', 'orders.sales_owner_id')
            ->where('orders.status', Order::STATUS_APPROVED)
            ->whereBetween('orders.approved_at', [$start, $end])
            ->groupBy('orders.sales_owner_id', 'users.name')
            ->selectRaw('orders.sales_owner_id, users.name as sales_name, SUM(orders.amount) as revenue, COUNT(*) as orders_count')
            ->orderByDesc('revenue')
            ->get();

        $crmJobIds = EmployeeJob::query()
            ->whereIn('code', array_values(config('crm.employee_job_codes')))
            ->pluck('id');

        $submittedReports = CrmReport::query()
            ->with(['user:id,name', 'group:id,name'])
            ->whereIn('user_id', User::employees()->whereIn('employee_job_id', $crmJobIds)->select('id'))
            ->whereBetween('created_at', [$start, $end])
            ->latest()
            ->limit(50)
            ->get();

        return [
            'period' => $period,
            'start' => $start,
            'end' => $end,
            'orders' => [
                'total' => (clone $ordersQuery)->count(),
                'pending' => (clone $ordersQuery)->where('status', Order::STATUS_PENDING)->count(),
                'approved' => (clone $approvedOrders)->count(),
                'rejected' => (clone $ordersQuery)->where('status', Order::STATUS_REJECTED)->count(),
                'revenue_approved' => (float) (clone $approvedOrders)->sum('amount'),
                'crm_linked' => (clone $crmLinkedOrders)->count(),
                'crm_revenue' => (float) (clone $crmLinkedOrders)->where('status', Order::STATUS_APPROVED)->sum('amount'),
            ],
            'commissions' => [
                'pending' => (float) (clone $commissionsQuery)->where('status', CrmCommission::STATUS_PENDING)->sum('commission_amount_egp'),
                'approved' => (float) (clone $commissionsQuery)->where('status', CrmCommission::STATUS_APPROVED)->sum('commission_amount_egp'),
                'paid' => (float) (clone $commissionsQuery)->where('status', CrmCommission::STATUS_PAID)->sum('commission_amount_egp'),
                'by_type' => (clone $commissionsQuery)
                    ->selectRaw('type, SUM(commission_amount_egp) as total')
                    ->groupBy('type')
                    ->pluck('total', 'type'),
            ],
            'leads' => [
                'total' => SalesLead::count(),
                'open' => SalesLead::query()->open()->count(),
                'payment_pending' => (int) ($leadStatuses[SalesLead::STATUS_PAYMENT_PENDING] ?? 0),
                'closed_won' => (int) ($leadStatuses[SalesLead::STATUS_CLOSED_WON] ?? 0),
                'by_status' => $leadStatuses,
            ],
            'revenue_by_sales' => $revenueBySales,
            'submitted_reports' => $submittedReports,
            'teams_count' => CrmGroup::where('is_active', true)->count(),
            'active_sales_members' => CrmGroupMember::query()
                ->where('role', 'sales')
                ->where('is_active', true)
                ->distinct('user_id')
                ->count('user_id'),
        ];
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    public static function allTeamsPerformance(): Collection
    {
        $stats = collect();

        $groups = CrmGroup::query()
            ->where('is_active', true)
            ->with(['activeMembers.user:id,name'])
            ->get();

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
                    'group_name' => $group->name,
                    'user_name' => $user->name,
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

        return $stats->sortByDesc('revenue')->values();
    }

    /**
     * @return array{0: Carbon, 1: Carbon}
     */
    private static function periodBounds(string $period): array
    {
        return match ($period) {
            'week' => [now()->startOfWeek(), now()->endOfWeek()],
            'quarter' => [now()->startOfQuarter(), now()->endOfQuarter()],
            'year' => [now()->startOfYear(), now()->endOfYear()],
            default => [now()->startOfMonth(), now()->endOfMonth()],
        };
    }
}
