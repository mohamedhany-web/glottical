@extends('layouts.employee')

@section('title', 'رفع تقرير CRM')
@section('header', 'CRM — رفع تقرير')

@section('content')
<div class="space-y-4 max-w-2xl">
  @include('partials.crm-employee-nav', ['role' => $role])

  <form method="POST" action="{{ route('employee.crm.reports.store') }}" enctype="multipart/form-data" class="rounded-2xl border bg-white p-6 space-y-4">
    @csrf
    <input type="hidden" name="type" value="{{ $type }}">

    <div>
      <label class="block text-sm font-bold mb-1">نوع التقرير</label>
      <p class="text-sm text-gray-600">{{ $typeLabels[$type] ?? $type }}</p>
    </div>

    <div>
      <label class="block text-sm font-bold mb-1">عنوان التقرير *</label>
      <input name="title" value="{{ old('title') }}" class="w-full rounded-lg border px-3 py-2" required placeholder="مثال: تقرير أسبوع 24 — فريق التسويق">
      @error('title')<p class="text-rose-600 text-xs mt-1">{{ $message }}</p>@enderror
    </div>

    @if(in_array($type, ['weekly', 'monthly'], true))
    <div class="grid grid-cols-2 gap-3">
      <div>
        <label class="block text-sm font-bold mb-1">من تاريخ *</label>
        <input type="date" name="period_start" value="{{ old('period_start', $period['start']) }}" class="w-full rounded-lg border px-3 py-2" required>
      </div>
      <div>
        <label class="block text-sm font-bold mb-1">إلى تاريخ *</label>
        <input type="date" name="period_end" value="{{ old('period_end', $period['end']) }}" class="w-full rounded-lg border px-3 py-2" required>
      </div>
    </div>
    @endif

    @if($groups->isNotEmpty())
    <div>
      <label class="block text-sm font-bold mb-1">المجموعة (اختياري)</label>
      <select name="crm_group_id" class="w-full rounded-lg border px-3 py-2">
        <option value="">—</option>
        @foreach($groups as $g)<option value="{{ $g->id }}" @selected(old('crm_group_id')==$g->id)>{{ $g->name }}</option>@endforeach
      </select>
    </div>
    @endif

    <div>
      <label class="block text-sm font-bold mb-1">ملخص التقرير</label>
      <textarea name="summary" rows="4" class="w-full rounded-lg border px-3 py-2" placeholder="أبرز الإنجازات والتحديات">{{ old('summary') }}</textarea>
    </div>

    <div>
      <label class="block text-sm font-bold mb-1">ملف التقرير (PDF / Word / Excel)</label>
      <input type="file" name="file" accept=".pdf,.doc,.docx,.xls,.xlsx" class="w-full text-sm">
    </div>

    <button class="px-5 py-2.5 rounded-xl bg-indigo-600 text-white font-bold text-sm">إرسال للإدارة</button>
  </form>
</div>
@endsection
