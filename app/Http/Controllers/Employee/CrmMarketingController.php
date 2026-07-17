<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\CrmCommission;
use App\Models\SalesLead;
use App\Services\Crm\CrmAccessService;
use App\Services\Crm\CrmLeadService;
use App\Services\Crm\CrmMarketingPerformanceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CrmMarketingController extends Controller
{
    private function gate(): void
    {
        abort_unless(CrmAccessService::canAccessCrm(auth()->user()), 403);
    }

    /**
     * صفحة المسوق بالعمولة: عملاؤه، الاشتراكات، الكورسات، العمولة والتقدم.
     */
    public function desk(Request $request): View
    {
        $this->gate();
        $user = $request->user();
        abort_unless(
            in_array(CrmAccessService::crmRole($user), ['marketing', 'super_admin'], true)
            || CrmAccessService::hasCrmPermission($user, 'crm_create_leads'),
            403
        );

        $summary = CrmMarketingPerformanceService::summaryFor($user);
        $leads = CrmMarketingPerformanceService::recentLeads($user, 30);
        $subscribers = CrmMarketingPerformanceService::subscribers($user, 40);
        $recentCommissions = CrmCommission::query()
            ->where('user_id', $user->id)
            ->where('type', CrmCommission::TYPE_MARKETING)
            ->with('lead:id,name')
            ->latest()
            ->limit(12)
            ->get();

        return view('employee.crm.marketing.desk', [
            'summary' => $summary,
            'leads' => $leads,
            'subscribers' => $subscribers,
            'recentCommissions' => $recentCommissions,
            'role' => CrmAccessService::crmRole($user),
            'roleLabel' => CrmAccessService::crmRoleLabel(CrmAccessService::crmRole($user)),
        ]);
    }

    public function submitToSales(Request $request, SalesLead $salesLead): RedirectResponse
    {
        $this->gate();
        $data = $request->validate([
            'note' => ['nullable', 'string', 'max:2000'],
        ]);

        try {
            CrmLeadService::submitToSales($salesLead, $request->user(), $data['note'] ?? null);
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['submit' => $e->getMessage()]);
        }

        return back()->with('success', 'تم إرسال العميل لصندوق المبيعات. سيظهر عند مندوبي المبيعات لاستلامه.');
    }
}
