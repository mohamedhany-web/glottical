<?php

namespace App\Services\Crm;

use App\Models\CrmCommission;
use App\Models\SalesLead;
use App\Models\User;
use Illuminate\Support\Collection;

class CrmMarketingPerformanceService
{
    /**
     * إحصاءات المسوق بالعمولة: العملاء الذين جلبهم + الاشتراكات + العمولة.
     *
     * @return array<string, mixed>
     */
    public static function summaryFor(User $marketer): array
    {
        $base = SalesLead::query()->where('marketing_owner_id', $marketer->id);
        $subscribedStatuses = [
            SalesLead::STATUS_PAYMENT_CONFIRMED,
            SalesLead::STATUS_ENROLLED,
            SalesLead::STATUS_COURSE_ACTIVE,
            SalesLead::STATUS_RENEWAL,
            SalesLead::STATUS_CLOSED_WON,
        ];

        $subscribedQuery = (clone $base)->whereIn('status', $subscribedStatuses);

        $commissions = CrmCommission::query()
            ->where('user_id', $marketer->id)
            ->where('type', CrmCommission::TYPE_MARKETING);

        $rate = (float) config('crm.commission_rates.marketing', 5);

        $subscribedLeads = (clone $subscribedQuery)
            ->with(['interestedCourse:id,title', 'order.course:id,title'])
            ->get();

        $byCourse = $subscribedLeads
            ->groupBy(function (SalesLead $lead) {
                $course = $lead->order?->course ?? $lead->interestedCourse;

                return $course?->id ? 'id:'.$course->id : 'none';
            })
            ->map(function (Collection $group) {
                $sample = $group->first();
                $course = $sample->order?->course ?? $sample->interestedCourse;

                return [
                    'course_id' => $course?->id,
                    'course_title' => $course?->title ?? 'بدون كورس محدد',
                    'subscribers' => $group->count(),
                    'revenue' => (float) $group->sum(fn (SalesLead $l) => (float) ($l->order?->amount ?? 0)),
                ];
            })
            ->sortByDesc('subscribers')
            ->values();

        return [
            'commission_percent' => $rate,
            'total_leads' => (clone $base)->count(),
            'open_leads' => (clone $base)->open()->count(),
            'submitted_to_sales' => (clone $base)->whereNotNull('submitted_to_sales_at')->count(),
            'awaiting_sales' => (clone $base)->whereNotNull('submitted_to_sales_at')->whereNull('assigned_to')->open()->count(),
            'in_sales_pipeline' => (clone $base)->whereNotNull('assigned_to')->open()->count(),
            'subscribed_count' => $subscribedLeads->count(),
            'closed_won' => (clone $base)->where('status', SalesLead::STATUS_CLOSED_WON)->count(),
            'closed_lost' => (clone $base)->where('status', SalesLead::STATUS_CLOSED_LOST)->count(),
            'conversion_rate' => (clone $base)->count() > 0
                ? round(($subscribedLeads->count() / (clone $base)->count()) * 100, 1)
                : 0.0,
            'commissions_pending' => (float) (clone $commissions)->where('status', CrmCommission::STATUS_PENDING)->sum('commission_amount_egp'),
            'commissions_approved' => (float) (clone $commissions)->where('status', CrmCommission::STATUS_APPROVED)->sum('commission_amount_egp'),
            'commissions_paid' => (float) (clone $commissions)->where('status', CrmCommission::STATUS_PAID)->sum('commission_amount_egp'),
            'commissions_total' => (float) (clone $commissions)->sum('commission_amount_egp'),
            'by_course' => $byCourse,
        ];
    }

    /**
     * @return Collection<int, SalesLead>
     */
    public static function recentLeads(User $marketer, int $limit = 20): Collection
    {
        return SalesLead::query()
            ->where('marketing_owner_id', $marketer->id)
            ->with(['assignedTo:id,name', 'interestedCourse:id,title', 'order.course:id,title', 'linkedUser:id,name'])
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * العملاء الذين اشتركوا فعلياً.
     *
     * @return Collection<int, SalesLead>
     */
    public static function subscribers(User $marketer, int $limit = 50): Collection
    {
        return SalesLead::query()
            ->where('marketing_owner_id', $marketer->id)
            ->whereIn('status', [
                SalesLead::STATUS_PAYMENT_CONFIRMED,
                SalesLead::STATUS_ENROLLED,
                SalesLead::STATUS_COURSE_ACTIVE,
                SalesLead::STATUS_RENEWAL,
                SalesLead::STATUS_CLOSED_WON,
            ])
            ->with(['linkedUser:id,name,email', 'interestedCourse:id,title', 'order.course:id,title', 'assignedTo:id,name'])
            ->latest('converted_at')
            ->latest()
            ->limit($limit)
            ->get();
    }
}
