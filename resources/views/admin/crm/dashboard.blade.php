@extends('layouts.admin')

@section('title', 'Glottical CRM')
@section('header', 'Glottical CRM')

@section('content')
<div class="space-y-6">
    <div class="rounded-2xl bg-gradient-to-l from-indigo-600 to-violet-700 text-white p-6 shadow-lg">
        <p class="text-indigo-100 text-sm font-semibold uppercase tracking-wider">Golden Rule</p>
        <p class="text-xl font-black mt-1">IF IT IS NOT IN CRM, IT DID NOT HAPPEN.</p>
        <p class="text-indigo-100 text-sm mt-2">أي عملية غير مسجلة داخل CRM لا يُحتسب لها عمولة.</p>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
        <div class="rounded-xl bg-white border p-4"><p class="text-xs text-slate-500">كل Leads</p><p class="text-2xl font-bold">{{ $stats['total_leads'] }}</p></div>
        <div class="rounded-xl bg-white border p-4"><p class="text-xs text-slate-500">مفتوحة</p><p class="text-2xl font-bold text-sky-700">{{ $stats['open_leads'] }}</p></div>
        <div class="rounded-xl bg-white border p-4"><p class="text-xs text-amber-600">بانتظار الدفع</p><p class="text-2xl font-bold text-amber-700">{{ $stats['payment_pending'] }}</p></div>
        <div class="rounded-xl bg-white border p-4"><p class="text-xs text-emerald-600">مغلقة ناجحة</p><p class="text-2xl font-bold text-emerald-700">{{ $stats['closed_won'] }}</p></div>
        <div class="rounded-xl bg-white border p-4"><p class="text-xs text-violet-600">عمولات معلقة</p><p class="text-2xl font-bold text-violet-700">{{ $stats['commissions_pending'] }}</p></div>
        <div class="rounded-xl bg-white border p-4"><p class="text-xs text-slate-500">إجمالي عمولات</p><p class="text-xl font-bold">{{ number_format($stats['commissions_total'], 2) }}</p></div>
    </div>

    <div class="flex flex-wrap gap-2">
        <a href="{{ route('admin.crm.leads.index') }}" class="px-4 py-2 rounded-xl bg-sky-600 text-white text-sm font-bold">Leads</a>
        <a href="{{ route('admin.crm.commissions.index') }}" class="px-4 py-2 rounded-xl bg-violet-600 text-white text-sm font-bold">العمولات</a>
        <a href="{{ route('admin.crm.groups.index') }}" class="px-4 py-2 rounded-xl bg-indigo-600 text-white text-sm font-bold">المجموعات</a>
        <a href="{{ route('admin.crm.audit.index') }}" class="px-4 py-2 rounded-xl bg-slate-800 text-white text-sm font-bold">سجل التدقيق</a>
    </div>

    <div class="grid lg:grid-cols-2 gap-4">
        <div class="rounded-2xl bg-white border overflow-hidden">
            <div class="px-5 py-4 border-b font-bold">آخر Leads</div>
            <div class="divide-y">
                @foreach($recentLeads as $lead)
                    <a href="{{ route('admin.crm.leads.show', $lead) }}" class="flex justify-between px-5 py-3 hover:bg-slate-50 text-sm">
                        <span class="font-semibold">{{ $lead->name }}</span>
                        <span class="text-slate-500">{{ $lead->status_label }}</span>
                    </a>
                @endforeach
            </div>
        </div>
        <div class="rounded-2xl bg-white border p-5">
            <h3 class="font-bold mb-3">توزيع الحالات</h3>
            <div class="space-y-2 text-sm">
                @foreach(\App\Models\SalesLead::statusLabels() as $key => $label)
                    <div class="flex justify-between">
                        <span>{{ $label }}</span>
                        <span class="font-bold">{{ $statusBreakdown[$key] ?? 0 }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
