@extends('layouts.admin')

@section('title', 'Lead #'.$salesLead->id)
@section('header', 'CRM — Lead #'.$salesLead->id)

@section('content')
<div class="space-y-6 max-w-5xl">
    @if(session('success'))<div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="rounded-xl bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 text-sm">{{ $errors->first() }}</div>@endif

    <a href="{{ route('admin.crm.leads.index') }}" class="text-sm text-sky-600 font-semibold">← القائمة</a>

    <div class="rounded-2xl bg-white border p-6 space-y-4">
        <div class="flex justify-between gap-3">
            <h2 class="text-xl font-black">{{ $salesLead->name }}</h2>
            <span class="px-3 py-1 rounded-full bg-slate-100 text-sm font-bold">{{ $salesLead->status_label }}</span>
        </div>
        <dl class="grid sm:grid-cols-2 gap-3 text-sm">
            <div><dt class="text-slate-500">مالك التسويق (ثابت)</dt><dd class="font-bold">{{ $salesLead->marketingOwner?->name ?? '—' }}</dd></div>
            <div><dt class="text-slate-500">سيلز</dt><dd class="font-bold">{{ $salesLead->assignedTo?->name ?? '—' }}</dd></div>
            <div><dt class="text-slate-500">البريد / الهاتف</dt><dd>{{ $salesLead->email ?: '—' }} / {{ $salesLead->phone ?: '—' }}</dd></div>
            <div><dt class="text-slate-500">المصدر</dt><dd>{{ $salesLead->source_label }}</dd></div>
            <div><dt class="text-slate-500">كورس الاهتمام</dt><dd>{{ $salesLead->interestedCourse?->title ?? '—' }}</dd></div>
            <div><dt class="text-slate-500">المجموعة</dt><dd>{{ $salesLead->crmGroup?->name ?? '—' }}</dd></div>
        </dl>
        @if($salesLead->notes)<div class="rounded-lg bg-slate-50 p-3 text-sm whitespace-pre-wrap">{{ $salesLead->notes }}</div>@endif
    </div>

    @if(!$salesLead->isClosed())
    <div class="rounded-2xl bg-white border p-6 space-y-4">
        <h3 class="font-bold">تعيين لسيلز</h3>
        <form method="POST" action="{{ route('admin.crm.leads.assign', $salesLead) }}" class="grid sm:grid-cols-3 gap-3">
            @csrf
            <select name="assigned_to" class="rounded-lg border px-3 py-2 text-sm" required>
                <option value="">اختر سيلز</option>
                @foreach($salesUsers as $u)<option value="{{ $u->id }}">{{ $u->name }}</option>@endforeach
            </select>
            <select name="crm_group_id" class="rounded-lg border px-3 py-2 text-sm">
                <option value="">مجموعة (اختياري)</option>
                @foreach($groups as $g)<option value="{{ $g->id }}">{{ $g->name }}</option>@endforeach
            </select>
            <button class="px-4 py-2 rounded-xl bg-sky-600 text-white text-sm font-bold">تعيين</button>
        </form>
    </div>

    <div class="rounded-2xl bg-white border p-6 space-y-4">
        <h3 class="font-bold">تغيير الحالة</h3>
        <form method="POST" action="{{ route('admin.crm.leads.transition', $salesLead) }}" class="space-y-3">
            @csrf
            <select name="status" class="w-full rounded-lg border px-3 py-2 text-sm" required>
                @foreach(\App\Models\SalesLead::allowedTransitions()[$salesLead->status] ?? [] as $st)
                    <option value="{{ $st }}">{{ \App\Models\SalesLead::statusLabels()[$st] }}</option>
                @endforeach
            </select>
            <textarea name="note" rows="2" class="w-full rounded-lg border px-3 py-2 text-sm" placeholder="ملاحظة (اختياري)"></textarea>
            <button class="px-4 py-2 rounded-xl bg-violet-600 text-white text-sm font-bold">تحديث الحالة</button>
        </form>
    </div>
    @endif

    @if($salesLead->commissions->isNotEmpty())
    <div class="rounded-2xl bg-white border p-6">
        <h3 class="font-bold mb-3">العمولات</h3>
        <div class="space-y-2 text-sm">
            @foreach($salesLead->commissions as $c)
                <div class="flex justify-between border-b pb-2">
                    <span>{{ $c->user?->name }} — {{ $c->typeLabel() }}</span>
                    <span class="font-bold">{{ number_format($c->commission_amount_egp, 2) }} ج.م ({{ $c->status }})</span>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <div class="rounded-2xl bg-white border p-6">
        <h3 class="font-bold mb-3">سجل التدقيق</h3>
        <div class="space-y-2 max-h-64 overflow-y-auto text-xs">
            @forelse($salesLead->auditLogs as $log)
                <div class="border-b pb-2">
                    <span class="font-bold">{{ $log->actionLabel() }}</span>
                    <span class="text-slate-500"> — {{ $log->user?->name }} — {{ $log->created_at?->format('Y-m-d H:i') }}</span>
                </div>
            @empty
                <p class="text-slate-500">لا سجلات بعد</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
