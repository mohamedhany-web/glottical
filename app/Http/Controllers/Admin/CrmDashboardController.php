<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CrmCommission;
use App\Models\SalesLead;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CrmDashboardController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->hasPermission('manage.leads') || $request->user()->role === 'super_admin', 403);

        $stats = [
            'total_leads' => SalesLead::count(),
            'open_leads' => SalesLead::open()->count(),
            'payment_pending' => SalesLead::where('status', SalesLead::STATUS_PAYMENT_PENDING)->count(),
            'closed_won' => SalesLead::where('status', SalesLead::STATUS_CLOSED_WON)->count(),
            'commissions_pending' => CrmCommission::where('status', CrmCommission::STATUS_PENDING)->count(),
            'commissions_total' => (float) CrmCommission::sum('commission_amount_egp'),
        ];

        $recentLeads = SalesLead::with(['marketingOwner:id,name', 'assignedTo:id,name'])
            ->latest()
            ->limit(10)
            ->get();

        $statusBreakdown = SalesLead::query()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('admin.crm.dashboard', compact('stats', 'recentLeads', 'statusBreakdown'));
    }
}
