@extends('layouts.employee')

@section('title', 'Glottical CRM')
@section('header', 'Glottical CRM')

@section('content')
<div class="space-y-6">
  @if(session('success'))<div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm">{{ session('success') }}</div>@endif

  <div class="rounded-2xl bg-gradient-to-l from-indigo-600 to-violet-700 text-white p-5">
    <p class="text-xs uppercase tracking-wider text-indigo-200">دورك: {{ $role }}</p>
    <p class="font-black text-lg mt-1">IF IT IS NOT IN CRM, IT DID NOT HAPPEN</p>
  </div>

  <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
    <div class="rounded-xl border bg-white p-4"><p class="text-xs text-gray-500">Leads</p><p class="text-2xl font-black">{{ $stats['my_leads'] }}</p></div>
    <div class="rounded-xl border bg-white p-4"><p class="text-xs text-gray-500">مفتوحة</p><p class="text-2xl font-black text-sky-700">{{ $stats['open'] }}</p></div>
    <div class="rounded-xl border bg-white p-4"><p class="text-xs text-gray-500">ناجحة</p><p class="text-2xl font-black text-emerald-700">{{ $stats['closed_won'] }}</p></div>
    <div class="rounded-xl border bg-white p-4"><p class="text-xs text-gray-500">بانتظار الدفع</p><p class="text-2xl font-black text-amber-700">{{ $stats['payment_pending'] }}</p></div>
  </div>

  <div class="flex flex-wrap gap-2">
    <a href="{{ route('employee.crm.leads.index') }}" class="px-4 py-2 rounded-xl bg-sky-600 text-white text-sm font-bold">Leads</a>
    @if($role === 'marketing')
      <a href="{{ route('employee.crm.leads.create') }}" class="px-4 py-2 rounded-xl bg-teal-600 text-white text-sm font-bold">Lead جديد</a>
    @endif
    <a href="{{ route('employee.crm.commissions') }}" class="px-4 py-2 rounded-xl bg-violet-600 text-white text-sm font-bold">عمولاتي</a>
  </div>

  <div class="rounded-2xl border bg-white overflow-hidden">
    <div class="px-5 py-3 border-b font-bold">آخر Leads</div>
    <ul class="divide-y">
      @foreach($recentLeads as $lead)
        <li><a href="{{ route('employee.crm.leads.show', $lead) }}" class="flex justify-between px-5 py-3 hover:bg-gray-50 text-sm">
          <span class="font-semibold">{{ $lead->name }}</span><span class="text-gray-500">{{ $lead->status_label }}</span>
        </a></li>
      @endforeach
    </ul>
  </div>
</div>
@endsection
