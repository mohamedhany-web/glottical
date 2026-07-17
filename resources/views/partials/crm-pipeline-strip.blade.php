@php
    $pipelineLead = $pipelineLead ?? $lead ?? $salesLead ?? null;
    $stages = \App\Models\SalesLead::pipelineStages();
    $labels = \App\Models\SalesLead::statusLabels();
    $current = $pipelineLead?->status;
    $currentIdx = $pipelineLead ? $pipelineLead->pipelineIndex() : -1;
    $isLost = $current === \App\Models\SalesLead::STATUS_CLOSED_LOST;
@endphp
@if($pipelineLead)
<div class="rounded-2xl border bg-white p-4 sm:p-5 space-y-3">
    <div class="flex flex-wrap items-center justify-between gap-2">
        <h3 class="font-bold text-gray-900">مسار البيع (Pipeline)</h3>
        <span class="text-xs font-bold px-2.5 py-1 rounded-full {{ $isLost ? 'bg-rose-100 text-rose-800' : 'bg-indigo-100 text-indigo-800' }}">
            {{ $labels[$current] ?? $current }}
        </span>
    </div>
    <div class="overflow-x-auto pb-1">
        <ol class="flex min-w-max gap-1.5">
            @foreach($stages as $i => $stage)
                @php
                    $done = !$isLost && $currentIdx >= 0 && $i < $currentIdx;
                    $active = !$isLost && $stage === $current;
                @endphp
                <li class="flex items-center gap-1.5">
                    <span @class([
                        'inline-flex items-center rounded-lg px-2.5 py-1.5 text-[11px] sm:text-xs font-bold border whitespace-nowrap',
                        'bg-emerald-600 text-white border-emerald-600' => $done,
                        'bg-violet-600 text-white border-violet-600 ring-2 ring-violet-300' => $active,
                        'bg-slate-50 text-slate-500 border-slate-200' => ! $done && ! $active,
                    ])>{{ $labels[$stage] }}</span>
                    @if(! $loop->last)
                        <span class="text-slate-300 text-xs">›</span>
                    @endif
                </li>
            @endforeach
        </ol>
    </div>
    @if($isLost)
        <p class="text-xs text-rose-700">هذا العميل مغلق كخاسر{{ $pipelineLead->lost_reason ? ': '.$pipelineLead->lost_reason : '' }}</p>
    @endif
</div>
@endif
