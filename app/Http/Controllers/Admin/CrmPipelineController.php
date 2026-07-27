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

        $totalLeads = (int) $counts->sum();
        $openLeads = SalesLead::query()->open()->count();

        $stagesSummary = collect($stages)->map(function (string $status) use ($labels, $counts, $totalLeads) {
            $count = (int) ($counts[$status] ?? 0);

            return [
                'status' => $status,
                'label' => $labels[$status] ?? $status,
                'count' => $count,
                'percent' => $totalLeads > 0 ? round(($count / $totalLeads) * 100, 1) : 0.0,
            ];
        })->values();

        return view('admin.crm.pipeline.index', [
            'stagesSummary' => $stagesSummary,
            'statusLabels' => $labels,
            'totalLeads' => $totalLeads,
            'openLeads' => $openLeads,
            'closedWon' => (int) ($counts[SalesLead::STATUS_CLOSED_WON] ?? 0),
            'closedLost' => (int) ($counts[SalesLead::STATUS_CLOSED_LOST] ?? 0),
            'paymentPending' => (int) ($counts[SalesLead::STATUS_PAYMENT_PENDING] ?? 0),
        ]);
    }
}
