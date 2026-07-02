<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\AdvancedCourse;
use App\Models\CrmCommission;
use App\Models\SalesLead;
use App\Services\Crm\CrmAccessService;
use App\Services\Crm\CrmLeadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CrmWorkspaceController extends Controller
{
    private function gate(): void
    {
        abort_unless(CrmAccessService::canAccessCrm(auth()->user()), 403);
    }

    public function dashboard(Request $request): View
    {
        $this->gate();
        $user = $request->user();
        $role = CrmAccessService::crmRole($user);
        $baseQuery = CrmAccessService::leadsQueryFor($user);

        $stats = [
            'my_leads' => (clone $baseQuery)->count(),
            'open' => (clone $baseQuery)->open()->count(),
            'closed_won' => (clone $baseQuery)->where('status', SalesLead::STATUS_CLOSED_WON)->count(),
            'payment_pending' => (clone $baseQuery)->where('status', SalesLead::STATUS_PAYMENT_PENDING)->count(),
        ];

        if ($role === 'marketing') {
            $stats['subscriptions'] = (clone $baseQuery)->whereIn('status', [
                SalesLead::STATUS_ENROLLED, SalesLead::STATUS_COURSE_ACTIVE, SalesLead::STATUS_CLOSED_WON,
            ])->count();
            $stats['my_commissions'] = CrmCommission::where('user_id', $user->id)->sum('commission_amount_egp');
        }

        if ($role === 'sales') {
            $stats['my_commissions'] = CrmCommission::where('user_id', $user->id)->sum('commission_amount_egp');
        }

        if ($role === 'team_leader') {
            $stats['team_revenue'] = (float) (clone $baseQuery)
                ->whereNotNull('order_id')
                ->join('orders', 'orders.id', '=', 'sales_leads.order_id')
                ->sum('orders.amount');
            $stats['team_commissions'] = CrmCommission::query()
                ->whereIn('sales_lead_id', (clone $baseQuery)->pluck('sales_leads.id'))
                ->sum('commission_amount_egp');
        }

        if ($role === 'finance') {
            $stats['pending_commissions'] = CrmCommission::where('status', CrmCommission::STATUS_PENDING)->count();
            $stats['pending_payments'] = SalesLead::where('status', SalesLead::STATUS_PAYMENT_PENDING)->count();
        }

        $recentLeads = (clone $baseQuery)->with(['assignedTo:id,name', 'marketingOwner:id,name'])
            ->latest()
            ->limit(8)
            ->get();

        return view('employee.crm.dashboard', compact('stats', 'recentLeads', 'role'));
    }

    public function leadsIndex(Request $request): View
    {
        $this->gate();

        $query = CrmAccessService::leadsQueryFor($request->user())
            ->with(['marketingOwner:id,name', 'assignedTo:id,name', 'interestedCourse:id,title']);

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        $leads = $query->latest()->paginate(20)->withQueryString();

        return view('employee.crm.leads.index', [
            'leads' => $leads,
            'statusLabels' => SalesLead::statusLabels(),
            'role' => CrmAccessService::crmRole($request->user()),
        ]);
    }

    public function leadsCreate(): View
    {
        $this->gate();
        abort_unless(in_array(CrmAccessService::crmRole(auth()->user()), ['marketing', 'super_admin'], true), 403);

        $courses = AdvancedCourse::orderBy('title')->get(['id', 'title']);

        return view('employee.crm.leads.create', compact('courses'));
    }

    public function leadsStore(Request $request): RedirectResponse
    {
        $this->gate();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:64',
            'company' => 'nullable|string|max:255',
            'source' => ['required', Rule::in(array_keys(SalesLead::sourceLabels()))],
            'notes' => 'nullable|string|max:10000',
            'interested_advanced_course_id' => 'nullable|exists:advanced_courses,id',
        ]);

        try {
            $lead = CrmLeadService::createLead($validated, $request->user());
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['name' => $e->getMessage()])->withInput();
        }

        return redirect()->route('employee.crm.leads.show', $lead)->with('success', 'تم إنشاء الـ Lead بنجاح.');
    }

    public function leadsShow(Request $request, SalesLead $salesLead): View
    {
        $this->gate();
        abort_unless(CrmAccessService::canViewLead($request->user(), $salesLead), 403);

        $salesLead->load(['marketingOwner', 'assignedTo', 'interestedCourse', 'auditLogs.user', 'commissions']);

        return view('employee.crm.leads.show', [
            'lead' => $salesLead,
            'role' => CrmAccessService::crmRole($request->user()),
            'canEdit' => CrmAccessService::canEditLead($request->user(), $salesLead),
            'canSeePayment' => CrmAccessService::canSeePaymentData($request->user()),
            'nextStatuses' => SalesLead::allowedTransitions()[$salesLead->status] ?? [],
        ]);
    }

    public function leadsUpdate(Request $request, SalesLead $salesLead): RedirectResponse
    {
        $this->gate();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:64',
            'company' => 'nullable|string|max:255',
            'source' => ['required', Rule::in(array_keys(SalesLead::sourceLabels()))],
            'notes' => 'nullable|string|max:10000',
            'interested_advanced_course_id' => 'nullable|exists:advanced_courses,id',
        ]);

        try {
            CrmLeadService::updateLead($salesLead, $validated, $request->user());
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['name' => $e->getMessage()]);
        }

        return back()->with('success', 'تم حفظ التعديلات.');
    }

    public function transition(Request $request, SalesLead $salesLead): RedirectResponse
    {
        $this->gate();

        $data = $request->validate([
            'status' => ['required', Rule::in(array_keys(SalesLead::statusLabels()))],
            'note' => ['nullable', 'string', 'max:5000'],
        ]);

        try {
            CrmLeadService::transitionStatus($salesLead, $data['status'], $request->user(), $data['note'] ?? null);
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['status' => $e->getMessage()]);
        }

        return back()->with('success', 'تم تحديث الحالة.');
    }

    public function addNote(Request $request, SalesLead $salesLead): RedirectResponse
    {
        $this->gate();

        $data = $request->validate(['note' => ['required', 'string', 'max:5000']]);
        CrmLeadService::addNote($salesLead, $data['note'], $request->user());

        return back()->with('success', 'تمت إضافة الملاحظة.');
    }

    public function commissions(Request $request): View
    {
        $this->gate();
        $role = CrmAccessService::crmRole($request->user());

        $query = CrmCommission::with(['lead:id,name', 'order:id,amount']);

        if ($role === 'finance' || $role === 'super_admin') {
            // all
        } else {
            $query->where('user_id', $request->user()->id);
        }

        $commissions = $query->latest()->paginate(25);

        return view('employee.crm.commissions', compact('commissions', 'role'));
    }

    public function approveCommission(Request $request, CrmCommission $commission): RedirectResponse
    {
        $this->gate();
        abort_unless(in_array(CrmAccessService::crmRole($request->user()), ['finance', 'super_admin'], true), 403);

        \App\Services\Crm\CrmCommissionService::approveCommission($commission, $request->user());

        return back()->with('success', 'تم اعتماد العمولة.');
    }

    public function confirmPayment(Request $request, SalesLead $salesLead): RedirectResponse
    {
        $this->gate();
        abort_unless(in_array(CrmAccessService::crmRole($request->user()), ['finance', 'super_admin'], true), 403);

        try {
            CrmLeadService::transitionStatus($salesLead, SalesLead::STATUS_PAYMENT_CONFIRMED, $request->user());
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['status' => $e->getMessage()]);
        }

        return back()->with('success', 'تم تأكيد الدفع في CRM.');
    }
}
