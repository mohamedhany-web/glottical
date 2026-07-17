@extends('layouts.admin')

@section('title', 'Pipeline CRM')
@section('header', 'لوحة مسار البيع — Pipeline')

@section('content')
<div class="space-y-6">
    @include('partials.crm-admin-nav')

    <div class="flex flex-wrap items-end justify-between gap-3">
        <div>
            <p class="text-sm text-slate-600">رقابة مباشرة على كل مراحل العملاء المحتملين في Glottical CRM.</p>
        </div>
        <div class="flex flex-wrap gap-3 text-sm">
            <span class="rounded-xl bg-white border px-4 py-2 font-bold text-slate-800">الإجمالي: {{ number_format($totalLeads) }}</span>
            <span class="rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-2 font-bold text-emerald-800">مفتوح: {{ number_format($openLeads) }}</span>
        </div>
    </div>

    <div class="flex gap-3 overflow-x-auto pb-4">
        @foreach($columns as $status => $col)
            <section class="w-72 shrink-0 rounded-2xl border bg-slate-50/80 shadow-sm overflow-hidden">
                <header class="px-3 py-3 border-b bg-white flex items-center justify-between gap-2">
                    <h2 class="text-sm font-black text-slate-900 leading-tight">{{ $col['label'] }}</h2>
                    <span class="text-xs font-bold tabular-nums rounded-full bg-indigo-100 text-indigo-800 px-2 py-0.5">{{ $col['count'] }}</span>
                </header>
                <ul class="p-2 space-y-2 max-h-[70vh] overflow-y-auto">
                    @forelse($col['leads'] as $lead)
                        <li>
                            <a href="{{ route('admin.crm.leads.show', $lead) }}" class="block rounded-xl border bg-white p-3 hover:border-indigo-300 hover:shadow-sm transition">
                                <p class="font-bold text-sm text-slate-900">{{ $lead->name }}</p>
                                <p class="text-[11px] text-slate-500 mt-1">سيلز: {{ $lead->assignedTo?->name ?? '—' }}</p>
                                <p class="text-[11px] text-slate-500">تسويق: {{ $lead->marketingOwner?->name ?? '—' }}</p>
                            </a>
                        </li>
                    @empty
                        <li class="text-center text-xs text-slate-400 py-8">لا عملاء</li>
                    @endforelse
                    @if($col['count'] > $col['leads']->count())
                        <li class="text-center text-[11px] text-indigo-700 font-semibold py-2">
                            <a href="{{ route('admin.crm.leads.index', ['status' => $status]) }}">عرض كل الـ {{ $col['count'] }}</a>
                        </li>
                    @endif
                </ul>
            </section>
        @endforeach
    </div>
</div>
@endsection
