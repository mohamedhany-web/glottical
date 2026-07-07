@extends('layouts.employee')

@section('title', 'تقارير CRM')
@section('header', 'CRM — التقارير')

@section('content')
<div class="space-y-4">
  @include('partials.crm-employee-nav', ['role' => $role])

  <div class="flex flex-wrap gap-2 justify-between items-center">
    <p class="text-sm text-gray-600">ارفع تقارير أسبوعية أو شهرية للإدارة (PDF أو ملف Office).</p>
    <div class="flex gap-2">
      <a href="{{ route('employee.crm.reports.create', ['type' => 'weekly']) }}" class="px-3 py-2 rounded-lg bg-indigo-600 text-white text-sm font-bold">تقرير أسبوعي</a>
      <a href="{{ route('employee.crm.reports.create', ['type' => 'monthly']) }}" class="px-3 py-2 rounded-lg bg-violet-600 text-white text-sm font-bold">تقرير شهري</a>
      <a href="{{ route('employee.crm.reports.create', ['type' => 'ad_hoc']) }}" class="px-3 py-2 rounded-lg bg-slate-600 text-white text-sm font-bold">تقرير خاص</a>
    </div>
  </div>

  @if(session('success'))<div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm">{{ session('success') }}</div>@endif

  <div class="rounded-2xl border bg-white overflow-hidden">
    <table class="min-w-full text-sm">
      <thead class="bg-gray-50 text-xs uppercase">
        <tr>
          <th class="px-4 py-3 text-right">العنوان</th>
          <th class="px-4 py-3 text-right">النوع</th>
          <th class="px-4 py-3 text-right">الفترة</th>
          <th class="px-4 py-3 text-right">الحالة</th>
          <th class="px-4 py-3 text-right">الملف</th>
        </tr>
      </thead>
      <tbody class="divide-y">
        @forelse($reports as $report)
          <tr>
            <td class="px-4 py-3 font-semibold">{{ $report->title }}</td>
            <td class="px-4 py-3">{{ $report->type_label }}</td>
            <td class="px-4 py-3 text-gray-600">
              @if($report->period_start){{ $report->period_start->format('Y-m-d') }} — {{ $report->period_end?->format('Y-m-d') }}@else—@endif
            </td>
            <td class="px-4 py-3">{{ $report->status_label }}</td>
            <td class="px-4 py-3">
              @if($report->file_path)
                <a href="{{ route('employee.crm.reports.download', $report) }}" class="text-indigo-600 font-bold text-xs">تحميل</a>
              @else — @endif
            </td>
          </tr>
        @empty
          <tr><td colspan="5" class="px-4 py-10 text-center text-gray-500">لم تُرفع تقارير بعد</td></tr>
        @endforelse
      </tbody>
    </table>
    <div class="p-4">{{ $reports->links() }}</div>
  </div>
</div>
@endsection
