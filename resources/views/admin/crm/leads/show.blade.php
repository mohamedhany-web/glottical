@extends('layouts.admin')

@section('title', 'عميل محتمل #'.$salesLead->id)
@section('header', 'CRM — '.$salesLead->name)

@section('content')
<div class="space-y-6 max-w-5xl">
    @include('partials.crm-admin-nav')

    @if(session('success'))<div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="rounded-xl bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 text-sm">{{ $errors->first() }}</div>@endif

    <div class="rounded-2xl bg-white border p-6 space-y-4">
        <div class="flex justify-between gap-3">
            <h2 class="text-xl font-black">{{ $salesLead->name }}</h2>
            <span class="px-3 py-1 rounded-full bg-slate-100 text-sm font-bold">{{ $salesLead->status_label }}</span>
        </div>
        <dl class="grid sm:grid-cols-2 gap-3 text-sm">
            <div><dt class="text-slate-500">مالك التسويق (ثابت)</dt><dd class="font-bold">{{ $salesLead->marketingOwner?->name ?? '—' }}</dd></div>
            <div><dt class="text-slate-500">موظف المبيعات</dt><dd class="font-bold">{{ $salesLead->assignedTo?->name ?? '—' }}</dd></div>
            <div><dt class="text-slate-500">البريد / الهاتف</dt><dd>{{ $salesLead->email ?: '—' }} / {{ $salesLead->phone ?: '—' }}</dd></div>
            <div><dt class="text-slate-500">المصدر</dt><dd>{{ $salesLead->source_label }}</dd></div>
            <div><dt class="text-slate-500">كورس الاهتمام</dt><dd>{{ $salesLead->interestedCourse?->title ?? '—' }}</dd></div>
            <div><dt class="text-slate-500">مجموعة الفريق</dt><dd>{{ $salesLead->crmGroup?->name ?? '—' }}</dd></div>
        </dl>
        @if($salesLead->notes)<div class="rounded-lg bg-slate-50 p-3 text-sm whitespace-pre-wrap">{{ $salesLead->notes }}</div>@endif
    </div>

    @include('partials.crm-pipeline-strip', ['pipelineLead' => $salesLead])

    @if(!$salesLead->isClosed())
    <div class="rounded-2xl bg-white border p-6 space-y-4">
        <h3 class="font-bold">تعيين لموظف مبيعات</h3>
        <p class="text-sm text-slate-500">بعد التعيين تصبح الحالة «مُعيَّن للمبيعات» — الموظف يبدأ المتابعة من قائمته.</p>
        <form method="POST" action="{{ route('admin.crm.leads.assign', $salesLead) }}" class="grid sm:grid-cols-3 gap-3">
            @csrf
            <select name="assigned_to" class="rounded-lg border px-3 py-2 text-sm" required>
                <option value="">اختر موظف مبيعات</option>
                @foreach($salesUsers as $u)<option value="{{ $u->id }}" @selected($salesLead->assigned_to==$u->id)>{{ $u->name }}</option>@endforeach
            </select>
            <select name="crm_group_id" class="rounded-lg border px-3 py-2 text-sm">
                <option value="">فريق العمل (اختياري)</option>
                @foreach($groups as $g)<option value="{{ $g->id }}" @selected($salesLead->crm_group_id==$g->id)>{{ $g->name }}</option>@endforeach
            </select>
            <button class="px-4 py-2 rounded-xl bg-sky-600 text-white text-sm font-bold">تعيين</button>
        </form>
    </div>
    @endif

    <div class="rounded-2xl bg-white border p-6 space-y-4">
        <h3 class="font-bold">تغيير الحالة (رقابة الإدارة)</h3>
        <p class="text-sm text-slate-500">اختر أي مرحلة لاحقة مسموحة، أو فعّل «فرض أي حالة» لإعادة فتح عميل مغلق أو الرجوع لمرحلة سابقة.</p>
        <form method="POST" action="{{ route('admin.crm.leads.transition', $salesLead) }}" class="space-y-3">
            @csrf
            <select name="status" class="w-full rounded-lg border px-3 py-2 text-sm" required>
                <option value="">اختر الحالة</option>
                <optgroup label="انتقالات مسموحة من الحالة الحالية">
                    @forelse($nextStatuses ?? [] as $st)
                        <option value="{{ $st }}">{{ \App\Models\SalesLead::statusLabels()[$st] ?? $st }}</option>
                    @empty
                        <option value="" disabled>لا انتقالات عادية (مغلق أو نهاية المسار)</option>
                    @endforelse
                </optgroup>
                <optgroup label="كل الحالات (استخدم مع فرض الحالة عند الحاجة)">
                    @foreach(\App\Models\SalesLead::statusLabels() as $st => $label)
                        @if($st !== $salesLead->status)
                            <option value="{{ $st }}">{{ $label }}</option>
                        @endif
                    @endforeach
                </optgroup>
            </select>
            <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                <input type="checkbox" name="force" value="1" class="rounded border-slate-300 text-violet-600 focus:ring-violet-500">
                فرض أي حالة (صلاحية الإدارة — يُسجَّل في المتابعة)
            </label>
            <textarea name="note" rows="2" class="w-full rounded-lg border px-3 py-2 text-sm" placeholder="ملاحظة / سبب التغيير (موصى به)"></textarea>
            <button class="px-4 py-2 rounded-xl bg-violet-600 text-white text-sm font-bold">تحديث الحالة</button>
        </form>
    </div>

    @if($salesLead->commissions->isNotEmpty())
    <div class="rounded-2xl bg-white border p-6">
        <h3 class="font-bold mb-3">العمولات</h3>
        <div class="space-y-2 text-sm">
            @foreach($salesLead->commissions as $c)
                <div class="flex justify-between border-b pb-2">
                    <span>{{ $c->user?->name }} — {{ $c->typeLabel() }}</span>
                    <span class="font-bold">{{ number_format($c->commission_amount_egp, 2) }} ج.م ({{ $c->statusLabel() }})</span>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <div class="rounded-2xl bg-white border p-6">
        <h3 class="font-bold mb-3">سجل المتابعة</h3>
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
