@extends('layouts.employee')

@section('title', 'العملاء المحتملون')
@section('header', 'CRM — العملاء المحتملون')

@section('content')
<div class="space-y-4">
  @include('partials.crm-employee-nav', ['role' => $role])

  @if($role === 'marketing')
    <div class="flex justify-end">
      <a href="{{ route('employee.crm.leads.create') }}" class="px-4 py-2 rounded-xl bg-teal-600 text-white text-sm font-bold">+ إضافة عميل جديد</a>
    </div>
  @endif

  <form method="GET" class="flex flex-wrap gap-2">
    <select name="status" class="rounded-lg border px-3 py-2 text-sm">
      <option value="">كل الحالات</option>
      @foreach($statusLabels as $val => $label)
        <option value="{{ $val }}" @selected(request('status') === $val)>{{ $label }}</option>
      @endforeach
    </select>
    <button class="px-4 py-2 rounded-lg bg-slate-800 text-white text-sm font-bold">تصفية</button>
  </form>

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
            <td class="px-4 py-3"><a href="{{ route('employee.crm.leads.show', $lead) }}" class="text-sky-600 font-bold">عرض التفاصيل</a></td>
          </tr>
        @empty
          <tr><td colspan="3" class="px-4 py-10 text-center text-gray-500">لا يوجد عملاء محتملون في قائمتك</td></tr>
        @endforelse
      </tbody>
    </table>
    <div class="p-4">{{ $leads->links() }}</div>
  </div>
</div>
@endsection
