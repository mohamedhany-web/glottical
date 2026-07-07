<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Services\Crm\CrmAccessService;
use App\Services\Crm\CrmSalesFinancialService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CrmSalesFinancialController extends Controller
{
    private function gate(): void
    {
        abort_unless(CrmAccessService::canAccessCrm(auth()->user()), 403);
        abort_unless(CrmAccessService::canViewSalesFinancialReports(auth()->user()), 403);
    }

    public function index(Request $request): View
    {
        $this->gate();

        $period = $request->string('period', 'month')->toString();
        if (! in_array($period, ['week', 'month', 'quarter', 'year'], true)) {
            $period = 'month';
        }

        $report = CrmSalesFinancialService::build($period);
        $teamPerformance = CrmAccessService::canViewTeamPerformance($request->user())
            ? CrmSalesFinancialService::allTeamsPerformance()
            : collect();

        return view('employee.crm.sales-financial', [
            'report' => $report,
            'teamPerformance' => $teamPerformance,
            'period' => $period,
            'role' => CrmAccessService::crmRole($request->user()),
        ]);
    }
}
