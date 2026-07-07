<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\CrmGroup;
use App\Models\CrmGroupMember;
use App\Models\User;
use App\Services\Crm\CrmAccessService;
use App\Services\Crm\CrmTeamService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CrmTeamController extends Controller
{
    private function gate(): void
    {
        abort_unless(CrmAccessService::canAccessCrm(auth()->user()), 403);
    }

    public function index(Request $request): View
    {
        $this->gate();
        $user = $request->user();
        abort_unless(CrmAccessService::canViewTeamPerformance($user) || CrmAccessService::canManageTeam($user), 403);

        $groups = CrmAccessService::teamsVisibleTo($user)
            ->with(['activeMembers.user:id,name', 'teamLeader:id,name'])
            ->withCount('leads')
            ->get();

        $memberStats = CrmAccessService::canViewTeamPerformance($user)
            ? (CrmAccessService::crmRole($user) === 'finance'
                ? \App\Services\Crm\CrmSalesFinancialService::allTeamsPerformance()
                : \App\Services\Crm\CrmTeamService::memberPerformanceStats($user))
            : collect();

        $marketingUsers = User::employees()
            ->whereHas('employeeJob', fn ($q) => $q->where('code', config('crm.employee_job_codes.marketing')))
            ->orderBy('name')
            ->get(['id', 'name']);

        $salesUsers = User::employees()
            ->whereHas('employeeJob', fn ($q) => $q->whereIn('code', ['sales', config('crm.employee_job_codes.sales')]))
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('employee.crm.team.index', [
            'groups' => $groups,
            'memberStats' => $memberStats,
            'marketingUsers' => $marketingUsers,
            'salesUsers' => $salesUsers,
            'canManage' => CrmAccessService::canManageTeam($user),
            'role' => CrmAccessService::crmRole($user),
        ]);
    }

    public function addMember(Request $request, CrmGroup $group): RedirectResponse
    {
        $this->gate();
        abort_unless(CrmAccessService::canManageTeam($request->user(), $group), 403);

        $data = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'role' => ['required', 'in:marketing,sales'],
        ]);

        try {
            CrmTeamService::addMember($group, (int) $data['user_id'], $data['role'], $request->user());
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['user_id' => $e->getMessage()]);
        }

        return back()->with('success', 'تمت إضافة العضو للفريق.');
    }

    public function removeMember(Request $request, CrmGroup $group, CrmGroupMember $member): RedirectResponse
    {
        $this->gate();
        abort_unless(CrmAccessService::canManageTeam($request->user(), $group), 403);

        try {
            CrmTeamService::removeMember($group, $member, $request->user());
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['member' => $e->getMessage()]);
        }

        return back()->with('success', 'تم إلغاء تفعيل العضو.');
    }
}
