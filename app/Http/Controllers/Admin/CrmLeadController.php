<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdvancedCourse;
use App\Models\CrmGroup;
use App\Models\SalesLead;
use App\Models\User;
use App\Services\Crm\CrmAccessService;
use App\Services\Crm\CrmLeadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CrmLeadController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->hasPermission('manage.leads') || $request->user()->role === 'super_admin', 403);

        $query = SalesLead::query()
            ->with(['marketingOwner:id,name', 'assignedTo:id,name', 'interestedCourse:id,title', 'crmGroup:id,name']);

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }
        if ($request->filled('search')) {
            $s = '%'.trim($request->string('search')).'%';
            $query->where(fn ($q) => $q->where('name', 'like', $s)->orWhere('email', 'like', $s)->orWhere('phone', 'like', $s));
        }

        $leads = $query->latest()->paginate(25)->withQueryString();

        return view('admin.crm.leads.index', [
            'leads' => $leads,
            'statusLabels' => SalesLead::statusLabels(),
        ]);
    }

    public function show(Request $request, SalesLead $salesLead): View
    {
        abort_unless($request->user()->hasPermission('manage.leads') || $request->user()->role === 'super_admin', 403);

        $salesLead->load([
            'marketingOwner', 'assignedTo', 'creator', 'linkedUser',
            'order', 'convertedOrder.course', 'interestedCourse', 'crmGroup.teamLeader',
            'commissions.user', 'auditLogs.user',
        ]);

        $salesUsers = User::employees()
            ->whereHas('employeeJob', fn ($q) => $q->whereIn('code', ['sales', config('crm.employee_job_codes.sales')]))
            ->orderBy('name')
            ->get(['id', 'name']);

        $groups = CrmGroup::where('is_active', true)->orderBy('name')->get();

        return view('admin.crm.leads.show', compact('salesLead', 'salesUsers', 'groups'));
    }

    public function assign(Request $request, SalesLead $salesLead): RedirectResponse
    {
        abort_unless(CrmAccessService::canAssignLead($request->user()), 403);

        $data = $request->validate([
            'assigned_to' => ['required', 'exists:users,id'],
            'crm_group_id' => ['nullable', 'exists:crm_groups,id'],
        ]);

        $salesUser = User::findOrFail($data['assigned_to']);
        CrmLeadService::assignToSales($salesLead, $salesUser, $request->user(), $data['crm_group_id'] ?? null);

        return back()->with('success', 'تم تعيين الـ Lead لموظف المبيعات.');
    }

    public function transition(Request $request, SalesLead $salesLead): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(array_keys(SalesLead::statusLabels()))],
            'note' => ['nullable', 'string', 'max:5000'],
        ]);

        try {
            CrmLeadService::transitionStatus($salesLead, $data['status'], $request->user(), $data['note'] ?? null);
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['status' => $e->getMessage()]);
        }

        return back()->with('success', 'تم تحديث حالة الـ Lead.');
    }

    public function addNote(Request $request, SalesLead $salesLead): RedirectResponse
    {
        $data = $request->validate(['note' => ['required', 'string', 'max:5000']]);

        CrmLeadService::addNote($salesLead, $data['note'], $request->user());

        return back()->with('success', 'تمت إضافة الملاحظة.');
    }
}
