@extends('layouts.employee')

@section('title', 'CRM Leads')
@section('header', 'CRM — Leads')

@section('content')
<div class="space-y-4">
  <div class="flex justify-between items-center">
    <a href="{{ route('employee.crm.dashboard') }}" class="text-sm text-sky-600 font-bold">← CRM</a>
    @if($role === 'marketing')
      <a href="{{ route('employee.crm.leads.create') }}" class="px-4 py-2 rounded-xl bg-teal-600 text-white text-sm font-bold">+ Lead</a>
    @endif
  </div>
  <div class="rounded-2xl border bg-white overflow-hidden">
    <table class="min-w-full text-sm">
      <thead class="bg-gray-50 text-xs uppercase"><tr>
        <th class="px-4 py-3 text-right">الاسم</th><th class="px-4 py-3 text-right">الحالة</th><th class="px-4 py-3 text-right"></th>
      </tr></thead>
      <tbody class="divide-y">
        @forelse($leads as $lead)
          <tr>
            <td class="px-4 py-3 font-semibold">{{ $lead->name }}</td>
            <td class="px-4 py-3">{{ $lead->status_label }}</td>
            <td class="px-4 py-3"><a href="{{ route('employee.crm.leads.show', $lead) }}" class="text-sky-600 font-bold">عرض</a></td>
          </tr>
        @empty
          <tr><td colspan="3" class="px-4 py-10 text-center text-gray-500">لا Leads</td></tr>
        @endforelse
      </tbody>
    </table>
    <div class="p-4">{{ $leads->links() }}</div>
  </div>
</div>
@endsection
