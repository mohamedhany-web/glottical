<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CrmGroup;
use App\Models\CrmGroupMember;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CrmGroupController extends Controller
{
    public function index(): View
    {
        abort_unless(auth()->user()->hasPermission('manage.leads') || auth()->user()->role === 'super_admin', 403);

        $groups = CrmGroup::with(['teamLeader:id,name', 'members.user:id,name'])
            ->withCount('leads')
            ->orderBy('name')
            ->get();

        return view('admin.crm.groups.index', compact('groups'));
    }

    public function create(): View
    {
        abort_unless(auth()->user()->hasPermission('manage.leads') || auth()->user()->role === 'super_admin', 403);

        $leaders = User::employees()
            ->whereHas('employeeJob', fn ($q) => $q->where('code', config('crm.employee_job_codes.team_leader')))
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('admin.crm.groups.create', compact('leaders'));
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless(auth()->user()->hasPermission('manage.leads') || auth()->user()->role === 'super_admin', 403);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'team_leader_id' => ['nullable', 'exists:users,id'],
        ]);

        CrmGroup::create($data);

        return redirect()->route('admin.crm.groups.index')->with('success', 'تم إنشاء المجموعة.');
    }

    public function edit(CrmGroup $group): View
    {
        abort_unless(auth()->user()->hasPermission('manage.leads') || auth()->user()->role === 'super_admin', 403);

        $group->load(['members.user', 'teamLeader:id,name'])->loadCount('leads');
        $leaders = User::employees()
            ->whereHas('employeeJob', fn ($q) => $q->where('code', config('crm.employee_job_codes.team_leader')))
            ->orderBy('name')
            ->get(['id', 'name']);

        $marketingUsers = User::employees()
            ->whereHas('employeeJob', fn ($q) => $q->where('code', config('crm.employee_job_codes.marketing')))
            ->orderBy('name')
            ->get(['id', 'name']);

        $salesUsers = User::employees()
            ->whereHas('employeeJob', fn ($q) => $q->whereIn('code', ['sales', config('crm.employee_job_codes.sales')]))
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('admin.crm.groups.edit', compact('group', 'leaders', 'marketingUsers', 'salesUsers'));
    }

    public function update(Request $request, CrmGroup $group): RedirectResponse
    {
        abort_unless(auth()->user()->hasPermission('manage.leads') || auth()->user()->role === 'super_admin', 403);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'team_leader_id' => ['nullable', 'exists:users,id'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $group->update([
            'name' => $data['name'],
            'team_leader_id' => $data['team_leader_id'] ?? null,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return back()->with('success', 'تم تحديث المجموعة.');
    }

    public function addMember(Request $request, CrmGroup $group): RedirectResponse
    {
        abort_unless(auth()->user()->hasPermission('manage.leads') || auth()->user()->role === 'super_admin', 403);

        $data = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'role' => ['required', 'in:marketing,sales'],
        ]);

        CrmGroupMember::updateOrCreate(
            ['crm_group_id' => $group->id, 'user_id' => $data['user_id'], 'role' => $data['role']],
            ['is_active' => true]
        );

        return back()->with('success', 'تمت إضافة العضو للمجموعة.');
    }

    public function removeMember(CrmGroup $group, CrmGroupMember $member): RedirectResponse
    {
        abort_unless(auth()->user()->hasPermission('manage.leads') || auth()->user()->role === 'super_admin', 403);
        abort_unless($member->crm_group_id === $group->id, 404);

        $member->update(['is_active' => false]);

        return back()->with('success', 'تم إلغاء تفعيل العضو.');
    }
}
