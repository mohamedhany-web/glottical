<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CrmCommission;
use App\Services\Crm\CrmCommissionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CrmCommissionController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->hasPermission('manage.leads') || $request->user()->role === 'super_admin', 403);

        $query = CrmCommission::with(['user:id,name', 'lead:id,name', 'order:id,amount']);

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        $commissions = $query->latest()->paginate(30)->withQueryString();

        $stats = [
            'pending' => CrmCommission::where('status', CrmCommission::STATUS_PENDING)->count(),
            'approved' => CrmCommission::where('status', CrmCommission::STATUS_APPROVED)->count(),
            'total_amount' => (float) CrmCommission::sum('commission_amount_egp'),
        ];

        return view('admin.crm.commissions.index', compact('commissions', 'stats'));
    }

    public function approve(Request $request, CrmCommission $commission): RedirectResponse
    {
        abort_unless($request->user()->hasPermission('manage.leads') || $request->user()->role === 'super_admin', 403);

        CrmCommissionService::approveCommission($commission, $request->user());

        return back()->with('success', 'تم اعتماد العمولة.');
    }
}
