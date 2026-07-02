@extends('layouts.employee')

@section('title', 'عمولات CRM')
@section('header', 'CRM — العمولات')

@section('content')
<div class="space-y-4">
  <a href="{{ route('employee.crm.dashboard') }}" class="text-sm text-sky-600 font-bold">← CRM</a>
  <div class="rounded-2xl border bg-white overflow-hidden">
    <table class="min-w-full text-sm">
      <thead class="bg-gray-50 text-xs uppercase"><tr>
        <th class="px-4 py-3 text-right">Lead</th><th class="px-4 py-3 text-right">النوع</th><th class="px-4 py-3 text-right">المبلغ</th><th class="px-4 py-3 text-right">الحالة</th><th></th>
      </tr></thead>
      <tbody class="divide-y">
        @foreach($commissions as $c)
          <tr>
            <td class="px-4 py-3">#{{ $c->sales_lead_id }}</td>
            <td class="px-4 py-3">{{ $c->typeLabel() }}</td>
            <td class="px-4 py-3 font-bold">{{ number_format($c->commission_amount_egp, 2) }}</td>
            <td class="px-4 py-3">{{ $c->status }}</td>
            <td class="px-4 py-3">
              @if($role === 'finance' && $c->status === \App\Models\CrmCommission::STATUS_PENDING)
                <form method="POST" action="{{ route('employee.crm.commissions.approve', $c) }}">@csrf<button class="text-emerald-600 font-bold text-xs">اعتماد</button></form>
              @endif
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
    <div class="p-4">{{ $commissions->links() }}</div>
  </div>
</div>
@endsection
