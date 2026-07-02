@extends('layouts.admin')

@section('title', 'CRM Leads')
@section('header', 'CRM — Leads')

@section('content')
<div class="space-y-4">
    <div class="flex flex-wrap gap-2 justify-between items-center">
        <a href="{{ route('admin.crm.dashboard') }}" class="text-sm text-sky-600 font-semibold">← لوحة CRM</a>
        <form method="GET" class="flex flex-wrap gap-2">
            <input type="search" name="search" value="{{ request('search') }}" placeholder="بحث..." class="rounded-lg border px-3 py-2 text-sm">
            <select name="status" class="rounded-lg border px-3 py-2 text-sm">
                <option value="">كل الحالات</option>
                @foreach($statusLabels as $val => $label)
                    <option value="{{ $val }}" @selected(request('status') === $val)>{{ $label }}</option>
                @endforeach
            </select>
            <button class="px-4 py-2 rounded-lg bg-slate-800 text-white text-sm font-bold">تصفية</button>
        </form>
    </div>

    <div class="rounded-2xl bg-white border overflow-hidden">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 text-xs uppercase text-slate-600">
                <tr>
                    <th class="px-4 py-3 text-right">#</th>
                    <th class="px-4 py-3 text-right">الاسم</th>
                    <th class="px-4 py-3 text-right">مالك التسويق</th>
                    <th class="px-4 py-3 text-right">سيلز</th>
                    <th class="px-4 py-3 text-right">الحالة</th>
                    <th class="px-4 py-3 text-right"></th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($leads as $lead)
                    <tr>
                        <td class="px-4 py-3">{{ $lead->id }}</td>
                        <td class="px-4 py-3 font-semibold">{{ $lead->name }}</td>
                        <td class="px-4 py-3">{{ $lead->marketingOwner?->name ?? '—' }}</td>
                        <td class="px-4 py-3">{{ $lead->assignedTo?->name ?? '—' }}</td>
                        <td class="px-4 py-3"><span class="px-2 py-0.5 rounded bg-slate-100 text-xs font-bold">{{ $lead->status_label }}</span></td>
                        <td class="px-4 py-3"><a href="{{ route('admin.crm.leads.show', $lead) }}" class="text-sky-600 font-bold">تفاصيل</a></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-10 text-center text-slate-500">لا توجد Leads</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($leads->hasPages())<div class="p-4">{{ $leads->links() }}</div>@endif
    </div>
</div>
@endsection
