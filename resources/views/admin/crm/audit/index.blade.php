@extends('layouts.admin')

@section('title', 'سجل المتابعة - CRM')
@section('page_title', 'سجل المتابعة')

@section('content')
@php
    $fieldClass = 'h-11 w-full rounded-xl border border-line bg-surface px-4 text-sm text-ink transition placeholder:text-muted focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
    $labelClass = 'mb-1.5 block text-xs font-medium text-muted';
@endphp

<div class="space-y-5">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div class="min-w-0">
            <p class="text-xs font-medium text-muted">المبيعات · CRM · السجل</p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-ink md:text-[28px]">سجل المتابعة</h2>
            <p class="mt-1 text-sm text-muted">سجلات للقراءة فقط — كل إجراء موثّق للشفافية ولا يُحذف</p>
        </div>
        <div class="admin-hero-actions flex flex-wrap gap-2">
            <a href="{{ route('admin.crm.leads.index') }}" class="btn-press inline-flex h-9 items-center gap-2 rounded-xl border border-line bg-surface px-4 text-sm font-medium text-ink-soft transition hover:border-accent/30 hover:text-accent">
                <i class="fas fa-users text-xs"></i>
                العملاء المحتملون
            </a>
            <a href="{{ route('admin.crm.dashboard') }}" class="btn-press inline-flex h-9 items-center gap-2 rounded-xl bg-accent px-4 text-sm font-medium text-white">
                <i class="fas fa-chart-pie text-xs"></i>
                لوحة CRM
            </a>
        </div>
    </section>

    <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
        <div class="border-b border-line px-4 py-4 sm:px-5">
            <h3 class="text-base font-semibold text-ink">تصفية السجلات</h3>
            <p class="mt-0.5 text-xs text-muted">حسب نوع العملية أو رقم العميل المحتمل</p>
        </div>
        <form method="GET" action="{{ route('admin.crm.audit.index') }}" class="grid grid-cols-1 gap-4 p-4 sm:p-5 md:grid-cols-3 md:items-end">
            <div>
                <label class="{{ $labelClass }}" for="action">نوع العملية</label>
                <select id="action" name="action" class="{{ $fieldClass }}">
                    <option value="">كل العمليات</option>
                    @foreach($actionLabels as $k => $l)
                        <option value="{{ $k }}" @selected(request('action') === $k)>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="{{ $labelClass }}" for="lead_id">رقم العميل (Lead)</label>
                <input id="lead_id" type="number" name="lead_id" value="{{ request('lead_id') }}" min="1" placeholder="مثال: 12" class="{{ $fieldClass }}">
            </div>
            <div class="flex flex-wrap gap-2">
                <button type="submit" class="btn-press inline-flex h-11 items-center gap-2 rounded-xl bg-accent px-5 text-sm font-medium text-white">
                    <i class="fas fa-filter text-xs"></i>
                    تصفية
                </button>
                @if(request()->anyFilled(['action', 'lead_id']))
                    <a href="{{ route('admin.crm.audit.index') }}" class="btn-press inline-flex h-11 items-center gap-2 rounded-xl border border-line px-5 text-sm font-medium text-ink hover:bg-canvas">
                        <i class="fas fa-times text-xs"></i>
                        مسح
                    </a>
                @endif
            </div>
        </form>
    </article>

    <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-line px-4 py-4 sm:px-5">
            <div>
                <h3 class="text-base font-semibold text-ink">الأحداث</h3>
                <p class="mt-0.5 text-xs text-muted">{{ number_format($logs->total()) }} سجل</p>
            </div>
        </div>
        <div class="divide-y divide-line">
            @forelse($logs as $log)
                <div class="flex flex-wrap items-start justify-between gap-3 px-4 py-4 sm:px-5 hover:bg-canvas/40">
                    <div class="min-w-0 space-y-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="inline-flex rounded-lg bg-accent-soft px-2.5 py-1 text-xs font-medium text-accent">{{ $log->actionLabel() }}</span>
                            @if($log->lead)
                                <a href="{{ route('admin.crm.leads.show', $log->lead) }}" class="text-sm font-semibold text-ink hover:text-accent">
                                    {{ $log->lead->name }}
                                    <span class="text-muted font-normal">#{{ $log->sales_lead_id }}</span>
                                </a>
                            @elseif($log->sales_lead_id)
                                <span class="text-sm text-muted">Lead #{{ $log->sales_lead_id }}</span>
                            @endif
                        </div>
                        <p class="text-xs text-muted">{{ $log->user?->name ?? 'نظام' }}</p>
                    </div>
                    <time class="shrink-0 text-xs tabular-nums text-muted">{{ $log->created_at?->format('Y-m-d H:i:s') }}</time>
                </div>
            @empty
                <div class="px-4 py-16 text-center sm:px-5">
                    <div class="mx-auto mb-3 inline-flex size-12 items-center justify-center rounded-2xl bg-accent-soft text-accent">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <p class="text-sm font-medium text-ink">لا توجد سجلات بعد</p>
                    <p class="mt-1 text-xs text-muted">ستظهر هنا عمليات الإنشاء والتعيين وتغيير الحالة.</p>
                </div>
            @endforelse
        </div>
        @if($logs->hasPages())
            <div class="border-t border-line px-4 py-4 sm:px-5">{{ $logs->links() }}</div>
        @endif
    </article>
</div>
@endsection
