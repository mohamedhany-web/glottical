<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SalesLead;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CrmPipelineController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->hasPermission('manage.leads') || $request->user()->role === 'super_admin', 403);

        $labels = SalesLead::statusLabels();
        $stages = array_merge(SalesLead::pipelineStages(), [SalesLead::STATUS_CLOSED_LOST]);

        $counts = SalesLead::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $columns = [];
        foreach ($stages as $status) {
            $columns[$status] = [
                'label' => $labels[$status] ?? $status,
                'count' => (int) ($counts[$status] ?? 0),
                'leads' => SalesLead::query()
                    ->where('status', $status)
                    ->with(['assignedTo:id,name', 'marketingOwner:id,name'])
                    ->latest()
                    ->limit(12)
                    ->get(),
            ];
        }

        return view('admin.crm.pipeline.index', [
            'columns' => $columns,
            'statusLabels' => $labels,
            'totalLeads' => (int) $counts->sum(),
            'openLeads' => SalesLead::query()->open()->count(),
        ]);
    }
}
