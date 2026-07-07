@extends('layouts.admin')

@section('title', 'تقرير CRM')
@section('header', $report->title)

@section('content')
<div class="space-y-4 max-w-3xl">
  @include('partials.crm-admin-nav')

  @if(session('success'))<div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm">{{ session('success') }}</div>@endif

  <div class="rounded-2xl border bg-white p-6 space-y-3 text-sm">
    <p><span class="text-gray-500">الموظف:</span> <strong>{{ $report->user?->name }}</strong></p>
    <p><span class="text-gray-500">النوع:</span> {{ $report->type_label }} — <span class="text-gray-500">الحالة:</span> {{ $report->status_label }}</p>
    @if($report->period_start)<p><span class="text-gray-500">الفترة:</span> {{ $report->period_start->format('Y-m-d') }} — {{ $report->period_end?->format('Y-m-d') }}</p>@endif
    @if($report->group)<p><span class="text-gray-500">المجموعة:</span> {{ $report->group->name }}</p>@endif
    @if($report->summary)<div class="bg-gray-50 rounded-lg p-3 whitespace-pre-wrap">{{ $report->summary }}</div>@endif
    @if($report->file_path)
      <a href="{{ route('admin.crm.reports.download', $report) }}" class="inline-flex items-center gap-2 text-indigo-600 font-bold"><i class="fas fa-download"></i> تحميل الملف</a>
    @endif
    @if($report->admin_notes)<div class="border-t pt-3 text-gray-700"><strong>ملاحظات الإدارة:</strong> {{ $report->admin_notes }}</div>@endif
  </div>

  @if($report->status !== \App\Models\CrmReport::STATUS_REVIEWED)
  <form method="POST" action="{{ route('admin.crm.reports.review', $report) }}" class="rounded-2xl border bg-white p-6 space-y-3">
    @csrf
    <h3 class="font-bold">تأكيد المراجعة</h3>
    <textarea name="admin_notes" rows="3" class="w-full rounded-lg border px-3 py-2" placeholder="ملاحظات للموظف (اختياري)"></textarea>
    <button class="px-4 py-2 rounded-xl bg-emerald-600 text-white font-bold text-sm">تمت المراجعة</button>
  </form>
  @endif
</div>
@endsection
