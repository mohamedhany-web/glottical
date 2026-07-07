@extends('layouts.admin')

@section('title', 'سجل المتابعة')
@section('header', 'CRM — سجل المتابعة')

@section('content')
<div class="space-y-4">
    @include('partials.crm-admin-nav')
    <p class="text-sm text-slate-600">السجلات للقراءة فقط — لا يُحذف أي سجل. كل إجراء موثّق للشفافية.</p>
    <form method="GET" class="flex gap-2">
        <select name="action" class="rounded-lg border px-3 py-2 text-sm">
            <option value="">كل العمليات</option>
            @foreach($actionLabels as $k => $l)<option value="{{ $k }}" @selected(request('action')===$k)>{{ $l }}</option>@endforeach
        </select>
        <button class="px-4 py-2 rounded-lg bg-slate-800 text-white text-sm font-bold">تصفية</button>
    </form>
    <div class="rounded-2xl bg-white border divide-y text-sm">
        @foreach($logs as $log)
            <div class="px-5 py-3 flex flex-wrap justify-between gap-2">
                <div>
                    <span class="font-bold">{{ $log->actionLabel() }}</span>
                    @if($log->lead)<a href="{{ route('admin.crm.leads.show', $log->lead) }}" class="text-sky-600 mr-2">Lead #{{ $log->sales_lead_id }}</a>@endif
                </div>
                <span class="text-slate-500 text-xs">{{ $log->user?->name }} — {{ $log->created_at?->format('Y-m-d H:i:s') }}</span>
            </div>
        @endforeach
    </div>
    <div>{{ $logs->links() }}</div>
</div>
@endsection
