@extends('layouts.admin')

@section('title', 'تقارير CRM')
@section('header', 'تقارير CRM')

@section('content')
<div class="space-y-4">
  @include('partials.crm-admin-nav')

  <form method="GET" class="flex flex-wrap gap-2">
    <select name="type" class="rounded-lg border px-3 py-2 text-sm">
      <option value="">كل الأنواع</option>
      @foreach($typeLabels as $k=>$l)<option value="{{ $k }}" @selected(request('type')===$k)>{{ $l }}</option>@endforeach
    </select>
    <select name="status" class="rounded-lg border px-3 py-2 text-sm">
      <option value="">كل الحالات</option>
      @foreach($statusLabels as $k=>$l)<option value="{{ $k }}" @selected(request('status')===$k)>{{ $l }}</option>@endforeach
    </select>
    <button class="px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-bold">تصفية</button>
  </form>

  <div class="rounded-2xl border bg-white overflow-hidden">
    <table class="min-w-full text-sm">
      <thead class="bg-gray-50 text-xs uppercase">
        <tr>
          <th class="px-4 py-3 text-right">الموظف</th>
          <th class="px-4 py-3 text-right">العنوان</th>
          <th class="px-4 py-3 text-right">النوع</th>
          <th class="px-4 py-3 text-right">الفترة</th>
          <th class="px-4 py-3 text-right">الحالة</th>
          <th class="px-4 py-3 text-right"></th>
        </tr>
      </thead>
      <tbody class="divide-y">
        @forelse($reports as $report)
          <tr>
            <td class="px-4 py-3">{{ $report->user?->name }}</td>
            <td class="px-4 py-3 font-semibold">{{ $report->title }}</td>
            <td class="px-4 py-3">{{ $report->type_label }}</td>
            <td class="px-4 py-3 text-gray-600">@if($report->period_start){{ $report->period_start->format('Y-m-d') }} — {{ $report->period_end?->format('Y-m-d') }}@else—@endif</td>
            <td class="px-4 py-3">{{ $report->status_label }}</td>
            <td class="px-4 py-3"><a href="{{ route('admin.crm.reports.show', $report) }}" class="text-indigo-600 font-bold text-xs">عرض</a></td>
          </tr>
        @empty
          <tr><td colspan="6" class="px-4 py-10 text-center text-gray-500">لا توجد تقارير</td></tr>
        @endforelse
      </tbody>
    </table>
    <div class="p-4">{{ $reports->links() }}</div>
  </div>
</div>
@endsection
