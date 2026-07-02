<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CrmAuditLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CrmAuditLogController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->hasPermission('manage.leads') || $request->user()->role === 'super_admin', 403);

        $query = CrmAuditLog::with(['user:id,name', 'lead:id,name']);

        if ($request->filled('action')) {
            $query->where('action', $request->string('action'));
        }
        if ($request->filled('lead_id')) {
            $query->where('sales_lead_id', (int) $request->input('lead_id'));
        }

        $logs = $query->orderByDesc('created_at')->paginate(40)->withQueryString();

        return view('admin.crm.audit.index', [
            'logs' => $logs,
            'actionLabels' => CrmAuditLog::actionLabels(),
        ]);
    }
}
