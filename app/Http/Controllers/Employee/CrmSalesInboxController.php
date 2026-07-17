<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\SalesLead;
use App\Services\Crm\CrmAccessService;
use App\Services\Crm\CrmLeadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CrmSalesInboxController extends Controller
{
    private function gate(): void
    {
        $u = auth()->user();
        abort_unless(
            CrmAccessService::canAccessCrm($u)
            && in_array(CrmAccessService::crmRole($u), ['sales', 'team_leader', 'super_admin'], true),
            403
        );
    }

    /**
     * صندوق بيانات المسوقين بالعمولة + متابعة تقدم الليدز المُسندة من التسويق.
     */
    public function index(Request $request): View
    {
        $this->gate();
        $user = $request->user();
        $role = CrmAccessService::crmRole($user);

        $inbox = CrmAccessService::marketingInboxQuery()
            ->with(['marketingOwner:id,name', 'interestedCourse:id,title'])
            ->latest('submitted_to_sales_at')
            ->paginate(20, ['*'], 'inbox_page')
            ->withQueryString();

        $trackingQuery = $role === 'sales'
            ? CrmAccessService::marketerSourcedAssignedQuery($user)
            : CrmAccessService::leadsQueryFor($user)->whereNotNull('marketing_owner_id')->whereNotNull('assigned_to');

        $tracking = $trackingQuery
            ->with(['marketingOwner:id,name', 'assignedTo:id,name', 'interestedCourse:id,title', 'order.course:id,title'])
            ->latest('assigned_at')
            ->paginate(20, ['*'], 'track_page')
            ->withQueryString();

        $stats = [
            'inbox' => CrmAccessService::marketingInboxQuery()->count(),
            'tracking_open' => (clone $trackingQuery)->open()->count(),
            'tracking_subscribed' => (clone $trackingQuery)->whereIn('status', [
                SalesLead::STATUS_PAYMENT_CONFIRMED,
                SalesLead::STATUS_ENROLLED,
                SalesLead::STATUS_COURSE_ACTIVE,
                SalesLead::STATUS_CLOSED_WON,
            ])->count(),
        ];

        return view('employee.crm.sales.marketing-inbox', [
            'inbox' => $inbox,
            'tracking' => $tracking,
            'stats' => $stats,
            'role' => $role,
            'roleLabel' => CrmAccessService::crmRoleLabel($role),
            'canClaim' => $role === 'sales' || $role === 'super_admin',
        ]);
    }

    public function claim(Request $request, SalesLead $salesLead): RedirectResponse
    {
        $this->gate();

        try {
            CrmLeadService::claimFromMarketingInbox($salesLead, $request->user());
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['claim' => $e->getMessage()]);
        }

        return redirect()
            ->route('employee.crm.leads.show', $salesLead)
            ->with('success', 'تم استلام العميل من المسوق. يمكنك متابعة تقدمه في الـ Pipeline.');
    }
}
